<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$oliver = \App\Models\User::where('name', 'like', '%oliver%')->first();
if ($oliver) {
    echo "ID: " . $oliver->id . "\n";
    echo "Total Spending: " . $oliver->total_spending . "\n";
    echo "Has Coloring Loyalty (method): " . ($oliver->has_coloring_loyalty ? 'true' : 'false') . "\n";
    
    // manual calculate coloring spend
    $coloringSpend = \App\Models\BookingDetail::whereHas('booking', function($q) use ($oliver) {
        $q->where(function($subQ) use ($oliver) {
            $subQ->where('user_id', $oliver->id);
            if (!empty($oliver->email)) $subQ->orWhere('customer_email', $oliver->email);
            if (!empty($oliver->phone)) $subQ->orWhere('customer_phone', $oliver->phone);
        })
          ->where('status', 'berhasil')
          ->where('payment_status', 'paid');
    })
    ->whereHas('treatmentDetail.treatment.category', function($q) {
        $q->where('name', 'like', '%Coloring%');
    })
    ->sum('price');
    
    echo "Manual Coloring Spend: " . $coloringSpend . "\n";
} else {
    echo "Oliver not found.\n";
}
