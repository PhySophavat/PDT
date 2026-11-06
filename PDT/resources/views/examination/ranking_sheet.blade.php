<!DOCTYPE html>
<html lang="km">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>តារាងចំណាត់ថ្នាក់ - ថ្នាក់ទី {{ $className }} - {{ $examination->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;500;600;700;800&family=Moul&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kantumruy Pro', sans-serif;
            background: #f1f5f9;
            color: #0f172a;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .font-moul { font-family: 'Moul', serif; }
        
        @page {
            size: A4 portrait;
            margin: 6mm 7mm 6mm 7mm;
        }

        @media print {
            .no-print { display: none !important; }
            body { 
                background: white !important; 
                padding: 0 !important; 
                margin: 0 !important;
            }
            .sheet-container {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
            .editable-field {
                border-bottom: none !important;
                background-color: transparent !important;
                outline: none !important;
            }
        }

        /* Interactive Editable Fields */
        .editable-field {
            cursor: pointer;
            border-bottom: 1px dashed rgba(59, 130, 246, 0.4);
            padding: 1px 3px;
            border-radius: 3px;
            transition: all 0.15s ease-in-out;
        }
        .editable-field:hover {
            background-color: rgba(254, 240, 138, 0.4);
            border-bottom-color: #2563eb;
        }
        .editable-field:focus {
            background-color: #ffffff;
            outline: 2px solid #3b82f6;
            border-bottom-color: transparent;
        }

        /* Compact Table Styling for Cambodian Official Documents */
        table.ranking-table {
            border-collapse: collapse;
            width: 100%;
            font-size: 11px;
            line-height: 1.25;
        }
        table.ranking-table th, table.ranking-table td {
            border: 1px solid #334155;
            padding: 2.2px 3px;
        }
        table.ranking-table th {
            background-color: #f8fafc;
            font-weight: 700;
            text-align: center;
        }
        .rank-num {
            color: #b91c1c; /* Official Cambodian Red Rank */
            font-weight: 700;
            text-align: center;
        }
    </style>
</head>
<body class="p-3 md:p-6 flex flex-col items-center justify-start min-h-screen">

    <!-- Top Action & Customization Toolbar (Hidden on Print) -->
    <div class="no-print mb-4 w-full max-w-[840px] space-y-3">
        <!-- Main Bar -->
        <div class="flex flex-wrap items-center justify-between gap-3 bg-white px-5 py-3 rounded-2xl shadow-sm border border-slate-200">
            <div class="flex items-center gap-3">
                <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
                <div>
                    <span class="text-xs font-bold text-slate-800">ទម្រង់តារាងចំណាត់ថ្នាក់ប្រចាំខែផ្លូវការ (A4 Portrait)</span>
                    <span class="text-[11px] text-slate-500 block">អ្នកអាចចុចលើអត្ថបទផ្ទាល់ ឬប្រើប្រអប់ខាងក្រោមដើម្បីកែឈ្មោះគ្រូ នាយក និងស្ថាប័ន</span>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="toggleEditPanel()" class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition flex items-center gap-1.5">
                    <i class="fa-solid fa-sliders text-blue-600"></i>
                    <span>កំណត់ព័ត៌មាន</span>
                </button>
                <button type="button" onclick="window.print()" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition flex items-center gap-2">
                    <i class="fa-solid fa-print"></i>
                    <span>បោះពុម្ព / Download PDF</span>
                </button>
                <button type="button" onclick="window.close()" class="px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        <!-- Expandable Settings Panel -->
        <div id="settingsPanel" class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 transition-all duration-200">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-user-pen text-blue-600"></i>
                    <h3 class="text-xs font-bold text-slate-800">កែសម្រួលឈ្មោះ គ្រូទទួលបន្ទុក នាយកសាលា និងស្ថាប័ន</h3>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="saveToSystem()" id="btnSaveSystem" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>រក្សាទុកជាលំនាំដើមក្នុងប្រព័ន្ធ</span>
                    </button>
                    <button type="button" onclick="resetToDefaults()" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition">
                        កំណត់ដើមវិញ
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 text-xs">
                <div>
                    <label class="block font-bold text-slate-700 mb-1">ឈ្មោះគ្រូទទួលបន្ទុកថ្នាក់</label>
                    <div class="relative">
                        <input type="text" id="inputTeacher" oninput="syncField('teacher', this.value)" value="{{ $teacherName }}" class="w-full pl-8 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 font-medium">
                        <i class="fa-solid fa-chalkboard-user absolute left-3 top-2.5 text-slate-400"></i>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">ឈ្មោះនាយកសាលា</label>
                    <div class="relative">
                        <input type="text" id="inputPrincipal" oninput="syncField('principal', this.value)" value="{{ $principalName }}" class="w-full pl-8 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 font-medium">
                        <i class="fa-solid fa-user-tie absolute left-3 top-2.5 text-slate-400"></i>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">កាលបរិច្ឆេទលើឯកសារ</label>
                    <div class="relative">
                        <input type="text" id="inputDate" oninput="syncField('date', this.value)" value="{{ $sheetDate }}" class="w-full pl-8 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 font-medium">
                        <i class="fa-solid fa-calendar-day absolute left-3 top-2.5 text-slate-400"></i>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">ការិយាល័យអប់រំស្រុក / ក្រុង</label>
                    <div class="relative">
                        <input type="text" id="inputDistrict" oninput="syncField('district', this.value)" value="{{ $districtOffice }}" class="w-full pl-8 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 font-medium">
                        <i class="fa-solid fa-building-columns absolute left-3 top-2.5 text-slate-400"></i>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">ឈ្មោះវិទ្យាល័យ / អនុវិទ្យាល័យ</label>
                    <div class="relative">
                        <input type="text" id="inputSchool" oninput="syncField('school', this.value)" value="{{ $schoolName }}" class="w-full pl-8 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 font-medium">
                        <i class="fa-solid fa-school absolute left-3 top-2.5 text-slate-400"></i>
                    </div>
                </div>

                <div>
                    <label class="block font-bold text-slate-700 mb-1">ឆ្នាំសិក្សា</label>
                    <div class="relative">
                        <input type="text" id="inputAcademicYear" oninput="syncField('academicYear', this.value)" value="{{ $academicYearName }}" class="w-full pl-8 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20 font-medium">
                        <i class="fa-solid fa-graduation-cap absolute left-3 top-2.5 text-slate-400"></i>
                    </div>
                </div>
            </div>
            <div id="saveNotification" class="hidden mt-3 p-2 bg-emerald-50 text-emerald-700 text-xs rounded-lg font-bold text-center border border-emerald-200">
                ✅ បានរក្សាទុកព័ត៌មានជាលំនាំដើមក្នុងប្រព័ន្ធដោយជោគជ័យ!
            </div>
        </div>
    </div>

    <!-- Official Document Sheet (Standard Cambodian High School Layout) -->
    <div class="sheet-container w-full max-w-[840px] bg-white p-7 shadow-lg border border-slate-200 text-slate-900 rounded-sm">
        
        <!-- Document Header -->
        <div class="flex items-start justify-between pb-1">
            <!-- Left Side: MoEYS Logo & District & School -->
            <div class="text-center w-56 flex flex-col items-center">
                <!-- Official MoEYS Logo -->
                <div class="mb-1.5 flex justify-center">
                    <img src="{{ asset('images/moeys-logo.jpg') }}" alt="Logo MoEYS" class="h-16 w-auto object-contain">
                </div>
                <h4 class="font-bold text-[12px] text-slate-800 leading-tight">
                    <span id="textDistrict" contenteditable="true" onblur="handleContentEdit('district', this.innerText)" class="editable-field" title="ចុចដើម្បីកែប្រែការិយាល័យ">{{ $districtOffice }}</span>
                </h4>
                <h3 class="font-bold text-[13px] text-slate-900 leading-tight mt-0.5">
                    <span id="textSchool" contenteditable="true" onblur="handleContentEdit('school', this.innerText)" class="editable-field" title="ចុចដើម្បីកែប្រែឈ្មោះវិទ្យាល័យ">{{ $schoolName }}</span>
                </h3>
            </div>

            <!-- Right Side: Kingdom of Cambodia -->
            <div class="text-center space-y-0.5 pt-1">
                <h2 class="font-moul text-[13px] text-slate-900 tracking-wider">ព្រះរាជាណាចក្រកម្ពុជា</h2>
                <h3 class="font-moul text-[12px] text-slate-900 tracking-wider">ជាតិ សាសនា ព្រះមហាក្សត្រ</h3>
                <!-- Traditional Cambodian flourish / ornament -->
                <div class="text-slate-800 text-sm tracking-[4px] font-bold select-none pt-0.5">
                    ៚ &#9866;&#9866;&#9866; ៚
                </div>
            </div>
        </div>

        <!-- Document Title -->
        <div class="text-center my-2 space-y-0.5">
            <h1 class="font-moul text-[15px] text-blue-900 leading-tight">
                <span id="textTitle" contenteditable="true" class="editable-field" title="ចុចដើម្បីកែប្រែចំណងជើង">តារាងចំណាត់ថ្នាក់ប្រចាំខែ {{ str_contains($examination->title, 'កុម្ភៈ') ? 'កុម្ភៈ' : (str_contains($examination->title, 'មករា') ? 'មករា' : $examination->title) }}</span>
            </h1>
            <p class="font-bold text-[13px] text-blue-800">
                ថ្នាក់ទី {{ $className }} &nbsp;&bull;&nbsp; ឆ្នាំសិក្សា <span id="textAcademicYear" contenteditable="true" onblur="handleContentEdit('academicYear', this.innerText)" class="editable-field" title="ចុចដើម្បីកែប្រែឆ្នាំសិក្សា">{{ $academicYearName }}</span>
            </p>
        </div>

        <!-- Two-Column Side-by-Side Ranking Table -->
        <div class="grid grid-cols-2 gap-2 mt-2">
            <!-- Left Table (Rank 1 to Half) -->
            <div>
                <table class="ranking-table">
                    <thead>
                        <tr>
                            <th style="width: 26px;">ល.រ</th>
                            <th>គោត្តនាម និង នាម</th>
                            <th style="width: 34px;">ភេទ</th>
                            <th style="width: 44px;">ពិន្ទុ</th>
                            <th style="width: 46px;">ម.ភាគ</th>
                            <th style="width: 26px;" class="rank-num">ចំ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($leftRows as $idx => $r)
                        <tr>
                            <td class="text-center font-bold text-slate-600">{{ $idx + 1 }}</td>
                            <td>
                                <div class="flex items-center justify-between px-0.5">
                                    <span class="font-bold text-slate-900 w-14 truncate">{{ $r['surname'] }}</span>
                                    <span class="font-bold text-slate-900 flex-1 pl-1 truncate">{{ $r['firstname'] }}</span>
                                </div>
                            </td>
                            <td class="text-center text-[10px] {{ $r['gender'] === 'ស្រី' ? 'font-bold text-rose-700' : 'text-slate-700' }}">{{ $r['gender'] }}</td>
                            <td class="text-right font-mono font-bold pr-1 text-slate-800">{{ number_format($r['total_score'], 0) }}</td>
                            <td class="text-right font-mono font-bold pr-1 text-blue-900">{{ number_format($r['average'], 2) }}</td>
                            <td class="rank-num font-mono">{{ $r['rank'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Right Table (Rank Half+1 to End) -->
            <div>
                <table class="ranking-table">
                    <thead>
                        <tr>
                            <th style="width: 26px;">ល.រ</th>
                            <th>គោត្តនាម និង នាម</th>
                            <th style="width: 34px;">ភេទ</th>
                            <th style="width: 44px;">ពិន្ទុ</th>
                            <th style="width: 46px;">ម.ភាគ</th>
                            <th style="width: 26px;" class="rank-num">ចំ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rightRows as $idx => $r)
                        <tr>
                            <td class="text-center font-bold text-slate-600">{{ count($leftRows) + $idx + 1 }}</td>
                            <td>
                                <div class="flex items-center justify-between px-0.5">
                                    <span class="font-bold text-slate-900 w-14 truncate">{{ $r['surname'] }}</span>
                                    <span class="font-bold text-slate-900 flex-1 pl-1 truncate">{{ $r['firstname'] }}</span>
                                </div>
                            </td>
                            <td class="text-center text-[10px] {{ $r['gender'] === 'ស្រី' ? 'font-bold text-rose-700' : 'text-slate-700' }}">{{ $r['gender'] }}</td>
                            <td class="text-right font-mono font-bold pr-1 text-slate-800">{{ number_format($r['total_score'], 0) }}</td>
                            <td class="text-right font-mono font-bold pr-1 text-blue-900">{{ number_format($r['average'], 2) }}</td>
                            <td class="rank-num font-mono">{{ $r['rank'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer Statistics and Summary (Exact Cambodian High School Document Style) -->
        <div class="mt-3 pt-1 border-t border-slate-300 text-[11px] leading-relaxed">
            <!-- Row 1: Total Student Count Line & Date -->
            <div class="flex items-center justify-between font-bold text-slate-900 mb-1">
                <div>
                    <span>បញ្ឈប់បញ្ជីត្រឹម <span contenteditable="true" class="editable-field font-mono text-sm text-blue-950" title="ចុចដើម្បីកែប្រែចំនួនសរុប">{{ sprintf('%02d', $totalCount) }}</span> នាក់</span>
                    <span class="ml-4">ស្រី <span contenteditable="true" class="editable-field font-mono text-sm text-rose-700" title="ចុចដើម្បីកែប្រែចំនួនស្រី">{{ sprintf('%02d', $femaleCount) }}</span> នាក់</span>
                </div>
                <div class="text-xs text-slate-800">
                    <span id="textDate" contenteditable="true" onblur="handleContentEdit('date', this.innerText)" class="editable-field font-semibold" title="ចុចដើម្បីកែប្រែកាលបរិច្ឆេទ">{{ $sheetDate }}</span>
                </div>
            </div>

            <!-- Row 2: Grade Breakdown Table and Signatures -->
            <div class="grid grid-cols-12 gap-2 items-start mt-2">
                <!-- Left Block: Approvals & Stamp -->
                <div class="col-span-4 text-center">
                    <p class="font-bold text-slate-800 text-[11px]">បានឃើញ និងឯកភាព</p>
                    <p class="font-moul text-slate-900 text-[12px] mt-0.5">នាយកសាលា</p>
                    <div class="h-16 flex items-center justify-center">
                        <!-- Simulated Stamp Space -->
                        <div class="w-16 h-16 rounded-full border border-dashed border-rose-300 flex items-center justify-center text-rose-400 text-[9px] select-none">
                            (ត្រាសាលា)
                        </div>
                    </div>
                    <p class="font-bold text-slate-900 text-[13px] tracking-wide">
                        <span id="textPrincipal" contenteditable="true" onblur="handleContentEdit('principal', this.innerText)" class="editable-field" title="ចុចដើម្បីកែប្រែឈ្មោះនាយក">{{ $principalName }}</span>
                    </p>
                </div>

                <!-- Center Block: Official Grade Distribution -->
                <div class="col-span-4 text-[10px] space-y-0.5 px-2 border-x border-slate-200">
                    <div class="flex justify-between items-center py-0.5 border-b border-slate-100">
                        <span class="font-bold">និទ្ទេស A: <span contenteditable="true" class="editable-field font-mono font-bold" title="ចុចដើម្បីកែ">{{ sprintf('%02d', $gradeStats['A']['count']) }}</span> នាក់</span>
                        <span>ស្រី <span contenteditable="true" class="editable-field font-mono" title="ចុចដើម្បីកែ">{{ sprintf('%02d', $gradeStats['A']['female']) }}</span> នាក់</span>
                        <span class="font-mono font-bold"><span contenteditable="true" class="editable-field" title="ចុចដើម្បីកែ">{{ $gradeStats['A']['pct'] }}</span>%</span>
                    </div>
                    <div class="flex justify-between items-center py-0.5 border-b border-slate-100">
                        <span class="font-bold">និទ្ទេស B: <span contenteditable="true" class="editable-field font-mono font-bold" title="ចុចដើម្បីកែ">{{ sprintf('%02d', $gradeStats['B']['count']) }}</span> នាក់</span>
                        <span>ស្រី <span contenteditable="true" class="editable-field font-mono" title="ចុចដើម្បីកែ">{{ sprintf('%02d', $gradeStats['B']['female']) }}</span> នាក់</span>
                        <span class="font-mono font-bold"><span contenteditable="true" class="editable-field" title="ចុចដើម្បីកែ">{{ $gradeStats['B']['pct'] }}</span>%</span>
                    </div>
                    <div class="flex justify-between items-center py-0.5 border-b border-slate-100">
                        <span class="font-bold">និទ្ទេស C: <span contenteditable="true" class="editable-field font-mono font-bold" title="ចុចដើម្បីកែ">{{ sprintf('%02d', $gradeStats['C']['count']) }}</span> នាក់</span>
                        <span>ស្រី <span contenteditable="true" class="editable-field font-mono" title="ចុចដើម្បីកែ">{{ sprintf('%02d', $gradeStats['C']['female']) }}</span> នាក់</span>
                        <span class="font-mono font-bold"><span contenteditable="true" class="editable-field" title="ចុចដើម្បីកែ">{{ $gradeStats['C']['pct'] }}</span>%</span>
                    </div>
                    <div class="flex justify-between items-center py-0.5 border-b border-slate-100">
                        <span class="font-bold">និទ្ទេស D: <span contenteditable="true" class="editable-field font-mono font-bold" title="ចុចដើម្បីកែ">{{ sprintf('%02d', $gradeStats['D']['count']) }}</span> នាក់</span>
                        <span>ស្រី <span contenteditable="true" class="editable-field font-mono" title="ចុចដើម្បីកែ">{{ sprintf('%02d', $gradeStats['D']['female']) }}</span> នាក់</span>
                        <span class="font-mono font-bold"><span contenteditable="true" class="editable-field" title="ចុចដើម្បីកែ">{{ $gradeStats['D']['pct'] }}</span>%</span>
                    </div>
                    <div class="flex justify-between items-center py-0.5 border-b border-slate-100">
                        <span class="font-bold">និទ្ទេស E: <span contenteditable="true" class="editable-field font-mono font-bold" title="ចុចដើម្បីកែ">{{ sprintf('%02d', $gradeStats['E']['count']) }}</span> នាក់</span>
                        <span>ស្រី <span contenteditable="true" class="editable-field font-mono" title="ចុចដើម្បីកែ">{{ sprintf('%02d', $gradeStats['E']['female']) }}</span> នាក់</span>
                        <span class="font-mono font-bold"><span contenteditable="true" class="editable-field" title="ចុចដើម្បីកែ">{{ $gradeStats['E']['pct'] }}</span>%</span>
                    </div>
                    <div class="flex justify-between items-center py-0.5 border-b border-slate-100">
                        <span class="font-bold text-slate-600">និទ្ទេស F: <span contenteditable="true" class="editable-field font-mono" title="ចុចដើម្បីកែ">{{ sprintf('%02d', $gradeStats['F']['count'] ?? 0) }}</span> នាក់</span>
                        <span class="text-slate-600">ស្រី <span contenteditable="true" class="editable-field font-mono" title="ចុចដើម្បីកែ">{{ sprintf('%02d', $gradeStats['F']['female'] ?? 0) }}</span> នាក់</span>
                        <span class="font-mono font-bold text-slate-600"><span contenteditable="true" class="editable-field" title="ចុចដើម្បីកែ">{{ $gradeStats['F']['pct'] ?? '0.00' }}</span>%</span>
                    </div>
                    <div class="flex justify-between items-center py-0.5">
                        <span class="font-bold text-slate-500">បោះបង់: <span contenteditable="true" class="editable-field font-mono" title="ចុចដើម្បីកែ">00</span> នាក់</span>
                        <span class="text-slate-500">ស្រី <span contenteditable="true" class="editable-field font-mono" title="ចុចដើម្បីកែ">00</span> នាក់</span>
                        <span class="font-mono text-slate-400"><span contenteditable="true" class="editable-field" title="ចុចដើម្បីកែ">0.00</span>%</span>
                    </div>
                </div>

                <!-- Right Block: Homeroom Teacher Signature -->
                <div class="col-span-4 text-center">
                    <p class="font-bold text-slate-800 text-[11px]">គ្រូទទួលបន្ទុកថ្នាក់</p>
                    <p class="text-[11px] text-slate-500 mt-0.5">ហត្ថលេខា</p>
                    <div class="h-16 flex items-end justify-center pb-2">
                        <div class="w-28 border-b border-dotted border-slate-400"></div>
                    </div>
                    <p class="font-bold text-slate-900 text-[13px] tracking-wide">
                        <span id="textTeacher" contenteditable="true" onblur="handleContentEdit('teacher', this.innerText)" class="editable-field" title="ចុចដើម្បីកែប្រែឈ្មោះគ្រូទទួលបន្ទុក">{{ $teacherName }}</span>
                    </p>
                </div>
            </div>
        </div>

    </div>

    <!-- Interactive Script for Two-way Editing and Local Storage Persistence -->
    <script>
        const STORAGE_KEY = 'ranking_sheet_settings_{{ $className }}';

        // Load saved settings from LocalStorage if available
        document.addEventListener('DOMContentLoaded', () => {
            const saved = localStorage.getItem(STORAGE_KEY);
            if (saved) {
                try {
                    const data = JSON.parse(saved);
                    if (data.teacher) syncField('teacher', data.teacher);
                    if (data.principal) syncField('principal', data.principal);
                    if (data.district) syncField('district', data.district);
                    if (data.school) syncField('school', data.school);
                    if (data.academicYear) syncField('academicYear', data.academicYear);
                    if (data.date) syncField('date', data.date);
                } catch(e) {
                    console.error('Failed to parse localStorage settings', e);
                }
            }
        });

        // Two-way synchronization between input panel and document
        function syncField(field, value) {
            const textMap = {
                teacher: 'textTeacher',
                principal: 'textPrincipal',
                district: 'textDistrict',
                school: 'textSchool',
                academicYear: 'textAcademicYear',
                date: 'textDate'
            };
            const inputMap = {
                teacher: 'inputTeacher',
                principal: 'inputPrincipal',
                district: 'inputDistrict',
                school: 'inputSchool',
                academicYear: 'inputAcademicYear',
                date: 'inputDate'
            };

            const textEl = document.getElementById(textMap[field]);
            const inputEl = document.getElementById(inputMap[field]);

            if (textEl && textEl.innerText !== value) {
                textEl.innerText = value;
            }
            if (inputEl && inputEl.value !== value) {
                inputEl.value = value;
            }

            saveToLocalStorage();
        }

        // When user edits directly on the document text
        function handleContentEdit(field, value) {
            syncField(field, value.trim());
        }

        function toggleEditPanel() {
            const p = document.getElementById('settingsPanel');
            p.classList.toggle('hidden');
        }

        function saveToLocalStorage() {
            const currentData = {
                teacher: document.getElementById('inputTeacher')?.value || '',
                principal: document.getElementById('inputPrincipal')?.value || '',
                district: document.getElementById('inputDistrict')?.value || '',
                school: document.getElementById('inputSchool')?.value || '',
                academicYear: document.getElementById('inputAcademicYear')?.value || '',
                date: document.getElementById('inputDate')?.value || '',
            };
            localStorage.setItem(STORAGE_KEY, JSON.stringify(currentData));
        }

        function resetToDefaults() {
            localStorage.removeItem(STORAGE_KEY);
            window.location.reload();
        }

        async function saveToSystem() {
            const btn = document.getElementById('btnSaveSystem');
            const note = document.getElementById('saveNotification');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> កំពុងរក្សាទុក...';

            const payload = {
                _token: '{{ csrf_token() }}',
                class_name: '{{ $className }}',
                teacher_name: document.getElementById('inputTeacher')?.value || '',
                principal_name: document.getElementById('inputPrincipal')?.value || '',
                district_office: document.getElementById('inputDistrict')?.value || '',
                school_name: document.getElementById('inputSchool')?.value || '',
            };

            try {
                const res = await fetch('{{ route('examinations.ranking-sheet.settings') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(payload)
                });
                const data = await res.json();
                if (data.status === 'success') {
                    note.classList.remove('hidden');
                    setTimeout(() => note.classList.add('hidden'), 4000);
                }
            } catch(e) {
                alert('មានបញ្ហាក្នុងការរក្សាទុក សូមព្យាយាមម្តងទៀត');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-floppy-disk"></i> រក្សាទុកជាលំនាំដើមក្នុងប្រព័ន្ធ';
            }
        }
    </script>
</body>
</html>
