<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

$details = DB::table('treatment_details')->where('treatment_id', 65)->get();
echo "Found " . count($details) . " details for treatment 65:\n";
foreach ($details as $d) {
    echo "ID: {$d->id} | Name: '{$d->name}' | Price: {$d->price}\n";
}
