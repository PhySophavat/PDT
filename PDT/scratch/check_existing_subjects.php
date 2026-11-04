<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$subjects = DB::table('course_subjects')->get();
foreach ($subjects as $s) {
    echo "ID: {$s->id} | Code: {$s->code} | Name: {$s->name} | Type: {$s->subject_type} | CreditHours: {$s->credit_hours}\n";
}

