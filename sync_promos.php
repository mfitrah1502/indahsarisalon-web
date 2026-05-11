<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Treatment;

$promos = DB::table('promos')->get();
$count = 0;

foreach ($promos as $promo) {
    $treatment = Treatment::where('name', $promo->title)->first();
    if ($treatment) {
        $treatment->promo_start_date = $promo->start_at;
        $treatment->promo_end_date = $promo->end_at;
        $treatment->is_promo = true;
        $treatment->save();
        $count++;
        echo "Updated treatment: {$treatment->name}\n";
    }
}

echo "Total updated: $count\n";
