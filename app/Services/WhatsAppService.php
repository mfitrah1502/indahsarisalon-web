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
            $postData = [
                'target' => $to,
                'message' => $message ?: ' ', // Cegah pesan benar-benar kosong
            ];

            if ($url) {
                $postData['url'] = $url;
            }

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $apiUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($postData), // Gunakan JSON Murni
                CURLOPT_HTTPHEADER => [
                    "Authorization: $apiKey",
                    "Content-Type: application/json" // Beri tahu Fonnte bahwa ini JSON
                ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                Log::error("WhatsApp cURL Error #:" . $err);
                return false;
            } else {
                Log::info("WA API Response: " . $response);
                $respData = json_decode($response, true);
                return isset($respData['status']) && $respData['status'] == true;
            }
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
