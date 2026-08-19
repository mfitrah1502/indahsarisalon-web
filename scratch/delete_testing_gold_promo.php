<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

$deleted = DB::table('promos')->where('title', 'Testing Gold')->delete();
echo "Deleted {$deleted} rows from promos table for 'Testing Gold'.\n";
