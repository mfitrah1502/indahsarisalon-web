<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = \App\Models\User::take(5)->get(['id', 'name', 'role']);
foreach ($users as $u) {
    echo "ID: {$u->id}, Name: {$u->name}, Role: {$u->role}\n";
}
