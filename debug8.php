<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$indexes = \Illuminate\Support\Facades\DB::select('SHOW INDEX FROM users');
foreach($indexes as $i) {
    echo "{$i->Key_name} -> {$i->Column_name} (Non_unique: {$i->Non_unique})\n";
}
