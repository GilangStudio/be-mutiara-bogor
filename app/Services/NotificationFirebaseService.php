<?php

namespace App\Services;

use Google\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class NotificationFirebaseService
{
    private $projectId;
    private $serviceAccountPath;
    private $fcmUrl;

    public function __construct()
    {
        $this->projectId = config('services.firebase.project_id');
        $this->serviceAccountPath = config('services.firebase.service_account_path');
        $this->fcmUrl = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";
    }

    /**
     * Get access token menggunakan service account
     */
    private function getAccessToken()
    {
        try {
            $serviceAccountData = $this->getServiceAccountData();
            
            if (!$serviceAccountData) {
                Log::error('Service account data tidak tersedia');
                return null;
            }

            $client = new Client();
            $client->setAuthConfig($serviceAccountData);
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            
            $accessToken = $client->fetchAccessTokenWithAssertion();
            
            if (isset($accessToken['access_token'])) {
                return $accessToken['access_token'];
            }
            
            Log::error('Failed to get FCM access token', ['response' => $accessToken]);
            return null;
            
        } catch (\Exception $e) {
            Log::error('Exception saat mendapatkan FCM access token', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Get service account data dari file atau environment
     */
    private function getServiceAccountData()
    {
        // Priority 2: Dari file (untuk development)
        if ($this->serviceAccountPath && Storage::exists($this->serviceAccountPath)) {
            $serviceAccountJson = Storage::get($this->serviceAccountPath);
            return json_decode($serviceAccountJson, true);
        }
        
        // Priority 3: Dari path absolut (fallback)
        $absolutePath = storage_path('app/' . $this->serviceAccountPath);
        if (file_exists($absolutePath)) {
            $serviceAccountJson = file_get_contents($absolutePath);
            return json_decode($serviceAccountJson, true);
        }
        
        return null;
    }

    /**
     * Send Firebase notification menggunakan FCM v1 API
     */
    public function sendToSales($sales, $title, $body, $data = [])
    {
        try {
            if (!$sales->fcm_token) {
                Log::info('Sales tidak memiliki FCM token', ['sales_id' => $sales->id]);
                return false;
            }

            if (!$this->projectId) {
                Log::warning('Firebase project ID tidak dikonfigurasi');
                return false;
            }

            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                Log::error('Tidak bisa mendapatkan access token untuk FCM');
                return false;
            }

            // Format message sesuai FCM v1 API
            $message = [
                'message' => [
                    'token' => $sales->fcm_token,
                    'notification' => [
                        'title' => $title,
                        'body' => $body,
                    ],
                    'data' => $data,
                    'android' => [
                        'priority' => 'high',
                        'notification' => [
                            'sound' => 'default',
                            'channel_id' => 'default_channel',
                            // 'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
                        ]
                    ],
                    'apns' => [
                        'payload' => [
                            'aps' => [
                                'sound' => 'default',
                                'badge' => 1,
                                'content-available' => 1,
                                // 'category' => 'FLUTTER_NOTIFICATION_CLICK'
                            ]
                        ]
                    ]
                ]
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $accessToken,
                'Content-Type' => 'application/json'
            ])->post($this->fcmUrl, $message);

            if ($response->successful()) {
                Log::info('Firebase notification berhasil dikirim', [
                    'sales_id' => $sales->id,
                    'sales_name' => $sales->name,
                    'title' => $title,
                    'response' => $response->json()
                ]);
                return true;
            } else {
                $responseBody = $response->json();
                
                // Handle specific FCM errors
                if (isset($responseBody['error']['details'])) {
                    foreach ($responseBody['error']['details'] as $detail) {
                        if (isset($detail['errorCode']) && $detail['errorCode'] === 'UNREGISTERED') {
                            Log::warning('FCM token tidak terdaftar, menghapus token dari sales', [
                                'sales_id' => $sales->id,
                                'token' => substr($sales->fcm_token, 0, 20) . '...' // Log partial token untuk security
                            ]);
                            
                            // Hapus FCM token yang tidak valid
                            $sales->update(['fcm_token' => null]);
                            return false;
                        }
                    }
                }
                
                Log::error('Firebase notification gagal dikirim', [
                    'sales_id' => $sales->id,
                    'response' => $responseBody,
                    'status' => $response->status()
                ]);
                return false;
            }

        } catch (\Exception $e) {
            Log::error('Exception saat mengirim Firebase notification', [
                'sales_id' => $sales->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Test connection ke FCM
     */
    public function testConnection()
    {
        try {
            $serviceAccountData = $this->getServiceAccountData();
            
            if (!$serviceAccountData) {
                return [
                    'success' => false,
                    'message' => 'Service account data tidak ditemukan. Periksa file service-account.json atau environment variable.'
                ];
            }

            $accessToken = $this->getAccessToken();
            
            if ($accessToken) {
                Log::info('FCM connection test berhasil', [
                    'project_id' => $this->projectId,
                    'service_account_email' => $serviceAccountData['client_email'] ?? 'unknown'
                ]);
                
                return [
                    'success' => true,
                    'message' => 'FCM connection berhasil',
                    'project_id' => $this->projectId,
                    'service_account_email' => $serviceAccountData['client_email'] ?? 'unknown'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Tidak bisa mendapatkan access token'
                ];
            }
            
        } catch (\Exception $e) {
            Log::error('FCM connection test exception', [
                'error' => $e->getMessage()
            ]);
            
            return [
                'success' => false,
                'message' => 'Connection test gagal: ' . $e->getMessage()
            ];
        }
    }

    // Method notifikasi lainnya...
    public function sendNewLeadNotification($sales, $lead)
    {
        $title = 'Lead Baru Masuk! 🎯';
        $body = "Hai {$sales->name}, ada lead baru dari {$lead->name}. Segera follow up ya!";
        
        $data = [
            'type' => 'new_lead',
            'lead_id' => (string) $lead->id,
            'lead_name' => $lead->name,
            'lead_phone' => $lead->phone ?? '',
            'lead_platform' => $lead->platform->platform_name ?? '',
            'timestamp' => now()->toISOString(),
            'action' => 'open_lead_detail'
        ];

        return $this->sendToSales($sales, $title, $body, $data);
    }

    public function sendStatusChangeNotification($sales, $lead, $oldStatus, $newStatus)
    {
        $statusText = [
            'NEW' => 'Baru',
            'PROCESS' => 'Proses',
            'CLOSING' => 'Closing'
        ];

        $oldStatusText = $statusText[$oldStatus] ?? $oldStatus;
        $newStatusText = $statusText[$newStatus] ?? $newStatus;

        $title = 'Status Lead Berubah 📊';
        $body = "Lead {$lead->name} berubah dari {$oldStatusText} ke {$newStatusText}";
        
        $data = [
            'type' => 'status_change',
            'lead_id' => (string) $lead->id,
            'lead_name' => $lead->name,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'old_status_text' => $oldStatusText,
            'new_status_text' => $newStatusText,
            'timestamp' => now()->toISOString(),
            'action' => 'open_lead_detail'
        ];

        return $this->sendToSales($sales, $title, $body, $data);
    }

    public function sendLeadAssignmentNotification($sales, $lead, $isReassignment = false)
    {
        // if ($isReassignment) {
            $title = 'Lead Dipindahkan 🔄';
            $body = "Lead {$lead->name} telah dipindahkan ke Anda. Jangan lupa follow up!";
        // } else {
        //     $title = 'Lead Baru Ditugaskan 📝';
        //     $body = "Lead {$lead->name} telah ditugaskan ke Anda. Selamat bekerja!";
        // }
        
        $data = [
            'type' => 'lead_assignment',
            'lead_id' => (string) $lead->id,
            'lead_name' => $lead->name,
            'lead_phone' => $lead->phone ?? '',
            'lead_platform' => $lead->platform->platform_name ?? '',
            'assignment_type' => $isReassignment ? 'reassignment' : 'new_assignment',
            'timestamp' => now()->toISOString(),
            'action' => 'open_lead_detail'
        ];

        return $this->sendToSales($sales, $title, $body, $data);
    }
}