<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\SchoolClass;
use App\Models\Attendance;
use App\Models\Examination;
use App\Models\ExaminationScore;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\SchoolExpense;
use App\Models\Book;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->input('type', 'students');
        $academicYearId = $request->input('academic_year_id');
        $grade = $request->input('grade');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $academicYears = AcademicYear::all();
        $classes = SchoolClass::orderedNames();

        $reportData = [];

        switch ($type) {
            case 'students':
                $reportData = Student::with('user', 'academicYear')
                    ->when($grade, fn ($q) => $q->where('grade', $grade))
                    ->when($academicYearId, fn ($q) => $q->where('academic_year_id', $academicYearId))
                    ->latest()
                    ->get();
                break;

            case 'teachers':
                $reportData = Teacher::with('user')->latest()->get();
                break;

            case 'attendance':
                $reportData = Attendance::with('student.user')
                    ->when($grade, fn ($q) => $q->where('class_name', $grade))
                    ->when($startDate, fn ($q) => $q->where('date', '>=', $startDate))
                    ->when($endDate, fn ($q) => $q->where('date', '<=', $endDate))
                    ->latest('date')
                    ->get();
                break;

            case 'finance':
                $revenue = (float) Payment::when($startDate, fn ($q) => $q->where('payment_date', '>=', $startDate))
                    ->when($endDate, fn ($q) => $q->where('payment_date', '<=', $endDate))
                    ->sum('amount');
                $expense = (float) SchoolExpense::when($startDate, fn ($q) => $q->where('expense_date', '>=', $startDate))
                    ->when($endDate, fn ($q) => $q->where('expense_date', '<=', $endDate))
                    ->sum('amount');
                $invoices = Invoice::with('student.user')->latest()->get();
                $payments = Payment::with('invoice.student.user')->latest()->get();
                $expenses = SchoolExpense::latest()->get();

                $reportData = compact('revenue', 'expense', 'invoices', 'payments', 'expenses');
                break;

            case 'examinations':
                $reportData = ExaminationScore::with('student.user', 'examination', 'courseSubject')
                    ->when($grade, fn ($q) => $q->where('class_name', $grade))
                    ->get();
                break;

            case 'library':
                $reportData = Book::with('category', 'borrows.student.user')->get();
                break;
        }

        return view('report.index', compact(
            'type',
            'reportData',
            'academicYears',
            'classes',
            'grade',
            'academicYearId',
            'startDate',
            'endDate'
        ));
    }
}
