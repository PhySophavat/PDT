<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('course_subjects', function (Blueprint $table) {
            if (!Schema::hasColumn('course_subjects', 'max_score')) {
                $table->decimal('max_score', 8, 2)->default(50.00)->after('subject_type');
            }
            if (!Schema::hasColumn('course_subjects', 'credit')) {
                $table->decimal('credit', 5, 2)->default(1.00)->after('max_score');
            }
            if (!Schema::hasColumn('course_subjects', 'class_configs')) {
                $table->json('class_configs')->nullable()->after('credit');
            }
        });

        // Initialize standard Cambodian defaults for subjects
        $subjects = DB::table('course_subjects')->get();
        foreach ($subjects as $s) {
            $code = strtoupper($s->code);
            $maxScore = 50.00;
            $credit = 1.00;
            $configs = [];

            if (str_contains($code, 'KHM')) {
                $maxScore = 100.00;
                $credit = 2.00;
                $configs = [
                    '7' => ['max_score' => 100, 'credit' => 2],
                    '8' => ['max_score' => 100, 'credit' => 2],
                    '9' => ['max_score' => 100, 'credit' => 2],
                    '10' => ['max_score' => 100, 'credit' => 2],
                    '11' => ['max_score' => 75, 'credit' => 1.5],
                    '12' => ['max_score' => 75, 'credit' => 1.5],
                ];
            } elseif (str_contains($code, 'MTH')) {
                $maxScore = 100.00;
                $credit = 2.00;
                $configs = [
                    '7' => ['max_score' => 100, 'credit' => 2],
                    '8' => ['max_score' => 100, 'credit' => 2],
                    '9' => ['max_score' => 100, 'credit' => 2],
                    '10' => ['max_score' => 100, 'credit' => 2],
                    '11' => ['max_score' => 100, 'credit' => 2],
                    '12' => ['max_score' => 100, 'credit' => 2],
                ];
            } elseif (str_contains($code, 'PHY') || str_contains($code, 'CHM') || str_contains($code, 'BIO')) {
                $maxScore = 75.00;
                $credit = 1.50;
                $configs = [
                    '7' => ['max_score' => 50, 'credit' => 1],
                    '8' => ['max_score' => 50, 'credit' => 1],
                    '9' => ['max_score' => 50, 'credit' => 1],
                    '10' => ['max_score' => 50, 'credit' => 1],
                    '11' => ['max_score' => 75, 'credit' => 1.5],
                    '12' => ['max_score' => 75, 'credit' => 1.5],
                ];
            } elseif (str_contains($code, 'ENG')) {
                $maxScore = 50.00;
                $credit = 1.00;
                $configs = [
                    '7' => ['max_score' => 50, 'credit' => 1],
                    '8' => ['max_score' => 50, 'credit' => 1],
                    '9' => ['max_score' => 50, 'credit' => 1],
                    '10' => ['max_score' => 50, 'credit' => 1],
                    '11' => ['max_score' => 50, 'credit' => 1],
                    '12' => ['max_score' => 50, 'credit' => 1],
                ];
            } else {
                $maxScore = 50.00;
                $credit = 1.00;
                $configs = [
                    '7' => ['max_score' => 50, 'credit' => 1],
                    '8' => ['max_score' => 50, 'credit' => 1],
                    '9' => ['max_score' => 50, 'credit' => 1],
                    '10' => ['max_score' => 50, 'credit' => 1],
                    '11' => ['max_score' => 50, 'credit' => 1],
                    '12' => ['max_score' => 50, 'credit' => 1],
                ];
            }

            DB::table('course_subjects')
                ->where('id', $s->id)
                ->update([
                    'max_score' => $maxScore,
                    'credit' => $credit,
                    'class_configs' => json_encode($configs),
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_subjects', function (Blueprint $table) {
            if (Schema::hasColumn('course_subjects', 'class_configs')) {
                $table->dropColumn('class_configs');
            }
            if (Schema::hasColumn('course_subjects', 'credit')) {
                $table->dropColumn('credit');
            }
            if (Schema::hasColumn('course_subjects', 'max_score')) {
                $table->dropColumn('max_score');
            }
        });
    }
};

