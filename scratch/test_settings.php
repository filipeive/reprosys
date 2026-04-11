<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Setting;

echo "Setting 'company_name'...\n";
Setting::set('company_name', 'FDSMULTSERVICES+');
echo "Value: " . Setting::get('company_name') . "\n";
echo "Done!\n";
