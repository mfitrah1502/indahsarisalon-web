<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\Treatment;

$t = Treatment::with(['details', 'category'])->find(56);
echo "Treatment: {$t->name} (Category: " . ($t->category ? $t->category->name : 'NULL') . ")\n";
foreach ($t->details as $d) {
    echo "  Detail ID: {$d->id} - Name: '{$d->name}' - Has Stylist Price: " . ($d->has_stylist_price ? 'YES' : 'NO') . "\n";
}
