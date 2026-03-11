<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;

class RankController extends Controller
{
    /**
     * Show dynamic rank for a given class (e.g. "7A" or "Flower").
     */
    public function index($className)
    {
        // Load students of that class with user and subjects
        $students = Student::with(['user', 'subjects'])
            ->where('grade', $className) // adjust column if your class field is named differently
            ->get();

        $scores = $students->map(function($s) {
            // If your subjects table stores a single numeric column 'score' per subject row:
            $sum = $s->subjects->sum('score'); // common pivot: subjects() -> withPivot('score')
            return ['student' => $s, 'total' => (float)$sum];
        });

      
        $allZero = $scores->every(fn($d) => $d['total'] == 0);

        if ($allZero) {
            // Strategy B: sum known subject columns on the related subjects collection
            $scores = $students->map(function($s) {
                // adapt these field names to your schema
                $math = $s->subjects->sum('math');
                $khmer = $s->subjects->sum('khmer');
                $english = $s->subjects->sum('english');
                $history = $s->subjects->sum('history');
                $geography = $s->subjects->sum('geography');

                $total = $math + $khmer + $english + $history + $geography;
                return ['student' => $s, 'total' => (float)$total];
            });
        }

        // Sort by total desc
        $sorted = $scores->sortByDesc('total')->values();

        // Assign dense ranks: equal totals => same rank; next rank increments by 1
        $ranked = collect();
        $currentRank = 0;
        $lastTotal = null;
        foreach ($sorted as $index => $row) {
            if ($lastTotal === null || $row['total'] !== $lastTotal) {
                $currentRank++;
                $lastTotal = $row['total'];
            }
            $row['rank'] = $currentRank;
            // compute average if you want (need subject count)
            // We'll set average = null here; you can compute if you know subject count
            $row['average'] = null;
            $ranked->push($row);
        }

        return view('rank.index', [
            'className' => $className,
            'rankedStudents' => $ranked,
        ]);
    }
}
