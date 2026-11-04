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

        // 3. Teachers (10 Comprehensive Khmer Teachers)
        $teacherRole = \App\Models\Role::firstOrCreate(['name' => 'teacher'], ['description' => 'គ្រូបង្រៀន']);
        $teachersData = [
            ['name' => 'លោក ឈុន វណ្ណា', 'name_latin' => 'Chhun Vanna', 'email' => 'vanna.chhun@example.com', 'subject' => 'គណិតវិទ្យា', 'gender' => 'ប្រុស', 'phone' => '012 345 678', 'salary' => 750, 'classes' => '7A, 7B, 12A', 'specialization' => 'គណិតវិទ្យាកម្រិតខ្ពស់', 'qualification' => 'បរិញ្ញាបត្រជាន់ខ្ពស់គរុកោសល្យ'],
            ['name' => 'អ្នកគ្រូ កែវ សុផល', 'name_latin' => 'Keo Sophal', 'email' => 'sophal.keo@example.com', 'subject' => 'ភាសាខ្មែរ', 'gender' => 'ស្រី', 'phone' => '098 765 432', 'salary' => 650, 'classes' => '7A, 8A, 9A', 'specialization' => 'អក្សរសិល្ប៍ខ្មែរ', 'qualification' => 'បរិញ្ញាបត្រអក្សរសាស្ត្រខ្មែរ'],
            ['name' => 'លោក សេង សុខា', 'name_latin' => 'Seng Sokha', 'email' => 'sokha.seng@example.com', 'subject' => 'ភាសាអង់គ្លេស', 'gender' => 'ប្រុស', 'phone' => '017 888 999', 'salary' => 680, 'classes' => '7A, 7B, 8B', 'specialization' => 'TEFL / ESL Teaching', 'qualification' => 'Bachelor of Education (English)'],
            ['name' => 'លោក អ៊ុំ រតនៈ', 'name_latin' => 'Oum Rattanak', 'email' => 'rattanak.oum@example.com', 'subject' => 'រូបវិទ្យា', 'gender' => 'ប្រុស', 'phone' => '077 222 333', 'salary' => 620, 'classes' => '8A, 9A, 10A', 'specialization' => 'រូបវិទ្យាអនុវត្ត', 'qualification' => 'បរិញ្ញាបត្ររូបវិទ្យា'],
            ['name' => 'អ្នកគ្រូ មាស ចរិយា', 'name_latin' => 'Meas Chariya', 'email' => 'chariya.meas@example.com', 'subject' => 'គីមីវិទ្យា', 'gender' => 'ស្រី', 'phone' => '089 444 555', 'salary' => 630, 'classes' => '9A, 10A, 11A', 'specialization' => 'គីមីវិទ្យាសរីរាង្គ', 'qualification' => 'បរិញ្ញាបត្រគីមីវិទ្យា'],
            ['name' => 'លោក លឹម ប៊ុនធឿន', 'name_latin' => 'Lim Bunthoeun', 'email' => 'bunthoeun.lim@example.com', 'subject' => 'ជីវវិទ្យា', 'gender' => 'ប្រុស', 'phone' => '015 666 777', 'salary' => 600, 'classes' => '8A, 9A, 10A', 'specialization' => 'ជីវវិទ្យា និងបរិស្ថាន', 'qualification' => 'បរិញ្ញាបត្រជីវវិទ្យា'],
            ['name' => 'អ្នកគ្រូ សួន ចិន្តា', 'name_latin' => 'Suon Chenda', 'email' => 'chenda.suon@example.com', 'subject' => 'ប្រវត្តិវិទ្យា', 'gender' => 'ស្រី', 'phone' => '092 111 222', 'salary' => 590, 'classes' => '7A, 8A, 9A', 'specialization' => 'ប្រវត្តិសាស្ត្រអាស៊ីអាគ្នេយ៍', 'qualification' => 'បរិញ្ញាបត្រប្រវត្តិវិទ្យា'],
            ['name' => 'អ្នកគ្រូ ពេជ្រ ពិសី', 'name_latin' => 'Pech Pisey', 'email' => 'pisey.pech@example.com', 'subject' => 'ភូមិវិទ្យា', 'gender' => 'ស្រី', 'phone' => '070 333 444', 'salary' => 590, 'classes' => '7B, 8B, 10A', 'specialization' => 'ភូមិវិទ្យាសេដ្ឋកិច្ច', 'qualification' => 'បរិញ្ញាបត្រភូមិវិទ្យា'],
            ['name' => 'លោក តាំង គីមឡេង', 'name_latin' => 'Tang Kimleng', 'email' => 'kimleng.tang@example.com', 'subject' => 'ព័ត៌មានវិទ្យា', 'gender' => 'ប្រុស', 'phone' => '016 555 666', 'salary' => 700, 'classes' => '7A, 8A, 9A, 10A', 'specialization' => 'Web Development & Python', 'qualification' => 'បរិញ្ញាបត្រវិទ្យាសាស្ត្រកុំព្យូទ័រ'],
            ['name' => 'លោក ម៉ៅ សុវណ្ណារ៉ា', 'name_latin' => 'Mao Sovannara', 'email' => 'sovannara.mao@example.com', 'subject' => 'អប់រំកាយ និងកីឡា', 'gender' => 'ប្រុស', 'phone' => '088 999 111', 'salary' => 580, 'classes' => '7A, 7B, 8A, 8B', 'specialization' => 'កីឡាបាល់ទាត់ និងបាល់ទះ', 'qualification' => 'វិញ្ញាបនបត្រគរុកោសល្យកីឡា'],
        ];

        $createdTeachers = [];
        foreach ($teachersData as $tIdx => $tItem) {
            $u = \App\Models\User::firstOrCreate(
                ['email' => $tItem['email']],
                [
                    'name' => $tItem['name'],
                    'password' => bcrypt('password'),
                ]
            );
            $u->roles()->syncWithoutDetaching([$teacherRole->id]);

            $tch = Teacher::updateOrCreate(
                ['teacher_id_card' => sprintf('TCH-2026-%03d', $tIdx + 1)],
                [
                    'user_id' => $u->id,
                    'name_khmer' => $tItem['name'],
                    'name_latin' => $tItem['name_latin'],
                    'subject' => $tItem['subject'],
                    'gender' => $tItem['gender'],
                    'phone' => $tItem['phone'],
                    'salary' => $tItem['salary'],
                    'class_assigned' => $tItem['classes'],
                    'specialization' => $tItem['specialization'],
                    'qualification' => $tItem['qualification'],
                    'joining_date' => '2023-10-01',
                    'status' => 'active',
                ]
            );
            $createdTeachers[] = $tch;
        }

        $firstTeacher = $createdTeachers[0] ?? null;

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

        // Seed 20 Realistic Khmer Students
        $studentRole = \App\Models\Role::firstOrCreate(['name' => 'student'], ['description' => 'សិស្សានុសិស្ស']);
        $studentsData = [
            ['name' => 'សុខ ពិសិដ្ឋ', 'name_latin' => 'Sok Piseth', 'email' => 'piseth.sok@example.com', 'grade' => '7A', 'gender' => 'ប្រុស', 'phone' => '012 111 222', 'dob' => '2012-05-14', 'parent' => 'សុខ ជា', 'parent_phone' => '012 888 777'],
            ['name' => 'ចាន់ ធីតា', 'name_latin' => 'Chan Thida', 'email' => 'thida.chan@example.com', 'grade' => '7A', 'gender' => 'ស្រី', 'phone' => '098 222 333', 'dob' => '2012-08-20', 'parent' => 'ចាន់ សុផល', 'parent_phone' => '098 777 666'],
            ['name' => 'មាស សុវណ្ណ', 'name_latin' => 'Meas Sovann', 'email' => 'sovann.meas@example.com', 'grade' => '7A', 'gender' => 'ប្រុស', 'phone' => '077 333 444', 'dob' => '2012-01-10', 'parent' => 'មាស រ៉ា', 'parent_phone' => '077 666 555'],
            ['name' => 'គង់ ចិន្តា', 'name_latin' => 'Kong Chenda', 'email' => 'chenda.kong@example.com', 'grade' => '7A', 'gender' => 'ស្រី', 'phone' => '015 444 555', 'dob' => '2012-11-25', 'parent' => 'គង់ សារ៉េត', 'parent_phone' => '015 555 444'],
            ['name' => 'ហេង វិសាល', 'name_latin' => 'Heng Visal', 'email' => 'visal.heng@example.com', 'grade' => '7A', 'gender' => 'ប្រុស', 'phone' => '089 555 666', 'dob' => '2012-03-30', 'parent' => 'ហេង វុទ្ធី', 'parent_phone' => '089 444 333'],
            ['name' => 'កែវ រចនា', 'name_latin' => 'Keo Rachana', 'email' => 'rachana.keo@example.com', 'grade' => '7A', 'gender' => 'ស្រី', 'phone' => '070 666 777', 'dob' => '2012-07-15', 'parent' => 'កែវ សម្បត្តិ', 'parent_phone' => '070 333 222'],
            ['name' => 'លី ដារ៉ា', 'name_latin' => 'Ly Dara', 'email' => 'dara.ly@example.com', 'grade' => '7B', 'gender' => 'ប្រុស', 'phone' => '016 777 888', 'dob' => '2012-09-05', 'parent' => 'លី សេង', 'parent_phone' => '016 222 111'],
            ['name' => 'វ៉ាន់ ស្រីមុំ', 'name_latin' => 'Van Sreymom', 'email' => 'sreymom.van@example.com', 'grade' => '7B', 'gender' => 'ស្រី', 'phone' => '092 888 999', 'dob' => '2012-04-18', 'parent' => 'វ៉ាន់ ធឿន', 'parent_phone' => '092 111 000'],
            ['name' => 'ឃុន វឌ្ឍនា', 'name_latin' => 'Khun Vathana', 'email' => 'vathana.khun@example.com', 'grade' => '7B', 'gender' => 'ប្រុស', 'phone' => '011 999 000', 'dob' => '2012-12-01', 'parent' => 'ឃុន ផល្លា', 'parent_phone' => '011 000 999'],
            ['name' => 'អ៊ុង គីមសួរ', 'name_latin' => 'Ung Kimsour', 'email' => 'kimsour.ung@example.com', 'grade' => '7B', 'gender' => 'ស្រី', 'phone' => '093 111 333', 'dob' => '2012-06-22', 'parent' => 'អ៊ុង វីរៈ', 'parent_phone' => '093 999 888'],
            ['name' => 'សេង បូរ៉ា', 'name_latin' => 'Seng Bora', 'email' => 'bora.seng@example.com', 'grade' => '8A', 'gender' => 'ប្រុស', 'phone' => '012 444 888', 'dob' => '2011-02-17', 'parent' => 'សេង ប៊ុនណា', 'parent_phone' => '012 888 444'],
            ['name' => 'នួន ស្រីណែត', 'name_latin' => 'Nuon Sreynet', 'email' => 'sreynet.nuon@example.com', 'grade' => '8A', 'gender' => 'ស្រី', 'phone' => '098 555 111', 'dob' => '2011-10-08', 'parent' => 'នួន សុខឃីម', 'parent_phone' => '098 111 555'],
            ['name' => 'ជា សុភាព', 'name_latin' => 'Chea Sopheap', 'email' => 'sopheap.chea@example.com', 'grade' => '8A', 'gender' => 'ប្រុស', 'phone' => '077 666 222', 'dob' => '2011-07-29', 'parent' => 'ជា ចំរើន', 'parent_phone' => '077 222 666'],
            ['name' => 'ឌិត មុនី', 'name_latin' => 'Dy Mony', 'email' => 'mony.dy@example.com', 'grade' => '8A', 'gender' => 'ស្រី', 'phone' => '015 777 333', 'dob' => '2011-03-12', 'parent' => 'ឌិត ថាវី', 'parent_phone' => '015 333 777'],
            ['name' => 'អេង សុភ័ក្ត្រ', 'name_latin' => 'Eng Sopheak', 'email' => 'sopheak.eng@example.com', 'grade' => '9A', 'gender' => 'ប្រុស', 'phone' => '089 888 444', 'dob' => '2010-09-19', 'parent' => 'អេង រតនា', 'parent_phone' => '089 444 888'],
            ['name' => 'យឹម ម៉ាលី', 'name_latin' => 'Yim Maly', 'email' => 'maly.yim@example.com', 'grade' => '9A', 'gender' => 'ស្រី', 'phone' => '070 999 555', 'dob' => '2010-12-31', 'parent' => 'យឹម សុគន្ធ', 'parent_phone' => '070 555 999'],
            ['name' => 'ស៊ូ វិរៈ', 'name_latin' => 'Sou Vireak', 'email' => 'vireak.sou@example.com', 'grade' => '10A', 'gender' => 'ប្រុស', 'phone' => '016 000 666', 'dob' => '2009-08-04', 'parent' => 'ស៊ូ ហេង', 'parent_phone' => '016 666 000'],
            ['name' => 'ផាន់ ធីរ៉ា', 'name_latin' => 'Phan Thira', 'email' => 'thira.phan@example.com', 'grade' => '11A', 'gender' => 'ស្រី', 'phone' => '092 111 777', 'dob' => '2008-04-26', 'parent' => 'ផាន់ គឹមហួរ', 'parent_phone' => '092 777 111'],
            ['name' => 'ព្រំ សុភា', 'name_latin' => 'Prom Sophea', 'email' => 'sophea.prom@example.com', 'grade' => '12A', 'gender' => 'ស្រី', 'phone' => '011 222 888', 'dob' => '2007-01-15', 'parent' => 'ព្រំ វ៉ាន់នី', 'parent_phone' => '011 888 222'],
            ['name' => 'ឡុង សម្បត្តិ', 'name_latin' => 'Long Sambath', 'email' => 'sambath.long@example.com', 'grade' => '12A', 'gender' => 'ប្រុស', 'phone' => '093 333 999', 'dob' => '2007-11-09', 'parent' => 'ឡុង វិសិដ្ឋ', 'parent_phone' => '093 999 333'],
        ];

        foreach ($studentsData as $sIdx => $sItem) {
            $u = \App\Models\User::firstOrCreate(
                ['email' => $sItem['email']],
                [
                    'name' => $sItem['name'],
                    'password' => bcrypt('password'),
                ]
            );
            $u->roles()->syncWithoutDetaching([$studentRole->id]);

            Student::updateOrCreate(
                ['student_id_card' => sprintf('STU-2026-%03d', $sIdx + 1)],
                [
                    'user_id' => $u->id,
                    'name_khmer' => $sItem['name'],
                    'name_latin' => $sItem['name_latin'],
                    'grade' => $sItem['grade'],
                    'gender' => $sItem['gender'],
                    'phone' => $sItem['phone'],
                    'date_of_birth' => $sItem['dob'],
                    'parent_name' => $sItem['parent'],
                    'parent_phone' => $sItem['parent_phone'],
                    'emergency_contact' => $sItem['parent_phone'],
                    'academic_year_id' => $ayCurrent->id,
                    'enrollment_date' => '2025-10-01',
                    'status' => 'active',
                ]
            );
        }

        $students = Student::all();

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
            Attendance::where('student_id', $stu->id)->whereDate('date', $today)->delete();
            Attendance::create([
                'student_id' => $stu->id,
                'date' => $today,
                'class_name' => $stu->grade ?: '7A',
                'status' => $idx === 2 ? 'late' : ($idx === 4 ? 'excused' : ($idx === 9 ? 'absent' : 'present')),
                'remark' => $idx === 2 ? 'យឺត ១០ នាទី' : ($idx === 4 ? 'ច្បាប់ឈឺ' : ($idx === 9 ? 'គ្មានច្បាប់' : 'មកទាន់ម៉ោង')),
                'academic_year_id' => $ayCurrent->id,
            ]);

            Attendance::where('student_id', $stu->id)->whereDate('date', $yesterday)->delete();
            Attendance::create([
                'student_id' => $stu->id,
                'date' => $yesterday,
                'class_name' => $stu->grade ?: '7A',
                'status' => $idx === 5 ? 'late' : 'present',
                'remark' => $idx === 5 ? 'យឺត ៥ នាទី' : 'មានវត្តមាន',
                'academic_year_id' => $ayCurrent->id,
            ]);
        }

        // 13.1 Teacher Attendance records (Today and past day)
        $allTeachers = Teacher::all();
        foreach ($allTeachers as $tIdx => $tch) {
            \App\Models\TeacherAttendance::where('teacher_id', $tch->id)->whereDate('date', $today)->delete();
            \App\Models\TeacherAttendance::create([
                'teacher_id' => $tch->id,
                'date' => $today,
                'status' => $tIdx === 3 ? 'late' : ($tIdx === 7 ? 'excused' : 'present'),
                'remark' => $tIdx === 3 ? 'យឺត ១៥ នាទី' : ($tIdx === 7 ? 'ច្បាប់ឈឺ' : 'មានវត្តមាន'),
            ]);

            \App\Models\TeacherAttendance::where('teacher_id', $tch->id)->whereDate('date', $yesterday)->delete();
            \App\Models\TeacherAttendance::create([
                'teacher_id' => $tch->id,
                'date' => $yesterday,
                'status' => 'present',
                'remark' => 'មានវត្តមាន',
            ]);
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
