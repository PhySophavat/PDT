<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RankController extends Controller
{
    /**
     * Show dynamic rank for a given class (e.g. "7A" or "Flower").
     */
    public function index(Request $request, $className)
    {
        abort_unless(Auth::user()?->canAccessResultClass($className), 403, 'Unauthorized action.');

        $selectedMonth = (int) $request->integer('month', 0);
        $monthOptions = collect(range(1, 12))
            ->mapWithKeys(fn (int $month) => [$month => Carbon::create()->month($month)->locale('km')->translatedFormat('F')])
            ->all();

        $students = Student::with(['user', 'latestSubject'])
            ->where('grade', $className)
            ->get();

        if ($selectedMonth >= 1 && $selectedMonth <= 12) {
            $students = $students->filter(function ($student) use ($selectedMonth) {
                $subject = $student->latestSubject;

                return $subject && (int) $subject->updated_at?->month === $selectedMonth;
            })->values();
        }

        $scores = $students->map(function ($student) {
            $subject = $student->latestSubject;

            return [
                'student' => $student,
                'total' => $subject ? $subject->totalScore() : 0.0,
                'average' => $subject ? $subject->averageScore() : 0.0,
                'coefficient' => $subject ? $subject->coefficientValue() : 1.0,
            ];
        });

        $sorted = $scores->sortByDesc('total')->values();

        $ranked = collect();
        $currentRank = 0;
        $lastTotal = null;
        foreach ($sorted as $index => $row) {
            if ($lastTotal === null || $row['total'] !== $lastTotal) {
                $currentRank++;
                $lastTotal = $row['total'];
            }
            $row['rank'] = $currentRank;
            $ranked->push($row);
        }

        return view('rank.index', [
            'className' => $className,
            'rankedStudents' => $ranked,
            'monthOptions' => $monthOptions,
            'selectedMonth' => $selectedMonth,
        ]);
    }
}
