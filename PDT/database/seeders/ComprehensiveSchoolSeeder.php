<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\CourseSubject;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\SchoolParent;
use App\Models\FeeCategory;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\SchoolExpense;
use App\Models\BookCategory;
use App\Models\Book;
use App\Models\BookBorrow;
use App\Models\Announcement;
use App\Models\SystemSetting;
use App\Models\Attendance;
use App\Models\Examination;
use App\Models\ExaminationScore;
use App\Models\Timetable;
use Carbon\Carbon;

class ComprehensiveSchoolSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Academic Years
        $ayCurrent = AcademicYear::firstOrCreate(
            ['name' => '2025-2026'],
            [
                'start_date' => '2025-10-01',
                'end_date' => '2026-07-31',
                'is_current' => true,
                'status' => 'active',
            ]
        );

        $ayNext = AcademicYear::firstOrCreate(
            ['name' => '2026-2027'],
            [
                'start_date' => '2026-10-01',
                'end_date' => '2027-07-31',
                'is_current' => false,
                'status' => 'inactive',
            ]
        );

        // 2. System Settings
        $settings = [
            'school_name' => 'សាលារៀន ជំនាន់ថ្មី PDT',
            'school_address' => 'មហាវិថីសហព័ន្ធរុស្ស៊ី សង្កាត់ទឹកល្អក់១ ខណ្ឌទួលគោក រាជធានីភ្នំពេញ',
            'school_phone' => '023 880 880 / 012 345 678',
            'school_email' => 'contact@pdt-school.edu.kh',
            'school_website' => 'https://pdt-school.edu.kh',
            'current_academic_year' => '2025-2026',
            'current_semester' => '1',
            'currency' => 'USD',
            'currency_symbol' => '$',
            'grading_system' => 'A:90-100, B:80-89, C:70-79, D:60-69, E:50-59, F:0-49',
        ];

        foreach ($settings as $key => $val) {
            SystemSetting::set($key, $val);
        }

        // 3. Teachers & Students update
        $firstTeacher = Teacher::first();
        if ($firstTeacher) {
            $firstTeacher->update([
                'teacher_id_card' => $firstTeacher->teacher_id_card ?: 'TCH-2026-001',
                'qualification' => 'បរិញ្ញាបត្រជាន់ខ្ពស់គរុកោសល្យ',
                'specialization' => 'គណិតវិទ្យា',
                'joining_date' => '2023-10-01',
                'salary' => 650.00,
                'status' => 'active',
                'gender' => $firstTeacher->gender ?: 'ប្រុស',
                'class_assigned' => '7A,7B',
            ]);
        }

        // 4. Classes (7A-7H to 12A-12H)
        $grades = [7, 8, 9, 10, 11, 12];
        $sections = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];

        foreach ($grades as $g) {
            foreach ($sections as $s) {
                $name = $g . $s;
                SchoolClass::firstOrCreate(
                    ['name' => $name],
                    [
                        'grade' => (string)$g,
                        'section' => $s,
                        'room_number' => 'B-' . $g . '0' . (ord($s) - 64),
                        'capacity' => 40,
                        'teacher_id' => $firstTeacher ? $firstTeacher->id : null,
                        'academic_year_id' => $ayCurrent->id,
                        'description' => "ថ្នាក់រៀនទី {$g} បន្ទប់ {$s}",
                    ]
                );
            }
        }

        // Update Students
        $students = Student::all();
        $counter = 1;
        foreach ($students as $stu) {
            $stu->update([
                'student_id_card' => $stu->student_id_card ?: sprintf('STU-2026-%03d', $counter++),
                'status' => $stu->status ?: 'active',
                'academic_year_id' => $ayCurrent->id,
                'enrollment_date' => $stu->enrollment_date ?: '2025-10-01',
                'emergency_contact' => $stu->emergency_contact ?: '012 999 888',
                'grade' => $stu->grade ?: '7A',
            ]);
        }

        // 5. Course Subjects
        $subjectsData = [
            ['code' => 'KHM-01', 'name' => 'ភាសាខ្មែរ', 'credit_hours' => 4, 'grade_level' => '7-12'],
            ['code' => 'MTH-01', 'name' => 'គណិតវិទ្យា', 'credit_hours' => 5, 'grade_level' => '7-12'],
            ['code' => 'ENG-01', 'name' => 'ភាសាអង់គ្លេស', 'credit_hours' => 3, 'grade_level' => '7-12'],
            ['code' => 'PHY-01', 'name' => 'រូបវិទ្យា', 'credit_hours' => 3, 'grade_level' => '7-12'],
            ['code' => 'CHM-01', 'name' => 'គីមីវិទ្យា', 'credit_hours' => 3, 'grade_level' => '7-12'],
            ['code' => 'BIO-01', 'name' => 'ជីវវិទ្យា', 'credit_hours' => 3, 'grade_level' => '7-12'],
            ['code' => 'HIS-01', 'name' => 'ប្រវត្តិវិទ្យា', 'credit_hours' => 2, 'grade_level' => '7-12'],
            ['code' => 'GEO-01', 'name' => 'ភូមិវិទ្យា', 'credit_hours' => 2, 'grade_level' => '7-12'],
            ['code' => 'ETH-01', 'name' => 'សីលធម៌-ពលរដ្ឋ', 'credit_hours' => 2, 'grade_level' => '7-12'],
            ['code' => 'SPO-01', 'name' => 'អប់រំកាយ និងកីឡា', 'credit_hours' => 1, 'grade_level' => '7-12'],
            ['code' => 'ICT-01', 'name' => 'ព័ត៌មានវិទ្យា', 'credit_hours' => 2, 'grade_level' => '7-12'],
        ];

        foreach ($subjectsData as $s) {
            CourseSubject::firstOrCreate(
                ['code' => $s['code']],
                [
                    'name' => $s['name'],
                    'credit_hours' => $s['credit_hours'],
                    'grade_level' => $s['grade_level'],
                    'description' => 'មុខវិជ្ជាគោលកម្មវិធីសិក្សារបស់ក្រសួងអប់រំ យុវជន និងកីឡា',
                ]
            );
        }

        // 6. Fee Categories
        $feeCats = [
            ['name' => 'ថ្លៃសិក្សាប្រចាំឆមាស', 'default_amount' => 350.00, 'description' => 'ការបង់ថ្លៃសិក្សាសម្រាប់ឆមាសនីមួយៗ'],
            ['name' => 'ថ្លៃចុះឈ្មោះចូលរៀន', 'default_amount' => 50.00, 'description' => 'ការបង់ថ្លៃចុះឈ្មោះដំបូង'],
            ['name' => 'ថ្លៃសម្ភារៈសិក្សា និងឯកសណ្ឋាន', 'default_amount' => 80.00, 'description' => 'សៀវភៅពុម្ព កាតសិស្ស និងឯកសណ្ឋាន'],
            ['name' => 'សេវារថយន្តដឹកជញ្ជូន', 'default_amount' => 120.00, 'description' => 'សេវាដឹកជញ្ជូនសិស្សប្រចាំខែ'],
        ];

        foreach ($feeCats as $fc) {
            FeeCategory::firstOrCreate(
                ['name' => $fc['name']],
                [
                    'default_amount' => $fc['default_amount'],
                    'description' => $fc['description'],
                ]
            );
        }

        // 7. Generate Invoices & Payments for students
        $tuitionCat = FeeCategory::where('name', 'ថ្លៃសិក្សាប្រចាំឆមាស')->first();
        if ($tuitionCat && $students->count() > 0) {
            foreach ($students as $idx => $stu) {
                $invNum = sprintf('INV-2026-%04d', $idx + 1);
                $isPaid = ($idx === 0);
                $isPartial = ($idx === 1);
                $paidAmt = $isPaid ? 350.00 : ($isPartial ? 150.00 : 0.00);
                $status = $isPaid ? 'paid' : ($isPartial ? 'partial' : 'unpaid');

                $inv = Invoice::firstOrCreate(
                    ['invoice_number' => $invNum],
                    [
                        'student_id' => $stu->id,
                        'fee_category_id' => $tuitionCat->id,
                        'title' => 'ថ្លៃសិក្សាឆមាសទី ១ (២០២៥-២០២៦)',
                        'total_amount' => 350.00,
                        'paid_amount' => $paidAmt,
                        'due_date' => Carbon::now()->addDays(15),
                        'status' => $status,
                        'currency' => 'USD',
                    ]
                );

                if ($paidAmt > 0) {
                    Payment::firstOrCreate(
                        ['receipt_number' => sprintf('RCP-2026-%04d', $idx + 1)],
                        [
                            'invoice_id' => $inv->id,
                            'amount' => $paidAmt,
                            'payment_method' => $idx === 0 ? 'bank_transfer' : 'cash',
                            'payment_date' => Carbon::now()->subDays(2),
                            'note' => 'បានទូទាត់ជោគជ័យ',
                        ]
                    );
                }
            }
        }

        // 8. School Expenses
        $expenses = [
            ['title' => 'ប្រាក់បៀវត្សរ៍គ្រូ និងបុគ្គលិក', 'category' => 'ប្រាក់ខែ', 'amount' => 1200.00, 'expense_date' => Carbon::now()->subDays(5)],
            ['title' => 'ថ្លៃប្រើប្រាស់ទឹក និងអគ្គិសនី', 'category' => 'ទឹកភ្លើង', 'amount' => 180.00, 'expense_date' => Carbon::now()->subDays(3)],
            ['title' => 'ទិញសម្ភារៈការិយាល័យ និងក្រដាសប្រឡង', 'category' => 'សម្ភារៈ', 'amount' => 95.00, 'expense_date' => Carbon::now()->subDays(1)],
        ];

        foreach ($expenses as $exp) {
            SchoolExpense::firstOrCreate(
                ['title' => $exp['title'], 'expense_date' => $exp['expense_date']],
                [
                    'category' => $exp['category'],
                    'amount' => $exp['amount'],
                    'description' => 'ចំណាយប្រតិបត្តិការសាលា',
                ]
            );
        }

        // 9. Parents
        $parent1 = SchoolParent::firstOrCreate(
            ['name' => 'សុខ ជា'],
            [
                'phone' => '012 888 777',
                'email' => 'sokchea@example.com',
                'occupation' => 'អាជីវករ',
                'address' => 'រាជធានីភ្នំពេញ',
                'relationship' => 'ឪពុក',
            ]
        );

        $parent2 = SchoolParent::firstOrCreate(
            ['name' => 'ម៉ៅ វណ្ណា'],
            [
                'phone' => '098 777 666',
                'email' => 'maovanna@example.com',
                'occupation' => 'មន្ត្រីរាជការ',
                'address' => 'រាជធានីភ្នំពេញ',
                'relationship' => 'ម្តាយ',
            ]
        );

        if ($students->count() >= 2) {
            $parent1->students()->syncWithoutDetaching([$students[0]->id]);
            $parent2->students()->syncWithoutDetaching([$students[1]->id]);
        }

        // 10. Library
        $catTextbook = BookCategory::firstOrCreate(['name' => 'សៀវភៅពុម្ពសិក្សា']);
        $catSci = BookCategory::firstOrCreate(['name' => 'វិទ្យាសាស្ត្រ និងបច្ចេកវិទ្យា']);
        $catLit = BookCategory::firstOrCreate(['name' => 'អក្សរសិល្ប៍ និងប្រវត្តិសាស្ត្រ']);

        $books = [
            ['title' => 'គណិតវិទ្យា ថ្នាក់ទី ៧', 'author' => 'ក្រសួងអប់រំ យុវជន និងកីឡា', 'category_id' => $catTextbook->id, 'isbn' => '978-99950-01', 'quantity' => 50, 'available' => 47],
            ['title' => 'ភាសាខ្មែរ ថ្នាក់ទី ៧', 'author' => 'ក្រសួងអប់រំ យុវជន និងកីឡា', 'category_id' => $catTextbook->id, 'isbn' => '978-99950-02', 'quantity' => 50, 'available' => 48],
            ['title' => 'រូបវិទ្យា ថ្នាក់ទី ៨', 'author' => 'ក្រសួងអប់រំ យុវជន និងកីឡា', 'category_id' => $catTextbook->id, 'isbn' => '978-99950-03', 'quantity' => 45, 'available' => 45],
            ['title' => 'មូលដ្ឋានគ្រឹះកុំព្យូទ័រ និងសរសេរកូដ', 'author' => 'វិទ្យាស្ថានបច្ចេកវិទ្យា', 'category_id' => $catSci->id, 'isbn' => '978-99950-04', 'quantity' => 20, 'available' => 18],
            ['title' => 'រឿងទុំទាវ និងវិភាគអក្សរសិល្ប៍', 'author' => 'ភិក្ខុសោម', 'category_id' => $catLit->id, 'isbn' => '978-99950-05', 'quantity' => 15, 'available' => 15],
        ];

        foreach ($books as $b) {
            Book::firstOrCreate(
                ['isbn' => $b['isbn']],
                [
                    'book_category_id' => $b['category_id'],
                    'title' => $b['title'],
                    'author' => $b['author'],
                    'publisher' => 'រោងពុម្ពអប់រំ',
                    'quantity' => $b['quantity'],
                    'available_copies' => $b['available'],
                ]
            );
        }

        // 11. Announcements
        $announcements = [
            [
                'title' => 'ការបើកបវេសនកាលឆ្នាំសិក្សា ២០២៥-២០២៦',
                'content' => 'សាលារៀនសូមជម្រាបជូនដំណឹងដល់លោកគ្រូ អ្នកគ្រូ សិស្សានុសិស្ស និងមាតាបិតាទាំងអស់ឲ្យបានជ្រាបថា ការបើកបវេសនកាលនឹងចាប់ផ្តើមជាផ្លូវការនៅថ្ងៃទី ០១ ខែតុលា ខាងមុខនេះ។',
                'category' => 'ព្រឹត្តិការណ៍',
                'target_audience' => 'all',
                'publish_date' => Carbon::now()->subDays(10),
                'is_active' => true,
            ],
            [
                'title' => 'កាលវិភាគប្រឡងឆមាសទី ១',
                'content' => 'កាលវិភាគសម្រាប់ការប្រឡងឆមាសទី ១ នឹងត្រូវរៀបចំឡើងចាប់ពីថ្ងៃទី ១៥ ដល់ថ្ងៃទី ២០ ខាងមុខ។ សូមសិស្សានុសិស្សត្រៀមខ្លួនឲ្យបានល្អ។',
                'category' => 'ការប្រឡង',
                'target_audience' => 'students',
                'publish_date' => Carbon::now()->subDays(2),
                'is_active' => true,
            ],
            [
                'title' => 'កិច្ចប្រជុំមាតាបិតា និងអាណាព្យាបាលសិស្ស',
                'content' => 'សាលានឹងរៀបចំកិច្ចប្រជុំមាតាបិតាសិស្សប្រចាំត្រីមាស ដើម្បីពិភាក្សាអំពីវឌ្ឍនភាពនៃការសិក្សារបស់សិស្សានុសិស្សនៅថ្ងៃអាទិត្យចុងសប្តាហ៍នេះ។',
                'category' => 'ដំណឹង',
                'target_audience' => 'parents',
                'publish_date' => Carbon::now()->subDays(1),
                'is_active' => true,
            ],
        ];

        foreach ($announcements as $an) {
            Announcement::firstOrCreate(
                ['title' => $an['title']],
                $an
            );
        }

        // 12. Examination & Scores
        $exam = Examination::firstOrCreate(
            ['title' => 'ការប្រឡងឆមាសទី ១ (២០២៥-២០២៦)'],
            [
                'exam_type' => 'semester',
                'academic_year_id' => $ayCurrent->id,
                'semester' => 1,
                'start_date' => Carbon::now()->addDays(7),
                'end_date' => Carbon::now()->addDays(12),
                'is_published' => true,
            ]
        );

        $mathSubject = CourseSubject::where('code', 'MTH-01')->first();
        $khmerSubject = CourseSubject::where('code', 'KHM-01')->first();
        $engSubject = CourseSubject::where('code', 'ENG-01')->first();

        foreach ($students as $stu) {
            if ($mathSubject) {
                ExaminationScore::firstOrCreate(
                    [
                        'examination_id' => $exam->id,
                        'student_id' => $stu->id,
                        'course_subject_id' => $mathSubject->id,
                    ],
                    [
                        'class_name' => $stu->grade ?: '7A',
                        'subject_name' => $mathSubject->name,
                        'score' => 88.00,
                        'max_score' => 100.00,
                        'grade_letter' => 'B',
                    ]
                );
            }
            if ($khmerSubject) {
                ExaminationScore::firstOrCreate(
                    [
                        'examination_id' => $exam->id,
                        'student_id' => $stu->id,
                        'course_subject_id' => $khmerSubject->id,
                    ],
                    [
                        'class_name' => $stu->grade ?: '7A',
                        'subject_name' => $khmerSubject->name,
                        'score' => 92.50,
                        'max_score' => 100.00,
                        'grade_letter' => 'A',
                    ]
                );
            }
        }

        // 13. Attendance records (Today and past few days)
        $today = Carbon::today()->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();

        foreach ($students as $idx => $stu) {
            Attendance::firstOrCreate(
                [
                    'student_id' => $stu->id,
                    'date' => $today,
                ],
                [
                    'class_name' => $stu->grade ?: '7A',
                    'status' => $idx === 2 ? 'late' : 'present',
                    'remark' => $idx === 2 ? 'យឺត ១០ នាទី' : 'មកទាន់ម៉ោង',
                    'academic_year_id' => $ayCurrent->id,
                ]
            );

            Attendance::firstOrCreate(
                [
                    'student_id' => $stu->id,
                    'date' => $yesterday,
                ],
                [
                    'class_name' => $stu->grade ?: '7A',
                    'status' => 'present',
                    'remark' => 'មានវត្តមាន',
                    'academic_year_id' => $ayCurrent->id,
                ]
            );
        }

        // 14. Timetable for 7A
        if ($firstTeacher && $mathSubject && $khmerSubject) {
            $ttData = [
                ['day' => 'ច័ន្ទ', 'start' => '07:30', 'end' => '08:30', 'subject' => $mathSubject->id],
                ['day' => 'ច័ន្ទ', 'start' => '08:30', 'end' => '09:30', 'subject' => $khmerSubject->id],
                ['day' => 'អង្គារ', 'start' => '07:30', 'end' => '08:30', 'subject' => $khmerSubject->id],
                ['day' => 'អង្គារ', 'start' => '08:30', 'end' => '09:30', 'subject' => $mathSubject->id],
                ['day' => 'ពុធ', 'start' => '07:30', 'end' => '08:30', 'subject' => $mathSubject->id],
            ];

            foreach ($ttData as $tt) {
                Timetable::firstOrCreate(
                    [
                        'class_name' => '7A',
                        'day_of_week' => $tt['day'],
                        'start_time' => $tt['start'],
                    ],
                    [
                        'course_subject_id' => $tt['subject'],
                        'teacher_id' => $firstTeacher->id,
                        'end_time' => $tt['end'],
                        'room_number' => 'B-701',
                        'academic_year_id' => $ayCurrent->id,
                    ]
                );
            }
        }
    }
}
