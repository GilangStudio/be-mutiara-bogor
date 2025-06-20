<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login()
    {
        return view('login');
    }

   // process login
   public function loginProcess(Request $request)
   {
       $request->validate([
           'username' => 'required',
           'password' => 'required',
       ]);

       if (Auth::attempt(['username' => $request->username, 'password' => $request->password, 'role' => 'superadmin'])) {
           return redirect()->route('dashboard');
       }

       return redirect()->back()->with('error', 'Username or password is incorrect');
   }

   public function logout()
   {
       Auth::logout();
       
       return redirect()->route('login');
   }
}
