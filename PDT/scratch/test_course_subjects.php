<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CourseSubject;

$subjects = CourseSubject::all();
echo "Total subjects: " . $subjects->count() . "\n";
foreach ($subjects->take(4) as $s) {
    echo "- {$s->name} ({$s->code}):\n";
    echo "  Default Max Score: {$s->max_score}, Credit: {$s->credit}\n";
    echo "  Max Score for Grade 7: " . $s->getMaxScoreForClass('7A') . ", Credit: " . $s->getCreditForClass('7A') . "\n";
    echo "  Max Score for Grade 11D: " . $s->getMaxScoreForClass('11D') . ", Credit: " . $s->getCreditForClass('11D') . "\n";
}

