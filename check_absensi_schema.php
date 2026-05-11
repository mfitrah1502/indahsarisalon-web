<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;

$columns = Schema::getConnection()->getDoctrineSchemaManager()->listTableColumns('absensi');
foreach($columns as $c) {
    echo $c->getName() . ': ' . ($c->getNotnull() ? 'NOT NULL' : 'NULL') . "\n";
}
