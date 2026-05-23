<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

$d = \App\Models\TreatmentDetail::with('treatment.category')->find(86);
echo "TreatmentDetail ID 86:\n";
print_r($d->toArray());
