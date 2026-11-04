<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Vite;

echo "Vite tag:\n";
echo Vite::useHotFile(public_path('hot'))->withEntryPoints(['resources/css/app.css', 'resources/js/app.js'])->toHtml();
echo "\n";

