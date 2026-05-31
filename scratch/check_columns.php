<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

$treatmentCols = DB::select("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'treatments'");
echo "Columns of 'treatments':\n";
foreach ($treatmentCols as $c) {
    echo "- {$c->column_name} ({$c->data_type})\n";
}

$detailCols = DB::select("SELECT column_name, data_type FROM information_schema.columns WHERE table_name = 'treatment_details'");
echo "\nColumns of 'treatment_details':\n";
foreach ($detailCols as $c) {
    echo "- {$c->column_name} ({$c->data_type})\n";
}
