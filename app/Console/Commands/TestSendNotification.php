<?php

namespace App\Console\Commands;

use App\Models\Sales;
use Illuminate\Console\Command;
use App\Services\NotificationFirebaseService;

class TestSendNotification extends Command
{
    protected $signature = 'firebase:send-test {sales_id}';
    protected $description = 'Test kirim notifikasi ke sales tertentu';

    public function handle(NotificationFirebaseService $firebaseService)
    {
        $salesId = $this->argument('sales_id');
        
        $sales = Sales::find($salesId);
        
        if (!$sales) {
            $this->error("❌ Sales dengan ID {$salesId} tidak ditemukan");
            return Command::FAILURE;
        }
        
        if (!$sales->fcm_token) {
            $this->error("❌ Sales {$sales->name} tidak memiliki FCM token");
            return Command::FAILURE;
        }
        
        $this->info("🔄 Mengirim test notification ke {$sales->name}...");
        
        $title = 'Test Notification 🧪';
        $body = "Hai {$sales->name}, ini adalah test notification dari sistem CRM.";
        $data = [
            'type' => 'test',
            'timestamp' => now()->toISOString()
        ];
        
        $result = $firebaseService->sendToSales($sales, $title, $body, $data);
        
        if ($result) {
            $this->info("✅ Test notification berhasil dikirim ke {$sales->name}");
            return Command::SUCCESS;
        } else {
            $this->error("❌ Gagal mengirim test notification");
            return Command::FAILURE;
        }
    }
}