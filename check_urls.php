<?php
$urls = [
    'https://acwubkiohkqaedhwrvpr.supabase.co/storage/v1/object/public/treatments/services/svc_1778514841893.png',
    'https://acwubkiohkqaedhwrvpr.supabase.co/storage/v1/object/public/treatments/services/svc_1778514854556.png',
    'https://acwubkiohkqaedhwrvpr.supabase.co/storage/v1/object/public/treatments/services/svc_1779106383703.jpg'
];

foreach ($urls as $url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    echo "$url => HTTP CODE: $code\n";
}
