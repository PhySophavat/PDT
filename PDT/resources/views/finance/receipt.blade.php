<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ប័ណ្ណទទួលប្រាក់ - {{ $payment->receipt_number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        body {
            font-family: 'Kantumruy Pro', sans-serif;
            background-color: #f1f5f9;
        }
        @media print {
            body {
                background: white !important;
            }
            .no-print {
                display: none !important;
            }
            .print-card {
                box-shadow: none !important;
                border: 1px solid #cbd5e1 !important;
                margin: 0 !important;
                max-width: 100% !important;
            }
        }
    </style>
</head>
<body class="p-4 md:p-8 flex flex-col items-center justify-center min-h-screen">

    <!-- Action Toolbar -->
    <div class="no-print max-w-2xl w-full flex items-center justify-between mb-6">
        <a href="{{ route('finance.index', ['tab' => 'payments']) }}" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-xl text-sm font-semibold flex items-center gap-2 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            ត្រឡប់ក្រោយ
        </a>
        <button onclick="window.print()" class="px-5 py-2.5 bg-cyan-600 hover:bg-cyan-700 text-white rounded-xl text-sm font-semibold flex items-center gap-2 shadow-lg shadow-cyan-600/20 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            បោះពុម្ពប័ណ្ណទទួលប្រាក់ (Print)
        </button>
    </div>

    <!-- Official Receipt Card -->
    <div class="print-card max-w-2xl w-full bg-white rounded-3xl shadow-xl p-8 md:p-10 border border-slate-100 text-slate-800">
        <!-- Receipt Header -->
        <div class="text-center border-b border-slate-200 pb-6 mb-6">
            <div class="flex items-center justify-center gap-3 mb-2">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-cyan-600 to-teal-400 flex items-center justify-center text-white shadow-md">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <div class="text-left">
                    <h1 class="text-xl font-bold text-slate-900">{{ $schoolName }}</h1>
                    <p class="text-xs text-slate-500">{{ $schoolAddress }} | ទូរស័ព្ទ: {{ $schoolPhone }}</p>
                </div>
            </div>
            <div class="mt-4 inline-block px-4 py-1.5 bg-cyan-50 border border-cyan-200 rounded-full">
                <h2 class="text-lg font-bold text-cyan-800 tracking-wide">ប័ណ្ណទទួលប្រាក់ (RECEIPT)</h2>
            </div>
        </div>

        <!-- Receipt Meta Information -->
        <div class="grid grid-cols-2 gap-4 text-sm mb-6 bg-slate-50 p-4 rounded-2xl border border-slate-100">
            <div>
                <p class="text-xs text-slate-500">លេខប័ណ្ណ (Receipt No):</p>
                <p class="font-bold text-cyan-700 text-base">{{ $payment->receipt_number }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-slate-500">កាលបរិច្ឆេទ (Payment Date):</p>
                <p class="font-semibold text-slate-700">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d-M-Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500">សិស្ស (Student):</p>
                <p class="font-bold text-slate-800">{{ $payment->invoice?->student?->user?->name ?? 'N/A' }} ({{ $payment->invoice?->student?->student_id_card ?? 'N/A' }})</p>
                <p class="text-xs text-slate-500">ថ្នាក់៖ {{ $payment->invoice?->student?->grade ?? 'N/A' }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-slate-500">លេខវិក្កយបត្រ (Invoice Ref):</p>
                <p class="font-semibold text-slate-700">{{ $payment->invoice?->invoice_number ?? 'N/A' }}</p>
                <p class="text-xs text-slate-500">វិធីបង់ប្រាក់៖ <span class="capitalize font-semibold text-slate-700">{{ $payment->payment_method }}</span></p>
            </div>
        </div>

        <!-- Description Table -->
        <table class="w-full text-sm mb-6 border-collapse">
            <thead>
                <tr class="border-b-2 border-slate-200 text-left text-slate-500 text-xs">
                    <th class="py-2">បរិយាយ / កាតព្វកិច្ច (Description)</th>
                    <th class="py-2 text-right">តម្លៃសរុប</th>
                    <th class="py-2 text-right">ចំនួនបានបង់</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <tr>
                    <td class="py-3 font-semibold text-slate-800">
                        {{ $payment->invoice?->title ?? 'ថ្លៃសិក្សា / សេវាកម្មសាលា' }}
                        @if($payment->invoice?->feeCategory)
                            <span class="block text-xs font-normal text-slate-500">{{ $payment->invoice->feeCategory->name }}</span>
                        @endif
                    </td>
                    <td class="py-3 text-right font-medium text-slate-600">
                        ${{ number_format($payment->invoice?->total_amount ?? 0, 2) }}
                    </td>
                    <td class="py-3 text-right font-bold text-cyan-600">
                        ${{ number_format($payment->amount, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Summary Totals -->
        <div class="border-t border-slate-200 pt-4 flex flex-col items-end mb-8 text-sm">
            <div class="w-64 space-y-2">
                <div class="flex justify-between text-slate-600">
                    <span>ចំនួនទឹកប្រាក់បង់លើកនេះ៖</span>
                    <span class="font-bold text-cyan-700 text-base">${{ number_format($payment->amount, 2) }}</span>
                </div>
                <div class="flex justify-between text-slate-500 text-xs">
                    <span>បានបង់សរុប (Total Paid):</span>
                    <span>${{ number_format($payment->invoice?->paid_amount ?? 0, 2) }}</span>
                </div>
                <div class="flex justify-between text-slate-500 text-xs border-t border-slate-200 pt-1">
                    <span>នៅសល់ (Remaining Balance):</span>
                    <span class="font-semibold text-rose-600">
                        ${{ number_format(max(0, ($payment->invoice?->total_amount ?? 0) - ($payment->invoice?->paid_amount ?? 0)), 2) }}
                    </span>
                </div>
            </div>
        </div>

        @if($payment->note)
            <div class="bg-amber-50 border border-amber-200 p-3 rounded-xl text-xs text-amber-800 mb-6">
                <strong>កំណត់សម្គាល់៖</strong> {{ $payment->note }}
            </div>
        @endif

        <!-- Signatures -->
        <div class="grid grid-cols-2 gap-8 text-center text-xs text-slate-500 pt-8 border-t border-dashed border-slate-300">
            <div>
                <p class="font-medium text-slate-700 mb-16">ហត្ថលេខាអ្នកបង់ប្រាក់ (Payer)</p>
                <div class="border-t border-slate-300 w-36 mx-auto pt-1">
                    កាលបរិច្ឆេទ
                </div>
            </div>
            <div>
                <p class="font-medium text-slate-700 mb-16">ហត្ថលេខា និងត្រាគណនេយ្យករ (Cashier / Accountant)</p>
                <div class="border-t border-slate-300 w-36 mx-auto pt-1">
                    {{ auth()->user()->name ?? 'ហត្ថលេខា' }}
                </div>
            </div>
        </div>
    </div>

</body>
</html>
