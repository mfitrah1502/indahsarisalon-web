<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\Treatment;

$orphans = Treatment::whereDoesntHave('details')->get();
echo "Found " . count($orphans) . " orphan treatments:\n";
foreach ($orphans as $t) {
    echo "- ID: {$t->id} | Name: '{$t->name}'\n";
    $t->delete();
    echo "  Soft-deleted successfully!\n";
}
