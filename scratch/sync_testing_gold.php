<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

// Fetch Testing Gold promo
$promo = DB::table('promos')->where('title', 'Testing Gold')->first();

if ($promo) {
    echo "Found promo 'Testing Gold':\n";
    echo "- Start At: {$promo->start_at}\n";
    echo "- End At: {$promo->end_at}\n";
    echo "- Target: {$promo->target_audience}\n";

    // Normalize audience
    $audience = strtolower($promo->target_audience);
    if (in_array($audience, ['general', 'semua (general)', 'semua'])) {
        $audience = 'general';
    } elseif (in_array($audience, ['silver', 'silver member'])) {
        $audience = 'silver';
    } elseif (in_array($audience, ['gold', 'gold member'])) {
        $audience = 'gold';
    } elseif (in_array($audience, ['platinum', 'platinum member'])) {
        $audience = 'platinum';
    }

    $start = $promo->start_at ? date('Y-m-d', strtotime($promo->start_at)) : null;
    $end = $promo->end_at ? date('Y-m-d', strtotime($promo->end_at)) : null;

    $updated = DB::table('treatments')
        ->where('name', 'Testing Gold')
        ->update([
            'promo_start_date' => $start,
            'promo_end_date' => $end,
            'target_audience' => $audience,
        ]);

    echo "Updated {$updated} treatment rows!\n";
} else {
    echo "Promo 'Testing Gold' not found in promos table.\n";
}
