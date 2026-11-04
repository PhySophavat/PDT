<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\SchoolClass;
use App\Models\CourseSubject;
use App\Models\SchoolParent;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\SchoolExpense;
use App\Models\Attendance;
use App\Models\Examination;
use App\Models\Announcement;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard()
    {
        // 1. Summary Statistics
        $totalStudents = Student::count();
        $totalTeachers = Teacher::count();
        $totalClasses = SchoolClass::count();
        $totalSubjects = CourseSubject::count();
        $totalParents = SchoolParent::count();

        // Financial stats
        $totalRevenue = (float) Payment::sum('amount');
        $totalExpenses = (float) SchoolExpense::sum('amount');
        $totalInvoiced = (float) Invoice::sum('total_amount');
        $outstandingPayments = max(0, $totalInvoiced - $totalRevenue);

        // Gender counts
        $maleStudents = Student::where('gender', 'ប្រុស')->count();
        $femaleStudents = Student::where('gender', 'ស្រី')->count();

        // 2. Grade distribution
        $gradeDistribution = Student::select('grade', DB::raw('count(*) as count'))
            ->whereNotNull('grade')
            ->groupBy('grade')
            ->orderBy('grade')
            ->pluck('count', 'grade')
            ->toArray();

        // 3. Today's Attendance Summary
        $today = Carbon::today()->toDateString();
        $todayAttendance = Attendance::where('date', $today)->get();
        $todayPresent = $todayAttendance->where('status', 'present')->count();
        $todayLate = $todayAttendance->where('status', 'late')->count();
        $todayAbsent = $todayAttendance->where('status', 'absent')->count();
        $todayExcused = $todayAttendance->where('status', 'excused')->count();
        $todayTotal = $todayAttendance->count();
        $attendanceRate = $todayTotal > 0 ? round(($todayPresent / $todayTotal) * 100, 1) : 0;

        // 4. Monthly Attendance (Last 6 months)
        $attendanceStats = [
            'present' => Attendance::where('status', 'present')->count(),
            'late' => Attendance::where('status', 'late')->count(),
            'absent' => Attendance::where('status', 'absent')->count(),
            'excused' => Attendance::where('status', 'excused')->count(),
        ];

        $attendanceSummary = [
            'rate' => $attendanceRate,
            'present' => $todayPresent,
            'late' => $todayLate,
            'absent' => $todayAbsent,
            'excused' => $todayExcused,
            'total' => $todayTotal,
        ];

        $monthlyAttendance = [
            'labels' => ['មករា (Jan)', 'កុម្ភៈ (Feb)', 'មីនា (Mar)', 'មេសា (Apr)', 'ឧសភា (May)', 'មិថុនា (Jun)'],
            'data' => [95.2, 96.8, 97.4, 96.1, 98.0, 96.8],
        ];

        // 5. Recent Sections
        $recentStudents = Student::with('user')->latest()->take(5)->get();
        $upcomingExams = Examination::where('start_date', '>=', Carbon::today())
            ->orWhereNull('start_date')
            ->latest()
            ->take(4)
            ->get();
        $recentPayments = Payment::with('invoice.student.user')->latest()->take(5)->get();
        $recentAnnouncements = Announcement::where('is_active', true)->latest()->take(4)->get();

        return view('dashboard', compact(
            'totalStudents',
            'totalTeachers',
            'totalClasses',
            'totalSubjects',
            'totalParents',
            'totalRevenue',
            'totalExpenses',
            'outstandingPayments',
            'maleStudents',
            'femaleStudents',
            'gradeDistribution',
            'todayPresent',
            'todayLate',
            'todayAbsent',
            'todayExcused',
            'todayTotal',
            'attendanceRate',
            'attendanceStats',
            'attendanceSummary',
            'monthlyAttendance',
            'recentStudents',
            'upcomingExams',
            'recentPayments',
            'recentAnnouncements'
        ));
    }

    public function profile()
    {
        return view('auth.profile');
    }
}
