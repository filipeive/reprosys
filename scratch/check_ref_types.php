<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\FinancialTransaction;

$types = FinancialTransaction::distinct()->pluck('reference_type');
echo "Reference Types:\n";
foreach ($types as $type) {
    echo "- " . ($type ?: 'NULL') . "\n";
}
