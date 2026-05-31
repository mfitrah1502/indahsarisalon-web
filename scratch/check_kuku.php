<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\Treatment;

$treatments = Treatment::withTrashed()->where('name', 'like', '%kuku%')->get();
echo "Found " . count($treatments) . " treatments:\n";
foreach ($treatments as $t) {
    echo "ID: {$t->id} | Name: '{$t->name}' | Deleted At: " . ($t->deleted_at ?: 'NULL') . " | Is Active: " . ($t->is_active ? 'True' : 'False') . "\n";
}
