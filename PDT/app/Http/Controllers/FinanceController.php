<?php

namespace App\Http\Controllers;

use App\Models\FeeCategory;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\SchoolExpense;
use App\Models\Student;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'invoices');

        // Financial Overview
        $totalRevenue = (float) Payment::sum('amount');
        $totalExpenses = (float) SchoolExpense::sum('amount');
        $totalInvoiced = (float) Invoice::sum('total_amount');
        $totalOutstanding = max(0, $totalInvoiced - $totalRevenue);
        $netIncome = $totalRevenue - $totalExpenses;

        // Invoices with filters
        $invoices = Invoice::with('student.user', 'feeCategory', 'payments')
            ->latest()
            ->paginate(10, ['*'], 'invoices_page');

        // Payments
        $payments = Payment::with('invoice.student.user')
            ->latest()
            ->paginate(10, ['*'], 'payments_page');

        // Expenses
        $expenses = SchoolExpense::latest()
            ->paginate(10, ['*'], 'expenses_page');

        // Fee Categories
        $feeCategories = FeeCategory::all();
        $students = Student::with('user')->get();

        $currency = SystemSetting::get('currency', 'USD');

        return view('finance.index', compact(
            'tab',
            'totalRevenue',
            'totalExpenses',
            'totalInvoiced',
            'totalOutstanding',
            'netIncome',
            'invoices',
            'payments',
            'expenses',
            'feeCategories',
            'students',
            'currency'
        ));
    }

    public function storeInvoice(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'fee_category_id' => 'nullable|exists:fee_categories,id',
            'title' => 'required|string|max:255',
            'total_amount' => 'required|numeric|min:0.01',
            'due_date' => 'nullable|date',
        ]);

        $nextId = Invoice::max('id') + 1;
        $invNumber = sprintf('INV-%s-%04d', date('Y'), $nextId);

        Invoice::create([
            'invoice_number' => $invNumber,
            'student_id' => $validated['student_id'],
            'fee_category_id' => $validated['fee_category_id'] ?? null,
            'title' => $validated['title'],
            'total_amount' => $validated['total_amount'],
            'paid_amount' => 0.00,
            'due_date' => $validated['due_date'] ?? Carbon::now()->addDays(30),
            'status' => 'unpaid',
            'currency' => SystemSetting::get('currency', 'USD'),
        ]);

        return redirect()->route('finance.index', ['tab' => 'invoices'])->with('success', "បានបង្កើតវិក្កយបត្រ {$invNumber} ដោយជោគជ័យ!");
    }

    public function storePayment(Request $request)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,qr_code,other',
            'payment_date' => 'required|date',
            'note' => 'nullable|string',
        ]);

        $invoice = Invoice::findOrFail($validated['invoice_id']);

        $nextId = Payment::max('id') + 1;
        $receiptNumber = sprintf('RCP-%s-%04d', date('Y'), $nextId);

        $newPaidAmount = $invoice->paid_amount + $validated['amount'];
        $status = 'partial';
        if ($newPaidAmount >= $invoice->total_amount) {
            $status = 'paid';
        }

        DB::transaction(function () use ($invoice, $validated, $receiptNumber, $newPaidAmount, $status) {
            Payment::create([
                'invoice_id' => $invoice->id,
                'receipt_number' => $receiptNumber,
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'payment_date' => $validated['payment_date'],
                'note' => $validated['note'] ?? null,
            ]);

            $invoice->update([
                'paid_amount' => $newPaidAmount,
                'status' => $status,
            ]);
        });

        return redirect()->route('finance.index', ['tab' => 'payments'])->with('success', "បានកត់ត្រាការទូទាត់ប្រាក់ {$receiptNumber} ដោយជោគជ័យ!");
    }

    public function storeExpense(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0.01',
            'expense_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        SchoolExpense::create($validated);

        return redirect()->route('finance.index', ['tab' => 'expenses'])->with('success', 'បានកត់ត្រាចំណាយថ្មីដោយជោគជ័យ!');
    }

    public function printReceipt(Payment $payment)
    {
        $payment->load('invoice.student.user', 'invoice.feeCategory');
        $schoolName = SystemSetting::get('school_name', 'សាលារៀន ជំនាន់ថ្មី PDT');
        $schoolAddress = SystemSetting::get('school_address', 'រាជធានីភ្នំពេញ');
        $schoolPhone = SystemSetting::get('school_phone', '012 345 678');

        return view('finance.receipt', compact('payment', 'schoolName', 'schoolAddress', 'schoolPhone'));
    }
}
