<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$user = new \App\Models\User();
$user->incrementing = false;
$user->keyType = 'string';
$user->id = 'guest-178';
echo "Raw ID with overrides: " . $user->id . "\n";
echo "Type: " . gettype($user->id) . "\n";
