<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>កាតសិស្ស - {{ $student->user->name ?? 'Student' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Kantumruy Pro', sans-serif; background: #f1f5f9; }
        @media print {
            .no-print { display: none !important; }
            body { background: white; margin: 0; padding: 0; }
        }
    </style>
</head>
<body class="p-6 flex flex-col items-center justify-center min-h-screen">
    <div class="no-print mb-6 flex items-center gap-3">
        <button onclick="window.print()" class="px-5 py-2.5 rounded-2xl bg-cyan-600 hover:bg-cyan-700 text-white font-bold text-sm shadow-md transition flex items-center gap-2">
            <i class="fa-solid fa-print"></i>
            <span>បោះពុម្ពកាតសិស្ស (Print ID Card)</span>
        </button>
        <button onclick="window.close()" class="px-4 py-2.5 rounded-2xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-sm transition">
            បិទ
        </button>
    </div>

    <!-- ID Card Container (Standard CR80 Credit Card Size Ratio) -->
    <div class="flex flex-col md:flex-row gap-6">
        <!-- Front Side -->
        <div class="w-[340px] h-[520px] rounded-3xl bg-gradient-to-b from-cyan-600 via-teal-600 to-slate-900 text-white p-5 shadow-2xl relative overflow-hidden flex flex-col justify-between border-4 border-white">
            <!-- Decorative Circles -->
            <div class="absolute -top-12 -right-12 w-40 h-40 rounded-full bg-white/10 blur-xl"></div>
            <div class="absolute -bottom-12 -left-12 w-40 h-40 rounded-full bg-cyan-400/20 blur-xl"></div>

            <!-- Top Header -->
            <div class="text-center relative z-10">
                <div class="flex items-center justify-center gap-2 mb-1">
                    <div class="w-8 h-8 rounded-lg bg-white text-cyan-600 flex items-center justify-center font-black text-sm shadow">
                        PDT
                    </div>
                    <div class="text-left">
                        <h2 class="text-xs font-extrabold uppercase tracking-wider leading-none">សាលារៀន ជំនាន់ថ្មី</h2>
                        <p class="text-[9px] text-cyan-200 font-medium tracking-widest uppercase">School Management</p>
                    </div>
                </div>
                <div class="h-0.5 w-full bg-gradient-to-r from-transparent via-cyan-300 to-transparent my-2"></div>
                <p class="text-[11px] font-bold tracking-wider text-cyan-100 uppercase">ប័ណ្ណសម្គាល់ខ្លួនសិស្ស (STUDENT ID CARD)</p>
            </div>

            <!-- Photo & Name -->
            <div class="text-center relative z-10 my-auto">
                <div class="w-28 h-28 mx-auto rounded-2xl border-4 border-white shadow-xl overflow-hidden bg-white/20 mb-3 flex items-center justify-center">
                    @if($student->photo)
                        <img src="{{ asset('storage/' . $student->photo) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-cyan-700 flex items-center justify-center text-white font-extrabold text-4xl">
                            {{ mb_substr($student->user->name ?? 'S', 0, 1) }}
                        </div>
                    @endif
                </div>

                <h3 class="text-lg font-black text-white leading-snug">{{ $student->user->name ?? 'ឈ្មោះសិស្ស' }}</h3>
                <span class="inline-block mt-1 px-3 py-0.5 rounded-full bg-cyan-500/30 border border-cyan-400/50 text-cyan-200 text-xs font-mono font-bold tracking-wider">
                    {{ $student->student_id_card ?? 'STU-' . $student->id }}
                </span>
            </div>

            <!-- Details Grid -->
            <div class="bg-black/25 backdrop-blur-md rounded-2xl p-3 border border-white/10 text-xs space-y-1 relative z-10">
                <div class="flex justify-between">
                    <span class="text-slate-300">ថ្នាក់ (Grade):</span>
                    <span class="font-bold text-white">{{ $student->grade ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-300">ភេទ (Gender):</span>
                    <span class="font-bold text-white">{{ $student->gender ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-300">ថ្ងៃខែឆ្នាំកំណើត (DOB):</span>
                    <span class="font-bold text-white">{{ $student->date_of_birth ? $student->date_of_birth->format('d-M-Y') : '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-300">ឆ្នាំសិក្សា (Academic Year):</span>
                    <span class="font-bold text-cyan-300">{{ $student->academicYear->name ?? '២០២៥-២០២៦' }}</span>
                </div>
            </div>

            <!-- Footer Barcode simulation -->
            <div class="text-center pt-2 relative z-10 border-t border-white/10">
                <div class="inline-block px-3 py-1 bg-white rounded text-slate-900 font-mono text-[10px] tracking-widest font-black">
                    *{{ $student->student_id_card ?? 'STU-' . $student->id }}*
                </div>
            </div>
        </div>

        <!-- Back Side -->
        <div class="w-[340px] h-[520px] rounded-3xl bg-white text-slate-800 p-5 shadow-2xl relative overflow-hidden flex flex-col justify-between border-4 border-slate-200">
            <div>
                <div class="text-center border-b border-slate-200 pb-3">
                    <h3 class="font-bold text-xs text-cyan-800 uppercase">លក្ខខណ្ឌប្រើប្រាស់ប័ណ្ណ</h3>
                    <p class="text-[10px] text-slate-400">Card Terms & Conditions</p>
                </div>

                <div class="mt-4 text-[11px] text-slate-600 space-y-2 leading-relaxed">
                    <p>១. ប័ណ្ណនេះជាកម្មសិទ្ធិរបស់សាលារៀន PDT។</p>
                    <p>២. សិស្សានុសិស្សត្រូវពាក់ប័ណ្ណនេះជានិច្ចពេលនៅក្នុងបរិវេណសាលា។</p>
                    <p>៣. ប័ណ្ណនេះមិនអាចផ្ទេរទៅឲ្យអ្នកដទៃប្រើប្រាស់បានឡើយ។</p>
                    <p>៤. បើបាត់បង់ សូមទាក់ទងមកការិយាល័យរដ្ឋបាលសាលាជាបន្ទាន់។</p>
                </div>

                <div class="mt-6 p-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-1.5">
                    <p class="font-bold text-cyan-800"><i class="fa-solid fa-phone mr-1"></i> ទំនាក់ទំនងបន្ទាន់:</p>
                    <p class="text-[11px] text-slate-600">ទូរស័ព្ទសាលា: <strong>012 345 678</strong></p>
                    <p class="text-[11px] text-slate-600">អាណាព្យាបាល: <strong>{{ $student->mother_phone ?? $student->father_phone ?? '-' }}</strong></p>
                </div>
            </div>

            <!-- Signatures -->
            <div class="pt-4 border-t border-slate-200 flex justify-between items-end text-center">
                <div>
                    <div class="h-10"></div>
                    <p class="text-[10px] font-bold text-slate-700">ហត្ថលេខាសិស្ស</p>
                </div>
                <div>
                    <div class="h-10 flex items-center justify-center">
                        <span class="text-cyan-800 font-serif italic text-xs font-bold">[ត្រា & ហត្ថលេខា]</span>
                    </div>
                    <p class="text-[10px] font-bold text-slate-700">នាយកសាលា</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
