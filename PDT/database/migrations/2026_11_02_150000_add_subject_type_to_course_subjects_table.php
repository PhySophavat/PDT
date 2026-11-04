<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_subjects', function (Blueprint $table) {
            if (!Schema::hasColumn('course_subjects', 'subject_type')) {
                $table->string('subject_type')->default('general')->after('description');
            }
        });

        // Smartly backfill existing subjects according to standard Cambodian classification
        $updates = [
            'science' => ['MTH', 'PHY', 'CHM', 'BIO', 'ESC', 'ICT', 'គណិត', 'រូប', 'គីមី', 'ជីវ', 'ផែនដី', 'កុំព្យូទ័រ', 'ព័ត៌មានវិទ្យា'],
            'social' => ['HIS', 'GEO', 'MOR', 'ECO', 'ប្រវត្តិ', 'ភូមិ', 'សីលធម៌', 'ពលរដ្ឋ', 'សេដ្ឋកិច្ច'],
            'languages' => ['ENG', 'FRA', 'KHM', 'អង់គ្លេស', 'បារាំង', 'ភាសា'],
            'core' => ['CORE', 'គោល'],
            'elective' => ['PE', 'SPT', 'ART', 'AGR', 'កីឡា', 'អប់រំកាយ', 'សិល្បៈ', 'កសិកម្ម', 'គេហវិជ្ជា'],
        ];

        try {
            $subjects = DB::table('course_subjects')->get();
            foreach ($subjects as $s) {
                $name = $s->name ?? '';
                $code = $s->code ?? '';
                $assigned = false;

                foreach ($updates as $type => $keywords) {
                    foreach ($keywords as $kw) {
                        if (stripos($code, $kw) !== false || mb_stripos($name, $kw) !== false) {
                            DB::table('course_subjects')
                                ->where('id', $s->id)
                                ->update(['subject_type' => $type]);
                            $assigned = true;
                            break 2;
                        }
                    }
                }

                if (!$assigned) {
                    DB::table('course_subjects')
                        ->where('id', $s->id)
                        ->update(['subject_type' => 'general']);
                }
            }
        } catch (\Throwable $e) {
            // Ignore if seeding during fresh migration
        }
    }

    public function down(): void
    {
        Schema::table('course_subjects', function (Blueprint $table) {
            if (Schema::hasColumn('course_subjects', 'subject_type')) {
                $table->dropColumn('subject_type');
            }
        });
    }
};

