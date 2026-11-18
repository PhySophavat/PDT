<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ប័ណ្ណសម្គាល់ខ្លួនសិស្ស - {{ $student->name_khmer ?? $student->user->name ?? 'Student' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Battambang:wght@400;700;900&family=Moul&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Battambang', sans-serif; background: #f1f5f9; }
        .font-moul { font-family: 'Moul', cursive, serif; }
        .font-battambang { font-family: 'Battambang', sans-serif; }
        .card-main-title {
            font-family: 'Moul', cursive, serif;
            color: #1a56db;
            -webkit-text-stroke: 0.8px #0f3460;
            text-shadow: 0 1px 2px rgba(15, 52, 96, 0.25);
            letter-spacing: 0.5px;
        }
        @media print {
            .no-print { display: none !important; }
            body { background: white; margin: 0; padding: 0; }
            #student-id-card-element {
                box-shadow: none !important;
                border: 3px solid #1f4e79 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body class="p-4 sm:p-6 flex flex-col items-center justify-center min-h-screen">
    <div class="no-print mb-6 flex items-center gap-3">
        <button onclick="window.print()" class="px-5 py-2.5 rounded-2xl bg-[#1f4e79] hover:bg-blue-900 text-white font-bold text-sm shadow-md transition flex items-center gap-2 cursor-pointer">
            <i class="fa-solid fa-print"></i>
            <span>បោះពុម្ពកាតសិស្ស (Print)</span>
        </button>
        <button onclick="window.close()" class="px-4 py-2.5 rounded-2xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-sm transition cursor-pointer">
            បិទ
        </button>
    </div>

    @php
        function toKhmerNum($num) {
            if ($num === null || $num === '') return '';
            $khmerDigits = ['០', '១', '២', '៣', '៤', '៥', '៦', '៧', '៨', '៩'];
            return str_replace(['0','1','2','3','4','5','6','7','8','9'], $khmerDigits, (string) $num);
        }
        function toKhmerDate($date) {
            if (!$date) return '១២ ឧសភា ២០០៨';
            try {
                $d = is_string($date) ? new \DateTime($date) : $date;
                $day = toKhmerNum(str_pad($d->format('d'), 2, '0', STR_PAD_LEFT));
                $months = ['មករា', 'កុម្ភៈ', 'មីនា', 'មេសា', 'ឧសភា', 'មិថុនា', 'កក្កដា', 'សីហា', 'កញ្ញា', 'តុលា', 'វិច្ឆិកា', 'ធ្នូ'];
                $month = $months[(int)$d->format('m') - 1];
                $year = toKhmerNum($d->format('Y'));
                return "{$day} {$month} {$year}";
            } catch (\Exception $e) {
                return (string)$date;
            }
        }
        $dob = toKhmerDate($student->date_of_birth ?? null);
        $gender = ($student->gender === 'female' || $student->gender === 'ស្រី') ? 'ស្រី' : 'ប្រុស';
        $pob = $student->address ?: trim(($student->village ?? '') . ' ' . ($student->commune ?? '') . ' ' . ($student->district ?? '') . ' ' . ($student->province ?? ''));
        if (empty($pob) || $pob === '-') $pob = 'សំរោងពូន កោះធំ កណ្ដាល';
        $academicYear = $student->academicYear->name ?? '២០២៥ ~ ២០២៦';
        $academicYearKhmer = toKhmerNum(str_replace(['-', '–'], ' ~ ', $academicYear));
        $today = new \DateTime();
        $currDay = toKhmerNum($today->format('d'));
        $months = ['មករា', 'កុម្ភៈ', 'មីនា', 'មេសា', 'ឧសភា', 'មិថុនា', 'កក្កដា', 'សីហា', 'កញ្ញា', 'តុលា', 'វិច្ឆិកា', 'ធ្នូ'];
        $currMonth = $months[(int)$today->format('m') - 1];
        $currYear = toKhmerNum($today->format('Y'));
    @endphp

    <!-- ID Card Container (Official MoEYS Vertical Portrait) -->
    <div 
        id="student-id-card-element" 
        class="relative w-[390px] bg-white border-[3px] border-[#1f4e79] text-black shadow-2xl select-none font-battambang overflow-hidden"
    >
        <!-- Top Header (with bottom border connecting edge-to-edge) -->
        <div class="px-4 pt-3.5 pb-2.5 border-b-[3px] border-[#1f4e79] relative z-10 bg-white">
            <!-- Top-left Logo + Top-center Kingdom Heading -->
            <div class="flex items-start justify-between gap-2">
                <!-- MoEYS Logo cleanly placed without square border box -->
                <div class="w-16 h-16 shrink-0 flex items-center justify-center">
                    <img src="/images/moeys-logo.png" alt="MoEYS Logo" class="w-15 h-15 object-contain" />
                </div>

                <!-- Top-center Kingdom Header -->
                <div class="text-center flex-1 pr-1">
                    <h4 class="font-moul text-[14px] text-black tracking-wide leading-tight">ព្រះរាជាណាចក្រកម្ពុជា</h4>
                    <h5 class="font-moul text-[12px] text-black tracking-wide leading-tight mt-1">ជាតិ សាសនា ព្រះមហាក្សត្រ</h5>
                    <!-- Decorative Khmer Divider (៚ / ༺༻) -->
                    <div class="flex items-center justify-center gap-1.5 my-1">
                        <span class="h-[1px] w-9 bg-[#1f4e79]"></span>
                        <span class="text-[10px] text-[#1f4e79] leading-none">༺ ៚ ༻</span>
                        <span class="h-[1px] w-9 bg-[#1f4e79]"></span>
                    </div>
                </div>
            </div>

            <!-- Bottom of Header: Centered in blue Moul font -->
            <div class="mt-1.5 text-center">
                <p class="font-moul text-[12.5px] text-[#1f4e79] leading-snug tracking-tight">
                    មន្ទីរអប់រំ យុវជន និង កីឡា រាជធានីភ្នំពេញ
                </p>
            </div>
        </div>

        <!-- Main Card Body Section -->
        <div class="px-4.5 pt-3 pb-4 relative z-10">
            <!-- Center Background Watermark (MoEYS emblem at subtle ~8% opacity) -->
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-[0.08] z-0 overflow-hidden">
                <img src="/images/moeys-logo.png" alt="MoEYS Watermark" class="w-68 h-68 object-contain" />
            </div>

            <!-- Title Block -->
            <div class="relative z-10 text-center my-1.5">
                <h2 class="card-main-title text-[23px] tracking-wide leading-tight">
                    ប័ណ្ណសម្គាល់ខ្លួនសិស្ស
                </h2>
                <p class="font-bold text-[13px] text-black mt-1.5 font-battambang">
                    ឆ្នាំសិក្សា {{ $academicYearKhmer }}
                </p>
                <p class="font-moul text-[13.5px] text-black mt-0.5">
                    ថ្នាក់ទី {{ toKhmerNum($student->grade ?? '៧A') }}
                </p>
            </div>

            <!-- Student Details: Sized large, bold and clearly legible matching reference -->
            <div class="relative z-10 mt-3 text-[13.5px] space-y-2 leading-relaxed text-black font-battambang">
                <!-- Name & Gender (Two Columns balanced) -->
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-1.5 min-w-0">
                        <span class="font-bold text-black shrink-0">គោត្តនាម នាម :</span>
                        <span class="font-bold text-[#154689] text-[14.5px] truncate">{{ $student->name_khmer ?? $student->user->name ?? 'សុខ សុផាវត្តី' }}</span>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0 pr-1">
                        <span class="font-bold text-black">ភេទ :</span>
                        <span class="font-bold text-[#154689] text-[14px]">{{ $gender }}</span>
                    </div>
                </div>

                <!-- Date of Birth (Single Line) -->
                <div class="flex items-center gap-1.5">
                    <span class="font-bold text-black shrink-0">ថ្ងៃខែឆ្នាំកំណើត :</span>
                    <span class="font-bold text-[#154689] text-[14px]">{{ $dob }}</span>
                </div>

                <!-- Place of Birth (Single Line) -->
                <div class="flex items-start gap-1.5">
                    <span class="font-bold text-black shrink-0">ទីកន្លែងកំណើត :</span>
                    <span class="font-bold text-[#154689] text-[14px] truncate">{{ $pob }}</span>
                </div>

                <!-- Father & Mother Names (Two Columns balanced) -->
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-1.5 min-w-0">
                        <span class="font-bold text-black shrink-0">ឪពុកឈ្មោះ :</span>
                        <span class="font-bold text-[#154689] text-[14px] truncate">{{ $student->father_name ?? 'ភិន សុខ' }}</span>
                    </div>
                    <div class="flex items-center gap-1.5 shrink-0 pr-1">
                        <span class="font-bold text-black shrink-0">ម្ដាយឈ្មោះ :</span>
                        <span class="font-bold text-[#154689] text-[14px] truncate">{{ $student->mother_name ?? 'ដួង ស្រេង' }}</span>
                    </div>
                </div>
            </div>

            <!-- Bottom Section (Left: Photo Container | Right: School Affiliation, Dates, Principal & Stamp) -->
            <div class="relative z-10 mt-3 sm:mt-3.5 flex items-end justify-between gap-2 sm:gap-3">
                <!-- Left Side: Rectangular Photo Container with thin 1px black border -->
                <div class="border border-black bg-white shrink-0 w-[102px] sm:w-[112px] shadow-2xs">
                    <!-- Top Header Bar with ID in blue separated by 1px solid black line -->
                    <div class="border-b border-black bg-white text-center py-0.5 sm:py-1 px-0.5 text-[8.5px] sm:text-[9.5px] font-mono font-bold text-[#154689] tracking-tight whitespace-nowrap overflow-hidden">
                        ID: {{ $student->student_id_card ?? '0011001' }}
                    </div>
                    <!-- Photo Area: Empty clean framed box with subtle 3x4 indicator -->
                    <div class="w-full h-32 sm:h-36 bg-[#f4f7fb] flex items-center justify-center text-[11px] text-slate-400 font-sans tracking-widest font-semibold select-none">
                        3 x 4
                    </div>
                </div>

                <!-- Right Side: Affiliation, Dates, Director, Stamp & Signature -->
                <div class="flex-1 flex flex-col items-center text-center text-black pb-0.5 min-w-0">
                    <!-- Top Line: 'ជាសិស្ស' (black) followed by School Name in green Moul font (#008000) -->
                    <div class="text-center mb-1 w-full flex items-center justify-center gap-1 sm:gap-1.5 whitespace-nowrap">
                        <span class="font-bold text-black text-[11px] sm:text-[12.5px] shrink-0 whitespace-nowrap">ជាសិស្ស</span>
                        <span class="font-moul text-[#008000] text-[10.5px] sm:text-[12.5px] tracking-tight shrink-0 whitespace-nowrap">សាលារៀន ជំនាន់ថ្មី</span>
                    </div>

                    <!-- Lunar Date & Gregorian Date in clear text -->
                    <p class="text-[8.5px] sm:text-[9.5px] leading-tight text-slate-900 font-battambang whitespace-nowrap">
                        ថ្ងៃសុក្រ ១០រោច ខែចេត្រ ឆ្នាំរោង ឆស័ក ព.ស.២៥៦៨
                    </p>
                    <p class="font-bold text-[9px] sm:text-[10.5px] leading-tight text-slate-900 mt-0.5 font-battambang whitespace-nowrap">
                        រាជធានីភ្នំពេញ ថ្ងៃទី {{ $currDay }} ខែ {{ $currMonth }} ឆ្នាំ {{ $currYear }}
                    </p>

                    <!-- Director Title in Moul font -->
                    <p class="font-moul text-[11px] sm:text-[12.5px] text-slate-900 mt-0.5 sm:mt-1 whitespace-nowrap">
                        នាយកសាលា
                    </p>

                    <!-- Circular Red School Stamp and Blue Signature (Authentic Official Circular Seal) -->
                    <div class="relative w-26 h-17 sm:w-30 sm:h-20 my-0.5 flex items-center justify-center">
                        <!-- Red Official Circular Stamp -->
                        <svg class="absolute inset-0 w-22 h-22 sm:w-26 sm:h-26 text-red-600/90 pointer-events-none transform -rotate-6 mx-auto -top-2.5 sm:-top-3" viewBox="0 0 100 100">
                            <circle cx="50" cy="50" r="46" fill="none" stroke="currentColor" stroke-width="1.8" />
                            <circle cx="50" cy="50" r="42" fill="none" stroke="currentColor" stroke-width="1" />
                            <path id="curve-top-blade" d="M 16,50 A 34,34 0 0,1 84,50" fill="none" />
                            <path id="curve-bottom-blade" d="M 84,50 A 34,34 0 0,1 16,50" fill="none" />
                            <text font-size="5.8" font-family="'Battambang', sans-serif" font-weight="bold" fill="currentColor">
                                <textPath href="#curve-top-blade" startOffset="50%" text-anchor="middle">
                                    ★ សាលារៀន ជំនាន់ថ្មី ★
                                </textPath>
                            </text>
                            <text font-size="5.2" font-family="'Battambang', sans-serif" font-weight="bold" fill="currentColor">
                                <textPath href="#curve-bottom-blade" startOffset="50%" text-anchor="middle">
                                    ★ ក្រសួងអប់រំ យុវជន និងកីឡា ★
                                </textPath>
                            </text>
                            <circle cx="50" cy="50" r="21" fill="none" stroke="currentColor" stroke-width="0.8" />
                            <!-- Center Seal Emblem -->
                            <polygon points="50,35 53,43 61,43 55,49 57,57 50,52 43,57 45,49 39,43 47,43" fill="currentColor" opacity="0.85" />
                        </svg>
                        
                        <!-- Blue Ink Cursive Signature (smooth realistic pen stroke) -->
                        <svg class="absolute inset-0 w-24 h-15 sm:w-28 sm:h-18 text-[#002b80] pointer-events-none transform rotate-2 mx-auto -top-1" viewBox="0 0 100 60">
                            <path d="M 16,36 C 24,18 34,44 42,26 C 48,16 54,36 62,22 C 70,16 80,32 86,20 M 22,33 L 84,29" fill="none" stroke="#002b80" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>

                    <!-- Director's Name in Red Moul font #d92626 -->
                    <p class="font-moul text-[11px] sm:text-[12.5px] text-[#d92626] font-bold tracking-tight mt-0.5 whitespace-nowrap">
                        សុខ ប៊ុនហុង
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
