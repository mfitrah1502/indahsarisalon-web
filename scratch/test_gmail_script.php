<?php

// Test GET request ke Google Apps Script
$params = http_build_query([
    'to'       => 'joozepeto@gmail.com',
    'subject'  => 'Test OTP - Indah Sari Salon',
    'htmlBody' => '<h2>Kode OTP Anda: <strong>123456</strong></h2>',
    'token'    => 'indahsari123',
]);

$url = 'https://script.google.com/macros/s/AKfycbwtLvoGSbYFyvr9d7p00pLBYHT3aCDhusjLOjiPgWvBFCjDQCQruZC0xI7x-CgiMuobzg/exec?' . $params;

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_TIMEOUT        => 30,
    CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err      = curl_error($ch);
curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Response: $response\n";
if ($err) echo "cURL Error: $err\n";
