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
    public static function sendMessage($to, $message, $url = null)
    {
        // Pastikan nomor diawali dengan kode negara (62 for ID)
        $to = self::formatNumber($to);

        // Jika API Key tidak ada di .env, kita hanya log saja
        $apiKey = env('WA_API_KEY');
        $apiUrl = env('WA_API_URL', 'https://api.fonnte.com/send');


        if (!$apiKey) {
            Log::info("WhatsApp Message would be sent to $to: $message" . ($url ? " with image: $url" : ""));
            return false;
        }

        try {
            // Persiapkan data pengiriman
            $data = [
                'target' => $to,
                'message' => $message,
            ];

            // Tambahkan URL jika ada gambar/file
            if ($url) {
                $data['url'] = $url;
            }

            // Gunakan format yang sudah terbukti berhasil di tes manual
            $response = Http::withHeaders([
                'Authorization' => $apiKey,
            ])->asForm()->post($apiUrl, $data);

            Log::info("WA API Response: " . $response->body());
            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Failed to send WA to $to: " . $e->getMessage());
            return false;
        }
    }

    private static function formatNumber($number)
    {
        // Hilangkan karakter non-digit
        $number = preg_replace('/[^0-9]/', '', $number);

        // Jika diawali '0', ganti dengan '62'
        if (strpos($number, '0') === 0) {
            $number = '62' . substr($number, 1);
        }
        
        // Jika diawali '+', (sudah hilang oleh preg_replace)
        // Pastikan tidak ada duplikasi '6262'
        if (strpos($number, '6262') === 0) {
            $number = substr($number, 2);
        }

        return $number;
    }
}
