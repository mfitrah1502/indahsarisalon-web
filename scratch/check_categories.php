<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\Category;
use App\Models\Treatment;

echo "--- CATEGORIES ---\n";
foreach (Category::all() as $cat) {
    echo "ID: {$cat->id} - Name: '{$cat->name}'\n";
}

echo "\n--- TREATMENTS BY CATEGORY ---\n";
foreach (Treatment::with('category')->get() as $t) {
    echo "ID: {$t->id} - Name: '{$t->name}' - Category: '" . ($t->category ? $t->category->name : 'NULL') . "'\n";
}
