<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$cols = Schema::getColumnListing('course_subjects');
echo "Columns: " . json_encode($cols, JSON_PRETTY_PRINT) . "\n";

$sample = DB::table('course_subjects')->first();
echo "Sample: " . json_encode($sample, JSON_PRETTY_PRINT) . "\n";

