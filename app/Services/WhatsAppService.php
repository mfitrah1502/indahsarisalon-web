<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send a WhatsApp message.
     * This is a generic implementation that can be adapted to any provider (Fozzil, Wablas, Starsender, etc.)
     */
    public static function sendMessage($to, $message)
    {
        // Pastikan nomor diawali dengan kode negara (62 for ID)
        $to = self::formatNumber($to);

        // Jika API Key tidak ada di .env, kita hanya log saja
        $apiKey = env('WA_API_KEY');
        $sender = env('WA_SENDER_NUMBER');

        if (!$apiKey) {
            Log::info("WhatsApp Message would be sent to $to: $message");
            return false;
        }

        try {
            // Contoh implementasi untuk generic provider (misal Fozzil/Starsender)
            $response = Http::post(env('WA_API_URL', 'https://api.whatsapp-gateway.com/send'), [
                'api_key' => $apiKey,
                'sender'  => $sender,
                'number'  => $to,
                'message' => $message
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Failed to send WA to $to: " . $e->getMessage());
            return false;
        }
    }

    private static function formatNumber($number)
    {
        $number = preg_replace('/[^0-9]/', '', $number);
        if (str_starts_with($number, '0')) {
            $number = '62' . substr($number, 1);
        } elseif (!str_starts_with($number, '62')) {
            $number = '62' . $number;
        }
        return $number;
    }
}
