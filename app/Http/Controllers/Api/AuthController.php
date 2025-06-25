<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Sales;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller {
    public function login(Request $request) {
        $request->validate([
            'phone' => 'required',
            'password' => 'required',
        ]);

        //verify login then save token to database
        $sales = Sales::select('id', 'user_id', 'name', 'phone', 'email','api_token')->where('phone', $request->phone)->where('is_active', 1)->first();
       
        if ($sales) {
            $user = User::where('id', $sales->user_id)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                // Authentication successful
                // $token = $user->createToken('authToken')->plainTextToken;
                //generate api token random string
                $token = Str::random(60);

                if ($request->token) {
                    //check fcm_token if same with $request->token in sales if exist set to null
                    Sales::where('fcm_token', $request->token)->update(['fcm_token' => null]);
                }

                $sales->update([
                    'api_token' => $token,
                    'fcm_token' => $request->token
                ]);
                //remove key value updated_at in sales table object
                unset($sales->updated_at);
                unset($sales->fcm_token);
                return response()->json([
                    'status' => 'success',
                    'data' => $sales
                ]);
            }
            else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Phone number or password is incorrect'
                ], 401);
            }
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'Phone number or password is incorrect'
            ], 401);
        }
    }

    public function logout(Request $request) {
        $sales = Sales::where('api_token', $request->bearerToken())->whereNotNull('api_token')->active()->first();
        $sales->update(['api_token' => null, 'fcm_token' => null]);

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out'
        ]);
    }

    public function user(Request $request) {
        $sales = Sales::select('id', 'user_id', 'name', 'phone', 'email','api_token')->where('api_token', $request->bearerToken())->whereNotNull('api_token')->active()->first();
        return response()->json([
            'status' => 'success',
            'data' => $sales
        ]);
    }

    public function update_token(Request $request) {
        $sales = Sales::where('api_token', request()->bearerToken())->whereNotNull('api_token')->active()->first();
        $sales->update(['fcm_token' => $request->token]);
        return response()->json([
            'status' => 'success'
        ]);
    }

    public function send_otp(Request $request) {
        $ipAddress = $request->ip();

        // Cek apakah pengguna sudah mencapai batas maksimum percobaan atau tidak
        if (RateLimiter::tooManyAttempts('otp-request:'.$ipAddress, 1)) {
            // Hitung waktu yang tersisa sebelum pengguna dapat mencoba lagi
            $secondsUntilAvailable = RateLimiter::availableIn('otp-request:'.$ipAddress);

            return response()->json([
                'status' => 'error',
                'message' => 'Too many OTP requests. Please try again in '.$secondsUntilAvailable.' seconds.',
            ], 429);
        }

        $request->validate([
            'email' => 'required',
        ]);

        $sales = Sales::where('sales_email', strtolower($request->email))->active()->first();

        if ($sales) {
            //generate otp random number between 1000 and 9999
            $otp = rand(1000, 9999);
            $send = EmailService::send(
                to: $sales->sales_email,
                subject: 'OTP Code PMM Sales',
                view: 'email.otp',
                data: [
                    'otp_code' => $otp
                ]
            );

            if ($send) {
                RateLimiter::hit('otp-request:'.$ipAddress);

                $sales->update(['otp_code' => $otp]);
                return response()->json([
                    'status' => 'success',
                    'message' => 'OTP sent successfully'
                ]);
            }
            else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to send OTP, please try again'
                ]);
            }
            
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'Email not found'
            ]);
        }
    }

    public function verify_otp(Request $request) {
        $ipAddress = $request->ip();

        // Cek apakah pengguna sudah mencapai batas maksimum percobaan atau tidak
        if (RateLimiter::tooManyAttempts('otp-verify:'.$ipAddress, 5)) {
            // Hitung waktu yang tersisa sebelum pengguna dapat mencoba lagi
            $secondsUntilAvailable = RateLimiter::availableIn('otp-verify:'.$ipAddress);

            return response()->json([
                'status' => 'error',
                'message' => 'Too many OTP verification attempts. Please try again in '.$secondsUntilAvailable.' seconds.',
            ]);
        }

        $request->validate([
            'email' => 'required',
            'otp_code' => 'required',
        ]);

        $sales = Sales::where('sales_email', strtolower($request->email))->where('otp_code', $request->otp_code)->active()->first();

        if ($sales) {
            $token = Str::random(60);
            $sales->update([
                'api_token' => $token,
                'otp_code' => null
            ]);

            // Reset percobaan
            RateLimiter::clear('otp-verify:'.$ipAddress);

            return response()->json([
                'status' => 'success',
                'token' => $token,
                'message' => 'OTP verified successfully'
            ]);
        }
        else {
            // Tambahkan hitungan percobaan gagal untuk alamat IP
            RateLimiter::hit('otp-verify:'.$ipAddress);

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid OTP code'
            ]);
        }
    }

    public function update_password(Request $request) {
        $request->validate([
            'current_password' =>'required',
            'new_password' => 'required',
        ]);

        $sales = Sales::where('api_token', request()->bearerToken())->whereNotNull('api_token')->active()->first();
        if ($sales) {
            // $sales->update(['password' => Hash::make($request->new_password)]);

            if (!Hash::check($request->current_password, $sales->user->password)) {
                return response()->json([
                   'status' => 'error',
                   'message' => 'Current password is incorrect'
                ]);
            }

            $sales->update(['api_token' => null, 'fcm_token' => null]);
            User::where('id', $sales->user_id)->update([
                'password' => Hash::make($request->new_password)
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Password updated successfully'
            ]);
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ]);
        }
    }

    public function update(Request $request) {
        try {
            // Mendapatkan sales dari bearer token
            $sales = Sales::where('api_token', request()->bearerToken())->first();
            
            if (!$sales) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid token or sales not found',
                    'data' => null
                ], 401);
            }

            // Validasi input
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'phone' => 'required|string|min:10|max:15|regex:/^([0-9\s\-\+\(\)]*)$/|unique:sales,phone,' . $sales->id,
                'email' => 'required|email|max:255|unique:sales,email,' . $sales->id
            ], [
                'name.required' => 'Name is required',
                'name.max' => 'Name cannot exceed 255 characters',
                'phone.required' => 'Phone number is required',
                'phone.min' => 'Phone number minimum 10 digits',
                'phone.max' => 'Phone number maximum 15 characters',
                'phone.regex' => 'Invalid phone number format',
                'phone.unique' => 'Phone number already used by another sales',
                'email.required' => 'Email is required',
                'email.email' => 'Invalid email format',
                'email.unique' => 'Email already used by another sales'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid data',
                    'data' => [
                        'errors' => $validator->errors()
                    ]
                ], 422);
            }

            // Update data sales
            $salesUpdateData = [
                'name' => $request->name,
                'phone' => $request->phone,
                'email' => $request->email,
                'updated_by' => $sales->user_id
            ];

            $sales->update($salesUpdateData);

            // Update data user
            $userUpdateData = [
                'name' => $request->name,
                'email' => $request->email
            ];

            $sales->user->update($userUpdateData);

            unset($sales->updated_at);
            unset($sales->fcm_token);

            return response()->json([
                'status' => 'success',
                'message' => 'Profile updated successfully',
                'data' => $sales
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating profile',
                'data' => null
            ], 500);
        }
    }
}

