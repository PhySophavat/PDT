<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>សៀវភៅតាមដានការសិក្សា - {{ $student->user->name ?? 'Student' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;500;600;700;800&family=Moul&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Kantumruy Pro', sans-serif; background: #f8fafc; }
        .font-moul { font-family: 'Moul', serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white; padding: 0; }
            .report-card-sheet { border: none !important; box-shadow: none !important; margin: 0 !important; width: 100% !important; }
        }
    </style>
</head>
<body class="p-6 flex flex-col items-center justify-center min-h-screen">
    <!-- Top Action Bar -->
    <div class="no-print mb-6 flex items-center gap-3">
        <button onclick="window.print()" class="px-5 py-2.5 rounded-2xl bg-cyan-600 hover:bg-cyan-700 text-white font-bold text-sm shadow-md transition flex items-center gap-2">
            <i class="fa-solid fa-print"></i>
            <span>បោះពុម្ពសៀវភៅតាមដាន</span>
        </button>
        <button onclick="window.close()" class="px-4 py-2.5 rounded-2xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-sm transition">
            បិទ
        </button>
    </div>

    <!-- Official Report Card Sheet (A4 Portrait standard) -->
    <div class="report-card-sheet w-[800px] bg-white rounded-3xl p-10 border-2 border-slate-300 shadow-xl space-y-6 text-slate-800">
        <!-- School Header Banner -->
        <div class="text-center space-y-1 border-b-2 border-slate-800 pb-5">
            <h3 class="font-moul text-sm text-cyan-900 tracking-wider">ព្រះរាជាណាចក្រកម្ពុជា</h3>
            <p class="font-moul text-xs text-cyan-900 tracking-widest">ជាតិ សាសនា ព្រះមហាក្សត្រ</p>
            <div class="w-24 h-0.5 bg-slate-800 mx-auto my-2"></div>

            <div class="flex items-center justify-between pt-2">
                <div class="text-left text-xs">
                    <h4 class="font-bold text-slate-900 text-sm uppercase">{{ $schoolName }}</h4>
                    <p class="text-slate-500">{{ $schoolAddress }}</p>
                    <p class="text-slate-500">ទូរស័ព្ទ: {{ $schoolPhone }}</p>
                </div>
                <div class="text-right">
                    <span class="inline-block px-3 py-1 rounded-xl bg-cyan-100 text-cyan-800 text-xs font-mono font-bold">
                        {{ $student->student_id_card ?? 'STU-' . $student->id }}
                    </span>
                    <p class="text-[10px] text-slate-400 mt-1">កាលបរិច្ឆេទចេញ: {{ date('d-M-Y') }}</p>
                </div>
            </div>

            <h2 class="font-moul text-lg text-cyan-950 pt-4 uppercase">សៀវភៅតាមដានការសិក្សា និងពិន្ទុប្រឡង</h2>
            <p class="text-xs font-bold text-slate-600">{{ $examination->title }} &bull; ឆមាសទី {{ $examination->semester }}</p>
        </div>

        <!-- Student Information Banner -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs">
            <div>
                <span class="text-slate-500 block">ឈ្មោះសិស្ស:</span>
                <span class="font-bold text-sm text-slate-900">{{ $student->user->name ?? '-' }}</span>
            </div>
            <div>
                <span class="text-slate-500 block">ភេទ:</span>
                <span class="font-bold text-sm text-slate-900">{{ $student->gender ?? '-' }}</span>
            </div>
            <div>
                <span class="text-slate-500 block">ថ្នាក់រៀន:</span>
                <span class="font-bold text-sm text-cyan-700">ថ្នាក់ {{ $student->grade ?? '-' }}</span>
            </div>
            <div>
                <span class="text-slate-500 block">ឆ្នាំសិក្សា:</span>
                <span class="font-bold text-sm text-slate-900">{{ $student->academicYear->name ?? '២០២៥-២០២៦' }}</span>
            </div>
        </div>

        <!-- Subject Scores Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse border border-slate-300">
                <thead class="bg-slate-100 text-xs uppercase font-bold text-slate-700 border-b border-slate-300">
                    <tr>
                        <th class="border border-slate-300 px-4 py-2.5 w-12 text-center">ល.រ</th>
                        <th class="border border-slate-300 px-4 py-2.5">មុខវិជ្ជា</th>
                        <th class="border border-slate-300 px-4 py-2.5 text-center">ពិន្ទុអតិបរមា</th>
                        <th class="border border-slate-300 px-4 py-2.5 text-center">ពិន្ទុទទួលបាន</th>
                        <th class="border border-slate-300 px-4 py-2.5 text-center">និទ្ទេស</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($scores as $idx => $sc)
                        <tr>
                            <td class="border border-slate-300 px-4 py-2 text-center">{{ $idx + 1 }}</td>
                            <td class="border border-slate-300 px-4 py-2 font-bold">{{ $sc->subject_name }}</td>
                            <td class="border border-slate-300 px-4 py-2 text-center font-mono">{{ number_format($sc->max_score, 0) }}</td>
                            <td class="border border-slate-300 px-4 py-2 text-center font-mono font-bold text-cyan-800">{{ number_format($sc->score, 1) }}</td>
                            <td class="border border-slate-300 px-4 py-2 text-center font-black">{{ $sc->grade_letter ?? 'B' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="border border-slate-300 px-4 py-4 text-center text-slate-400">មិនទាន់មានពិន្ទុត្រូវបង្ហាញ</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-slate-50 font-bold border-t-2 border-slate-400">
                    <tr>
                        <td colspan="2" class="border border-slate-300 px-4 py-2 text-right">ពិន្ទុសរុប៖</td>
                        <td colspan="3" class="border border-slate-300 px-4 py-2 text-center font-mono font-black text-cyan-800 text-base">
                            {{ number_format($totalScore, 1) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="border border-slate-300 px-4 py-2 text-right">មធ្យមភាគ៖</td>
                        <td colspan="3" class="border border-slate-300 px-4 py-2 text-center font-mono font-black text-emerald-700 text-base">
                            {{ number_format($average, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="border border-slate-300 px-4 py-2 text-right">ចំណាត់ថ្នាក់ក្នុងថ្នាក់៖</td>
                        <td colspan="3" class="border border-slate-300 px-4 py-2 text-center font-black text-purple-700 text-base">
                            លេខ {{ $rank }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="border border-slate-300 px-4 py-2 text-right">និទ្ទេសរួម៖</td>
                        <td colspan="3" class="border border-slate-300 px-4 py-2 text-center font-black text-cyan-900 text-lg">
                            {{ $overallGrade }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="border border-slate-300 px-4 py-2 text-right">លទ្ធផល៖</td>
                        <td colspan="3" class="border border-slate-300 px-4 py-2 text-center font-black {{ $average >= 25.0 ? 'text-emerald-700' : 'text-rose-700' }} text-base">
                            {{ $average >= 25.0 ? 'ជាប់' : 'ធ្លាក់' }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Teacher Remarks & Observations -->
        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-1.5">
            <p class="font-bold text-slate-800">មតិយោបល់ និងការវាយតម្លៃរបស់គ្រូបន្ទុកថ្នាក់៖</p>
            <p class="italic text-slate-600">
                សិស្សានុសិស្សមានការខិតខំប្រឹងប្រែងរៀនសូត្របានល្អ មានវិន័យ និងគោរពបទបញ្ជាផ្ទៃក្នុងរបស់សាលាបានយ៉ាងខ្ជាប់ខ្ជួន។ សូមបន្តការខិតខំប្រឹងប្រែងបន្ថែមទៀតដើម្បីទទួលបានលទ្ធផលកាន់តែប្រសើរ។
            </p>
        </div>

        <!-- Signatures Block -->
        <div class="pt-8 grid grid-cols-3 text-center text-xs">
            <div>
                <p class="font-bold text-slate-700">មាតាបិតា / អាណាព្យាបាល</p>
                <div class="h-20"></div>
                <p class="text-[11px] text-slate-400">[ ហត្ថលេខា & ឈ្មោះ ]</p>
            </div>
            <div>
                <p class="font-bold text-slate-700">គ្រូបន្ទុកថ្នាក់</p>
                <div class="h-20"></div>
                <p class="text-[11px] text-slate-400">[ ហត្ថលេខា & ឈ្មោះ ]</p>
            </div>
            <div>
                <p class="font-bold text-slate-700">នាយកសាលា</p>
                <div class="h-20 flex items-center justify-center">
                    <span class="text-cyan-800 font-serif italic font-bold">[ត្រាសាលា & ហត្ថលេខា]</span>
                </div>
                <p class="text-[11px] text-slate-900 font-bold">គណៈគ្រប់គ្រងសាលា</p>
            </div>
        </div>
    </div>
</body>
</html>
