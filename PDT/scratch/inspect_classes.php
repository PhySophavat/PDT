<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$classes = DB::table('students')->select('grade')->distinct()->orderBy('grade')->pluck('grade');
echo "Distinct student grades: " . json_encode($classes) . "\n";

$classesTable = DB::table('classes')->get();
echo "Classes table count: " . $classesTable->count() . "\n";
if ($classesTable->count() > 0) {
    echo "Classes: " . json_encode($classesTable->take(5)) . "\n";
}

