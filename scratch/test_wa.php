<?php
require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$token = $_ENV['WA_API_KEY'];
$url = 'https://api.fonnte.com/send';
$target = '6282227097005'; // Ganti dengan nomor Anda untuk tes
$message = 'Tes Koneksi WhatsApp Salon';

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => $url,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => array(
    'target' => $target,
    'message' => 'Tes Gambar (Parameter FILE)',
    'file' => 'https://via.placeholder.com/300/09f/fff.png'
  ),
  CURLOPT_HTTPHEADER => array(
    "Authorization: $token"
  ),
));

$response = curl_exec($curl);
curl_close($curl);
echo "Response: " . $response . "\n";
echo "Using Token: " . substr($token, 0, 5) . "...\n";
