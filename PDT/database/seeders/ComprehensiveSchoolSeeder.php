<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\CourseSubject;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TeacherAttendance;
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
use App\Models\User;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ComprehensiveSchoolSeeder extends Seeder
{
    public function run(): void
    {
        // 1. ACADEMIC YEARS
        $ayCurrent = AcademicYear::updateOrCreate(
            ['name' => '2025-2026'],
            [
                'start_date' => '2025-10-01',
                'end_date' => '2026-07-31',
                'semester_1' => 'Oct-Feb',
                'semester_2' => 'Mar-Jul',
                'is_current' => true,
                'status' => 'active',
            ]
        );

        $ayNext = AcademicYear::updateOrCreate(
            ['name' => '2026-2027'],
            [
                'start_date' => '2026-10-01',
                'end_date' => '2027-07-31',
                'semester_1' => 'Oct-Feb',
                'semester_2' => 'Mar-Jul',
                'is_current' => false,
                'status' => 'inactive',
            ]
        );

        // 2. SYSTEM SETTINGS
        $settings = [
            'school_name' => 'សាលារៀន ជំនាន់ថ្មី PDT',
            'school_name_en' => 'PDT New Generation School',
            'school_address' => 'មហាវិថីសហព័ន្ធរុស្ស៊ី សង្កាត់ទឹកល្អក់១ ខណ្ឌទួលគោក រាជធានីភ្នំពេញ',
            'school_phone' => '023 880 880 / 012 345 678',
            'school_email' => 'contact@pdt-school.edu.kh',
            'school_website' => 'https://pdt-school.edu.kh',
            'current_academic_year' => '2025-2026',
            'current_semester' => '1',
            'currency' => 'USD',
            'currency_symbol' => '$',
            'grading_system' => 'A:90-100, B:80-89, C:70-79, D:60-69, E:50-59, F:0-49',
            'demo_mode' => '1',
        ];

        foreach ($settings as $key => $val) {
            SystemSetting::set($key, $val);
        }

        // 3. SUBJECTS & CURRICULUM (8 Core MoEYS Subjects)
        $subjectsData = [
            ['code' => 'KHM-01', 'name' => 'អក្សរសាស្ត្រខ្មែរ', 'credit' => 2.0, 'max_score' => 100.0, 'credit_hours' => 5],
            ['code' => 'MTH-01', 'name' => 'គណិតវិទ្យា', 'credit' => 2.0, 'max_score' => 100.0, 'credit_hours' => 5],
            ['code' => 'PHY-01', 'name' => 'រូបវិទ្យា', 'credit' => 1.5, 'max_score' => 100.0, 'credit_hours' => 3],
            ['code' => 'CHM-01', 'name' => 'គីមីវិទ្យា', 'credit' => 1.5, 'max_score' => 100.0, 'credit_hours' => 3],
            ['code' => 'BIO-01', 'name' => 'ជីវវិទ្យា', 'credit' => 1.5, 'max_score' => 100.0, 'credit_hours' => 3],
            ['code' => 'HIS-01', 'name' => 'ប្រវត្តិវិទ្យា', 'credit' => 1.0, 'max_score' => 100.0, 'credit_hours' => 2],
            ['code' => 'ETH-01', 'name' => 'សីលធម៌-ពលរដ្ឋ', 'credit' => 1.0, 'max_score' => 100.0, 'credit_hours' => 2],
            ['code' => 'ENG-01', 'name' => 'ភាសាអង់គ្លេស', 'credit' => 1.0, 'max_score' => 100.0, 'credit_hours' => 3],
        ];

        $subjectModels = [];
        foreach ($subjectsData as $s) {
            $sub = CourseSubject::updateOrCreate(
                ['code' => $s['code']],
                [
                    'name' => $s['name'],
                    'credit' => $s['credit'],
                    'max_score' => $s['max_score'],
                    'credit_hours' => $s['credit_hours'],
                    'grade_level' => '7-12',
                    'description' => 'មុខវិជ្ជាគោលកម្មវិធីសិក្សាក្រសួងអប់រំ យុវជន និងកីឡា',
                ]
            );
            $subjectModels[$s['code']] = $sub;
        }

        // 4. TEACHERS (15 Comprehensive Khmer Teachers)
        $teacherRole = Role::firstOrCreate(['name' => 'teacher'], ['description' => 'គ្រូបង្រៀន']);
        $teachersData = [
            ['id_card' => 'TCH-2026-001', 'name' => 'លោក ឈុន វណ្ណា', 'name_latin' => 'Chhun Vanna', 'email' => 'vanna.chhun@pdt.edu.kh', 'subject' => 'គណិតវិទ្យា', 'gender' => 'ប្រុស', 'phone' => '012 345 678', 'salary' => 850, 'classes' => '12A, 11D', 'homeroom' => '12A', 'specialization' => 'គណិតវិទ្យាកម្រិតខ្ពស់', 'qualification' => 'បរិញ្ញាបត្រជាន់ខ្ពស់គរុកោសល្យ'],
            ['id_card' => 'TCH-2026-002', 'name' => 'អ្នកគ្រូ កែវ សុផល', 'name_latin' => 'Keo Sophal', 'email' => 'sophal.keo@pdt.edu.kh', 'subject' => 'អក្សរសាស្ត្រខ្មែរ', 'gender' => 'ស្រី', 'phone' => '098 765 432', 'salary' => 800, 'classes' => '11D, 10A', 'homeroom' => '11D', 'specialization' => 'អក្សរសិល្ប៍ និងវេយ្យាករណ៍ខ្មែរ', 'qualification' => 'បរិញ្ញាបត្រអក្សរសាស្ត្រខ្មែរ'],
            ['id_card' => 'TCH-2026-003', 'name' => 'លោក សេង សុខា', 'name_latin' => 'Seng Sokha', 'email' => 'sokha.seng@pdt.edu.kh', 'subject' => 'ភាសាអង់គ្លេស', 'gender' => 'ប្រុស', 'phone' => '017 888 999', 'salary' => 780, 'classes' => '10A, 11D', 'homeroom' => '10A', 'specialization' => 'TEFL & Advanced Grammar', 'qualification' => 'Bachelor of Education (English)'],
            ['id_card' => 'TCH-2026-004', 'name' => 'លោក អ៊ុំ រតនៈ', 'name_latin' => 'Oum Rattanak', 'email' => 'rattanak.oum@pdt.edu.kh', 'subject' => 'រូបវិទ្យា', 'gender' => 'ប្រុស', 'phone' => '077 222 333', 'salary' => 750, 'classes' => '10B, 11D', 'homeroom' => '10B', 'specialization' => 'រូបវិទ្យាអនុវត្ត និងពិសោធន៍', 'qualification' => 'បរិញ្ញាបត្ររូបវិទ្យា'],
            ['id_card' => 'TCH-2026-005', 'name' => 'អ្នកគ្រូ មាស ចរិយា', 'name_latin' => 'Meas Chariya', 'email' => 'chariya.meas@pdt.edu.kh', 'subject' => 'គីមីវិទ្យា', 'gender' => 'ស្រី', 'phone' => '089 444 555', 'salary' => 740, 'classes' => '11D, 10A, 12A', 'homeroom' => null, 'specialization' => 'គីមីវិទ្យាសរីរាង្គ', 'qualification' => 'បរិញ្ញាបត្រគីមីវិទ្យា'],
            ['id_card' => 'TCH-2026-006', 'name' => 'លោក លឹម ប៊ុនធឿន', 'name_latin' => 'Lim Bunthoeun', 'email' => 'bunthoeun.lim@pdt.edu.kh', 'subject' => 'ជីវវិទ្យា', 'gender' => 'ប្រុស', 'phone' => '015 666 777', 'salary' => 720, 'classes' => '11D, 10A', 'homeroom' => null, 'specialization' => 'ហ្សែន និងបរិស្ថានវិទ្យា', 'qualification' => 'បរិញ្ញាបត្រជីវវិទ្យា'],
            ['id_card' => 'TCH-2026-007', 'name' => 'អ្នកគ្រូ សួន ចិន្តា', 'name_latin' => 'Suon Chenda', 'email' => 'chenda.suon@pdt.edu.kh', 'subject' => 'ប្រវត្តិវិទ្យា', 'gender' => 'ស្រី', 'phone' => '092 111 222', 'salary' => 700, 'classes' => '11D, 10A, 10B', 'homeroom' => null, 'specialization' => 'ប្រវត្តិសាស្ត្រខ្មែរ និងសកលលោក', 'qualification' => 'បរិញ្ញាបត្រប្រវត្តិវិទ្យា'],
            ['id_card' => 'TCH-2026-008', 'name' => 'លោក ហេង សម្បត្តិ', 'name_latin' => 'Heng Sambath', 'email' => 'sambath.heng@pdt.edu.kh', 'subject' => 'សីលធម៌-ពលរដ្ឋ', 'gender' => 'ប្រុស', 'phone' => '016 555 666', 'salary' => 690, 'classes' => '11D, 10A', 'homeroom' => null, 'specialization' => 'ច្បាប់ និងពលរដ្ឋវិទ្យា', 'qualification' => 'បរិញ្ញាបត្រនីតិសាស្ត្រ'],
            ['id_card' => 'TCH-2026-009', 'name' => 'អ្នកគ្រូ ពេជ្រ ពិសី', 'name_latin' => 'Pech Pisey', 'email' => 'pisey.pech@pdt.edu.kh', 'subject' => 'ភូមិវិទ្យា', 'gender' => 'ស្រី', 'phone' => '070 333 444', 'salary' => 690, 'classes' => '10A, 10B, 11D', 'homeroom' => null, 'specialization' => 'ភូមិវិទ្យាសេដ្ឋកិច្ច', 'qualification' => 'បរិញ្ញាបត្រភូមិវិទ្យា'],
            ['id_card' => 'TCH-2026-010', 'name' => 'លោក តាំង គីមឡេង', 'name_latin' => 'Tang Kimleng', 'email' => 'kimleng.tang@pdt.edu.kh', 'subject' => 'ព័ត៌មានវិទ្យា', 'gender' => 'ប្រុស', 'phone' => '011 222 333', 'salary' => 760, 'classes' => '10A, 11D, 12A', 'homeroom' => null, 'specialization' => 'Computer Science & Coding', 'qualification' => 'បរិញ្ញាបត្រវិទ្យាសាស្ត្រកុំព្យូទ័រ'],
            ['id_card' => 'TCH-2026-011', 'name' => 'លោក ម៉ៅ សុវណ្ណារ៉ា', 'name_latin' => 'Mao Sovannara', 'email' => 'sovannara.mao@pdt.edu.kh', 'subject' => 'អប់រំកាយ', 'gender' => 'ប្រុស', 'phone' => '088 999 111', 'salary' => 650, 'classes' => '10A, 10B, 11D, 12A', 'homeroom' => null, 'specialization' => 'បាល់ទាត់ និងអត្តពលកម្ម', 'qualification' => 'គរុកោសល្យកីឡា'],
            ['id_card' => 'TCH-2026-012', 'name' => 'អ្នកគ្រូ ឌិត សុគន្ធា', 'name_latin' => 'Dy Sokunthea', 'email' => 'sokunthea.dy@pdt.edu.kh', 'subject' => 'គណិតវិទ្យា', 'gender' => 'ស្រី', 'phone' => '096 444 111', 'salary' => 730, 'classes' => '10A, 10B', 'homeroom' => null, 'specialization' => 'ធរណីមាត្រ និងពិជគណិត', 'qualification' => 'បរិញ្ញាបត្រគណិតវិទ្យា'],
            ['id_card' => 'TCH-2026-013', 'name' => 'លោក គង់ សុជាតិ', 'name_latin' => 'Kong Socheat', 'email' => 'socheat.kong@pdt.edu.kh', 'subject' => 'អក្សរសាស្ត្រខ្មែរ', 'gender' => 'ប្រុស', 'phone' => '012 999 888', 'salary' => 720, 'classes' => '10B, 12A', 'homeroom' => null, 'specialization' => 'តែងសេចក្តី និងកំណាព្យ', 'qualification' => 'បរិញ្ញាបត្រអក្សរសាស្ត្រខ្មែរ'],
            ['id_card' => 'TCH-2026-014', 'name' => 'អ្នកគ្រូ រស់ វណ្ណដា', 'name_latin' => 'Ros Vannda', 'email' => 'vannda.ros@pdt.edu.kh', 'subject' => 'ភាសាអង់គ្លេស', 'gender' => 'ស្រី', 'phone' => '097 555 333', 'salary' => 710, 'classes' => '10B, 12A', 'homeroom' => null, 'specialization' => 'IELTS Preparation', 'qualification' => 'BA in English Literature'],
            ['id_card' => 'TCH-2026-015', 'name' => 'លោក ជា វីរៈ', 'name_latin' => 'Chea Vireak', 'email' => 'vireak.chea@pdt.edu.kh', 'subject' => 'រូបវិទ្យា', 'gender' => 'ប្រុស', 'phone' => '078 666 999', 'salary' => 710, 'classes' => '10A, 12A', 'homeroom' => null, 'specialization' => 'អគ្គិសនី និងមេកានិច', 'qualification' => 'បរិញ្ញាបត្ររូបវិទ្យា'],
        ];

        $teacherModels = [];
        foreach ($teachersData as $tItem) {
            $u = User::updateOrCreate(
                ['email' => $tItem['email']],
                [
                    'name' => $tItem['name'],
                    'password' => Hash::make('password'),
                ]
            );
            $u->roles()->syncWithoutDetaching([$teacherRole->id]);

            $tch = Teacher::updateOrCreate(
                ['teacher_id_card' => $tItem['id_card']],
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
            $teacherModels[$tItem['id_card']] = $tch;
        }

        // 5. CLASSES (10A, 10B, 11D, 12A + all 7A-12H)
        $grades = [7, 8, 9, 10, 11, 12];
        $sections = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
        $classModels = [];

        foreach ($grades as $g) {
            foreach ($sections as $s) {
                $className = $g . $s;
                $homeroomId = null;
                $room = 'B-' . $g . '0' . (ord($s) - 64);
                $capacity = 45;

                if ($className === '11D') {
                    $homeroomId = $teacherModels['TCH-2026-002']->id; // អ្នកគ្រូ កែវ សុផល
                    $room = 'B-114';
                    $capacity = 55;
                } elseif ($className === '10A') {
                    $homeroomId = $teacherModels['TCH-2026-003']->id; // លោក សេង សុខា
                    $room = 'B-101';
                    $capacity = 45;
                } elseif ($className === '10B') {
                    $homeroomId = $teacherModels['TCH-2026-004']->id; // លោក អ៊ុំ រតនៈ
                    $room = 'B-102';
                } elseif ($className === '12A') {
                    $homeroomId = $teacherModels['TCH-2026-001']->id; // លោក ឈុន វណ្ណា
                    $room = 'B-121';
                }

                $sc = SchoolClass::updateOrCreate(
                    ['name' => $className],
                    [
                        'grade' => (string)$g,
                        'section' => $s,
                        'room_number' => $room,
                        'capacity' => $capacity,
                        'teacher_id' => $homeroomId,
                        'academic_year_id' => $ayCurrent->id,
                        'description' => "ថ្នាក់រៀនទី {$g} បន្ទប់ {$s} (ជំនាន់ថ្មី PDT)",
                    ]
                );
                $classModels[$className] = $sc;
            }
        }

        // 6. STUDENTS GENERATION
        $studentRole = Role::firstOrCreate(['name' => 'student'], ['description' => 'សិស្សានុសិស្ស']);

        // 6.1: 52 Students for Class 11D
        $class11DStudents = [
            ['name' => 'សុខ ពិសិដ្ឋ', 'latin' => 'Sok Piseth', 'gender' => 'ប្រុស', 'dob' => '2008-05-14', 'parent' => 'សុខ ជា', 'rel' => 'ឪពុក', 'p_phone' => '012 888 777'],
            ['name' => 'ចាន់ ធីតា', 'latin' => 'Chan Thida', 'gender' => 'ស្រី', 'dob' => '2008-08-20', 'parent' => 'ចាន់ សុផល', 'rel' => 'ឪពុក', 'p_phone' => '098 777 666'],
            ['name' => 'មាស សុវណ្ណ', 'latin' => 'Meas Sovann', 'gender' => 'ប្រុស', 'dob' => '2008-01-10', 'parent' => 'មាស រ៉ា', 'rel' => 'ឪពុក', 'p_phone' => '077 666 555'],
            ['name' => 'គង់ ចិន្តា', 'latin' => 'Kong Chenda', 'gender' => 'ស្រី', 'dob' => '2008-11-25', 'parent' => 'គង់ សារ៉េត', 'rel' => 'ម្តាយ', 'p_phone' => '015 555 444'],
            ['name' => 'ហេង វិសាល', 'latin' => 'Heng Visal', 'gender' => 'ប្រុស', 'dob' => '2008-03-30', 'parent' => 'ហេង វុទ្ធី', 'rel' => 'ឪពុក', 'p_phone' => '089 444 333'],
            ['name' => 'កែវ រចនា', 'latin' => 'Keo Rachana', 'gender' => 'ស្រី', 'dob' => '2008-07-15', 'parent' => 'កែវ សម្បត្តិ', 'rel' => 'ឪពុក', 'p_phone' => '070 333 222'],
            ['name' => 'លី ដារ៉ា', 'latin' => 'Ly Dara', 'gender' => 'ប្រុស', 'dob' => '2008-09-05', 'parent' => 'លី សេង', 'rel' => 'ឪពុក', 'p_phone' => '016 222 111'],
            ['name' => 'វ៉ាន់ ស្រីមុំ', 'latin' => 'Van Sreymom', 'gender' => 'ស្រី', 'dob' => '2008-04-18', 'parent' => 'វ៉ាន់ ធឿន', 'rel' => 'ឪពុក', 'p_phone' => '092 111 000'],
            ['name' => 'ឃុន វឌ្ឍនា', 'latin' => 'Khun Vathana', 'gender' => 'ប្រុស', 'dob' => '2008-12-01', 'parent' => 'ឃុន ផល្លា', 'rel' => 'ម្តាយ', 'p_phone' => '011 000 999'],
            ['name' => 'អ៊ុង គីមសួរ', 'latin' => 'Ung Kimsour', 'gender' => 'ស្រី', 'dob' => '2008-06-22', 'parent' => 'អ៊ុង វីរៈ', 'rel' => 'ឪពុក', 'p_phone' => '093 999 888'],
            ['name' => 'សេង បូរ៉ា', 'latin' => 'Seng Bora', 'gender' => 'ប្រុស', 'dob' => '2008-02-17', 'parent' => 'សេង ប៊ុនណា', 'rel' => 'ឪពុក', 'p_phone' => '012 888 444'],
            ['name' => 'នួន ស្រីណែត', 'latin' => 'Nuon Sreynet', 'gender' => 'ស្រី', 'dob' => '2008-10-08', 'parent' => 'នួន សុខឃីម', 'rel' => 'ឪពុក', 'p_phone' => '098 111 555'],
            ['name' => 'ជា សុភាព', 'latin' => 'Chea Sopheap', 'gender' => 'ប្រុស', 'dob' => '2008-07-29', 'parent' => 'ជា ចំរើន', 'rel' => 'ឪពុក', 'p_phone' => '077 222 666'],
            ['name' => 'ឌិត មុនី', 'latin' => 'Dy Mony', 'gender' => 'ស្រី', 'dob' => '2008-03-12', 'parent' => 'ឌិត ថាវី', 'rel' => 'ម្តាយ', 'p_phone' => '015 333 777'],
            ['name' => 'អេង សុភ័ក្ត្រ', 'latin' => 'Eng Sopheak', 'gender' => 'ប្រុស', 'dob' => '2008-09-19', 'parent' => 'អេង រតនា', 'rel' => 'ឪពុក', 'p_phone' => '089 444 888'],
            ['name' => 'យឹម ម៉ាលី', 'latin' => 'Yim Maly', 'gender' => 'ស្រី', 'dob' => '2008-12-31', 'parent' => 'យឹម សុគន្ធ', 'rel' => 'ម្តាយ', 'p_phone' => '070 555 999'],
            ['name' => 'ស៊ូ វិរៈ', 'latin' => 'Sou Vireak', 'gender' => 'ប្រុស', 'dob' => '2008-08-04', 'parent' => 'ស៊ូ ហេង', 'rel' => 'ឪពុក', 'p_phone' => '016 666 000'],
            ['name' => 'ផាន់ ធីរ៉ា', 'latin' => 'Phan Thira', 'gender' => 'ស្រី', 'dob' => '2008-04-26', 'parent' => 'ផាន់ គឹមហួរ', 'rel' => 'ឪពុក', 'p_phone' => '092 777 111'],
            ['name' => 'ព្រំ សុភា', 'latin' => 'Prom Sophea', 'gender' => 'ស្រី', 'dob' => '2008-01-15', 'parent' => 'ព្រំ វ៉ាន់នី', 'rel' => 'ម្តាយ', 'p_phone' => '011 888 222'],
            ['name' => 'ឡុង សម្បត្តិ', 'latin' => 'Long Sambath', 'gender' => 'ប្រុស', 'dob' => '2008-11-09', 'parent' => 'ឡុង វិសិដ្ឋ', 'rel' => 'ឪពុក', 'p_phone' => '093 999 333'],
            ['name' => 'ឈិត ចំរើន', 'latin' => 'Chhit Chamroeun', 'gender' => 'ប្រុស', 'dob' => '2008-06-18', 'parent' => 'ឈិត សារ៉ន', 'rel' => 'ឪពុក', 'p_phone' => '012 345 111'],
            ['name' => 'ប៊ុន ស្រីពៅ', 'latin' => 'Bun Sreypov', 'gender' => 'ស្រី', 'dob' => '2008-02-23', 'parent' => 'ប៊ុន វ៉ាន់ដេត', 'rel' => 'ឪពុក', 'p_phone' => '098 765 222'],
            ['name' => 'គឹម វណ្ណារ៉ា', 'latin' => 'Kim Vannara', 'gender' => 'ប្រុស', 'dob' => '2008-10-14', 'parent' => 'គឹម ហេង', 'rel' => 'ឪពុក', 'p_phone' => '017 890 333'],
            ['name' => 'ម៉ែន លក្ខណា', 'latin' => 'Men Leakhena', 'gender' => 'ស្រី', 'dob' => '2008-05-09', 'parent' => 'ម៉ែន សំអាត', 'rel' => 'ម្តាយ', 'p_phone' => '077 123 444'],
            ['name' => 'ផាន មុនីរ័ត្ន', 'latin' => 'Phan Muniroth', 'gender' => 'ប្រុស', 'dob' => '2008-12-28', 'parent' => 'ផាន សុជាតិ', 'rel' => 'ឪពុក', 'p_phone' => '089 234 555'],
            ['name' => 'ជា សុវណ្ណារី', 'latin' => 'Chea Sovannary', 'gender' => 'ស្រី', 'dob' => '2008-04-03', 'parent' => 'ជា ប៊ុនថន', 'rel' => 'ឪពុក', 'p_phone' => '070 345 666'],
            ['name' => 'ទូច សុខុម', 'latin' => 'Touch Sokhom', 'gender' => 'ប្រុស', 'dob' => '2008-08-11', 'parent' => 'ទូច សារ៉ាត់', 'rel' => 'ឪពុក', 'p_phone' => '016 456 777'],
            ['name' => 'ពេជ្រ សុភាព', 'latin' => 'Pech Sopheap', 'gender' => 'ស្រី', 'dob' => '2008-01-27', 'parent' => 'ពេជ្រ គឹមសាន', 'rel' => 'ម្តាយ', 'p_phone' => '092 567 888'],
            ['name' => 'ម៉ៅ សុផល', 'latin' => 'Mao Sophal', 'gender' => 'ប្រុស', 'dob' => '2008-07-21', 'parent' => 'ម៉ៅ សុខន', 'rel' => 'ឪពុក', 'p_phone' => '011 678 999'],
            ['name' => 'រស់ ចរិយា', 'latin' => 'Ros Chariya', 'gender' => 'ស្រី', 'dob' => '2008-03-16', 'parent' => 'រស់ សារ៉េត', 'rel' => 'ឪពុក', 'p_phone' => '093 789 000'],
            ['name' => 'ប្រាក់ វិទូ', 'latin' => 'Prak Vithou', 'gender' => 'ប្រុស', 'dob' => '2008-09-30', 'parent' => 'ប្រាក់ សារឿន', 'rel' => 'ឪពុក', 'p_phone' => '012 890 111'],
            ['name' => 'សោម សុវត្តី', 'latin' => 'Saom Sovattey', 'gender' => 'ស្រី', 'dob' => '2008-11-19', 'parent' => 'សោម ចំរើន', 'rel' => 'ម្តាយ', 'p_phone' => '098 901 222'],
            ['name' => 'អ៊ុក វ៉ាន់ឌី', 'latin' => 'Ouk Vandy', 'gender' => 'ប្រុស', 'dob' => '2008-04-07', 'parent' => 'អ៊ុក សុគន្ធ', 'rel' => 'ឪពុក', 'p_phone' => '017 012 333'],
            ['name' => 'តាំង គីមហួយ', 'latin' => 'Tang Kimhuoy', 'gender' => 'ស្រី', 'dob' => '2008-06-25', 'parent' => 'តាំង គឹមសែ', 'rel' => 'ឪពុក', 'p_phone' => '077 123 444'],
            ['name' => 'រ័ត្ន សុភ័ណ្ឌ', 'latin' => 'Roth Sophean', 'gender' => 'ប្រុស', 'dob' => '2008-10-02', 'parent' => 'រ័ត្ន ផល្លី', 'rel' => 'ឪពុក', 'p_phone' => '089 234 555'],
            ['name' => 'សុខ ស្រីល័ក្ខ', 'latin' => 'Sok Sreyleak', 'gender' => 'ស្រី', 'dob' => '2008-08-14', 'parent' => 'សុខ វុទ្ធី', 'rel' => 'ឪពុក', 'p_phone' => '070 345 666'],
            ['name' => 'យឹម ចាន់ដារ៉ូ', 'latin' => 'Yim Chandaro', 'gender' => 'ប្រុស', 'dob' => '2008-02-11', 'parent' => 'យឹម ផល្លា', 'rel' => 'ឪពុក', 'p_phone' => '016 456 777'],
            ['name' => 'ហួត ម៉ារីណា', 'latin' => 'Huot Marina', 'gender' => 'ស្រី', 'dob' => '2008-12-05', 'parent' => 'ហួត សុខុម', 'rel' => 'ម្តាយ', 'p_phone' => '092 567 888'],
            ['name' => 'ស៊ិន សុវណ្ណឌី', 'latin' => 'Sin Sovandy', 'gender' => 'ប្រុស', 'dob' => '2008-05-18', 'parent' => 'ស៊ិន វណ្ណៈ', 'rel' => 'ឪពុក', 'p_phone' => '011 678 999'],
            ['name' => 'សៀង មុនីរ័ត្ន', 'latin' => 'Sieng Muniroth', 'gender' => 'ស្រី', 'dob' => '2008-07-28', 'parent' => 'សៀង គឹមហេង', 'rel' => 'ឪពុក', 'p_phone' => '093 789 000'],
            ['name' => 'ឈុន វិបុល', 'latin' => 'Chhun Vibol', 'gender' => 'ប្រុស', 'dob' => '2008-03-04', 'parent' => 'ឈុន សារិទ្ធ', 'rel' => 'ឪពុក', 'p_phone' => '012 890 222'],
            ['name' => 'ភិន ស្រីពេជ្រ', 'latin' => 'Phin Sreypech', 'gender' => 'ស្រី', 'dob' => '2008-09-12', 'parent' => 'ភិន វ៉ាន់ឌី', 'rel' => 'ឪពុក', 'p_phone' => '098 901 333'],
            ['name' => 'ណុប សុជាតិ', 'latin' => 'Nop Socheat', 'gender' => 'ប្រុស', 'dob' => '2008-01-22', 'parent' => 'ណុប គឹមឡាយ', 'rel' => 'ឪពុក', 'p_phone' => '017 012 444'],
            ['name' => 'ឈិន កល្យាណ', 'latin' => 'Chhin Kallyan', 'gender' => 'ស្រី', 'dob' => '2008-11-03', 'parent' => 'ឈិន សារឿន', 'rel' => 'ម្តាយ', 'p_phone' => '077 123 555'],
            ['name' => 'តែ គីមស្រ៊ុន', 'latin' => 'Tae Kimsrun', 'gender' => 'ប្រុស', 'dob' => '2008-06-08', 'parent' => 'តែ ហុកសេង', 'rel' => 'ឪពុក', 'p_phone' => '089 234 666'],
            ['name' => 'ហុង លីដា', 'latin' => 'Hong Lyda', 'gender' => 'ស្រី', 'dob' => '2008-04-19', 'parent' => 'ហុង វណ្ណដា', 'rel' => 'ឪពុក', 'p_phone' => '070 345 777'],
            ['name' => 'ហ៊ីង វិសិដ្ឋ', 'latin' => 'Hing Viseth', 'gender' => 'ប្រុស', 'dob' => '2008-10-24', 'parent' => 'ហ៊ីង សុខា', 'rel' => 'ឪពុក', 'p_phone' => '016 456 888'],
            ['name' => 'អ៊ឹម ទេវី', 'latin' => 'Im Devi', 'gender' => 'ស្រី', 'dob' => '2008-08-01', 'parent' => 'អ៊ឹម សម្បត្តិ', 'rel' => 'ម្តាយ', 'p_phone' => '092 567 999'],
            ['name' => 'កែវ ប៊ុនរុង', 'latin' => 'Keo Bunrong', 'gender' => 'ប្រុស', 'dob' => '2008-02-15', 'parent' => 'កែវ វិបុល', 'rel' => 'ឪពុក', 'p_phone' => '011 678 000'],
            ['name' => 'ឡេង រតនា', 'latin' => 'Leng Ratana', 'gender' => 'ស្រី', 'dob' => '2008-12-14', 'parent' => 'ឡេង សុផល', 'rel' => 'ឪពុក', 'p_phone' => '093 789 111'],
            ['name' => 'នេត វ៉ាន់ថា', 'latin' => 'Neth Vantha', 'gender' => 'ប្រុស', 'dob' => '2008-05-27', 'parent' => 'នេត វណ្ណដេត', 'rel' => 'ឪពុក', 'p_phone' => '012 890 333'],
            ['name' => 'ទិត្យ ស្រីនាង', 'latin' => 'Tith Sreyneang', 'gender' => 'ស្រី', 'dob' => '2008-07-06', 'parent' => 'ទិត្យ សារ៉ុម', 'rel' => 'ម្តាយ', 'p_phone' => '098 901 444'],
        ];

        // 6.2: 35 Students for Class 10A
        $class10AStudents = [
            ['name' => 'ប៊ុន ថេង', 'latin' => 'Bun Theng', 'gender' => 'ប្រុស', 'dob' => '2009-03-12', 'parent' => 'ប៊ុន សុខា', 'rel' => 'ឪពុក', 'p_phone' => '012 777 101'],
            ['name' => 'ផាន់ ធីរ៉ា', 'latin' => 'Phan Thira', 'gender' => 'ស្រី', 'dob' => '2009-08-25', 'parent' => 'ផាន់ គឹមហួរ', 'rel' => 'ឪពុក', 'p_phone' => '098 888 102'],
            ['name' => 'គង់ សុវណ្ណដារ៉ា', 'latin' => 'Kong Sovandara', 'gender' => 'ប្រុស', 'dob' => '2009-01-19', 'parent' => 'គង់ ចំរើន', 'rel' => 'ឪពុក', 'p_phone' => '077 999 103'],
            ['name' => 'កែវ សុគន្ធា', 'latin' => 'Keo Sokunthea', 'gender' => 'ស្រី', 'dob' => '2009-11-04', 'parent' => 'កែវ សារិទ្ធ', 'rel' => 'ម្តាយ', 'p_phone' => '015 111 104'],
            ['name' => 'ម៉ៅ វិច្ឆិកា', 'latin' => 'Mao Vicheka', 'gender' => 'ប្រុស', 'dob' => '2009-04-18', 'parent' => 'ម៉ៅ វុទ្ធី', 'rel' => 'ឪពុក', 'p_phone' => '089 222 105'],
            ['name' => 'សុខ មាលី', 'latin' => 'Sok Maly', 'gender' => 'ស្រី', 'dob' => '2009-09-30', 'parent' => 'សុខ សារ៉េត', 'rel' => 'ម្តាយ', 'p_phone' => '070 333 106'],
            ['name' => 'ជា រ៉ាដូ', 'latin' => 'Chea Rado', 'gender' => 'ប្រុស', 'dob' => '2009-02-14', 'parent' => 'ជា ប៊ុនថន', 'rel' => 'ឪពុក', 'p_phone' => '016 444 107'],
            ['name' => 'ជា ស្រីណុច', 'latin' => 'Chea Sreynoch', 'gender' => 'ស្រី', 'dob' => '2009-06-22', 'parent' => 'ជា សម្បត្តិ', 'rel' => 'ឪពុក', 'p_phone' => '092 555 108'],
            ['name' => 'ឈឹម សុខគង់', 'latin' => 'Chhim Sokkong', 'gender' => 'ប្រុស', 'dob' => '2009-12-09', 'parent' => 'ឈឹម សារ៉ុម', 'rel' => 'ឪពុក', 'p_phone' => '011 666 109'],
            ['name' => 'វ៉ាន់ ស្រីនិច', 'latin' => 'Van Sreynich', 'gender' => 'ស្រី', 'dob' => '2009-07-15', 'parent' => 'វ៉ាន់ ធឿន', 'rel' => 'ម្តាយ', 'p_phone' => '093 777 110'],
            ['name' => 'លឹម សម្បត្តិ', 'latin' => 'Lim Sambath', 'gender' => 'ប្រុស', 'dob' => '2009-05-28', 'parent' => 'លឹម គឹមសាន', 'rel' => 'ឪពុក', 'p_phone' => '012 888 111'],
            ['name' => 'ហេង ធីតា', 'latin' => 'Heng Thida', 'gender' => 'ស្រី', 'dob' => '2009-10-11', 'parent' => 'ហេង សុផល', 'rel' => 'ឪពុក', 'p_phone' => '098 999 112'],
            ['name' => 'ហេង សំណាង', 'latin' => 'Heng Samnang', 'gender' => 'ប្រុស', 'dob' => '2009-03-03', 'parent' => 'ហេង វិបុល', 'rel' => 'ឪពុក', 'p_phone' => '017 000 113'],
            ['name' => 'ម៉ែន ស្រីអូន', 'latin' => 'Men Sreyoun', 'gender' => 'ស្រី', 'dob' => '2009-08-16', 'parent' => 'ម៉ែន សំអាត', 'rel' => 'ម្តាយ', 'p_phone' => '077 111 114'],
            ['name' => 'ព្រំ វិបុល', 'latin' => 'Prom Vibol', 'gender' => 'ប្រុស', 'dob' => '2009-01-24', 'parent' => 'ព្រំ វ៉ាន់នី', 'rel' => 'ឪពុក', 'p_phone' => '089 222 115'],
            ['name' => 'អេង វណ្ណារី', 'latin' => 'Eng Vannary', 'gender' => 'ស្រី', 'dob' => '2009-11-17', 'parent' => 'អេង រតនា', 'rel' => 'ឪពុក', 'p_phone' => '070 333 116'],
            ['name' => 'អ៊ុំ ចាន់ដារ៉ា', 'latin' => 'Oum Chandara', 'gender' => 'ប្រុស', 'dob' => '2009-04-05', 'parent' => 'អ៊ុំ សុគន្ធ', 'rel' => 'ឪពុក', 'p_phone' => '016 444 117'],
            ['name' => 'រស់ សុវណ្ណម៉ាលី', 'latin' => 'Ros Sovannmaly', 'gender' => 'ស្រី', 'dob' => '2009-09-12', 'parent' => 'រស់ សារ៉េត', 'rel' => 'ម្តាយ', 'p_phone' => '092 555 118'],
            ['name' => 'សេង រតនៈ', 'latin' => 'Seng Rattanak', 'gender' => 'ប្រុស', 'dob' => '2009-02-27', 'parent' => 'សេង ប៊ុនណា', 'rel' => 'ឪពុក', 'p_phone' => '011 666 119'],
            ['name' => 'តាំង ស្រីរ័ត្ន', 'latin' => 'Tang Sreyroth', 'gender' => 'ស្រី', 'dob' => '2009-07-08', 'parent' => 'តាំង គឹមសែ', 'rel' => 'ឪពុក', 'p_phone' => '093 777 120'],
            ['name' => 'ទូច វិសាល', 'latin' => 'Touch Visal', 'gender' => 'ប្រុស', 'dob' => '2009-12-21', 'parent' => 'ទូច សារ៉ាត់', 'rel' => 'ឪពុក', 'p_phone' => '012 888 121'],
            ['name' => 'នេត សុជាតា', 'latin' => 'Neth Socheata', 'gender' => 'ស្រី', 'dob' => '2009-05-10', 'parent' => 'នេត វណ្ណដេត', 'rel' => 'ម្តាយ', 'p_phone' => '098 999 122'],
            ['name' => 'ឌុក ប៊ុនណាត', 'latin' => 'Duk Bunnat', 'gender' => 'ប្រុស', 'dob' => '2009-10-03', 'parent' => 'ឌុក សារឿន', 'rel' => 'ឪពុក', 'p_phone' => '017 000 123'],
            ['name' => 'សោម ចរិយា', 'latin' => 'Saom Chariya', 'gender' => 'ស្រី', 'dob' => '2009-03-29', 'parent' => 'សោម ចំរើន', 'rel' => 'ឪពុក', 'p_phone' => '077 111 124'],
            ['name' => 'នួន សុខា', 'latin' => 'Nuon Sokha', 'gender' => 'ប្រុស', 'dob' => '2009-08-02', 'parent' => 'នួន សុខឃីម', 'rel' => 'ឪពុក', 'p_phone' => '089 222 125'],
            ['name' => 'ពេជ្រ ម៉ារី', 'latin' => 'Pech Mary', 'gender' => 'ស្រី', 'dob' => '2009-01-14', 'parent' => 'ពេជ្រ គឹមសាន', 'rel' => 'ម្តាយ', 'p_phone' => '070 333 126'],
            ['name' => 'អ៊ុក ពិសិដ្ឋ', 'latin' => 'Ouk Piseth', 'gender' => 'ប្រុស', 'dob' => '2009-11-20', 'parent' => 'អ៊ុក សុគន្ធ', 'rel' => 'ឪពុក', 'p_phone' => '016 444 127'],
            ['name' => 'ឌិត ស្រីកែវ', 'latin' => 'Dy Sreykeo', 'gender' => 'ស្រី', 'dob' => '2009-04-11', 'parent' => 'ឌិត ថាវី', 'rel' => 'ម្តាយ', 'p_phone' => '092 555 128'],
            ['name' => 'ចាន់ វឌ្ឍនៈ', 'latin' => 'Chan Vathana', 'gender' => 'ប្រុស', 'dob' => '2009-09-06', 'parent' => 'ចាន់ សុផល', 'rel' => 'ឪពុក', 'p_phone' => '011 666 129'],
            ['name' => 'ប៊ុន សុខលីន', 'latin' => 'Bun Soklin', 'gender' => 'ស្រី', 'dob' => '2009-02-18', 'parent' => 'ប៊ុន វ៉ាន់ដេត', 'rel' => 'ឪពុក', 'p_phone' => '093 777 130'],
            ['name' => 'ឡុង ថាវី', 'latin' => 'Long Thavy', 'gender' => 'ប្រុស', 'dob' => '2009-07-25', 'parent' => 'ឡុង វិសិដ្ឋ', 'rel' => 'ឪពុក', 'p_phone' => '012 888 131'],
            ['name' => 'ឈិត ស្រីមាស', 'latin' => 'Chhit Sreymeas', 'gender' => 'ស្រី', 'dob' => '2009-12-13', 'parent' => 'ឈិត សារ៉ន', 'rel' => 'ម្តាយ', 'p_phone' => '098 999 132'],
            ['name' => 'យឹម សុវណ្ណ', 'latin' => 'Yim Sovann', 'gender' => 'ប្រុស', 'dob' => '2009-05-01', 'parent' => 'យឹម ផល្លា', 'rel' => 'ឪពុក', 'p_phone' => '017 000 133'],
            ['name' => 'អ៊ុង ចិន្តា', 'latin' => 'Ung Chenda', 'gender' => 'ស្រី', 'dob' => '2009-10-29', 'parent' => 'អ៊ុង វីរៈ', 'rel' => 'ឪពុក', 'p_phone' => '077 111 134'],
            ['name' => 'ស៊ូ សម្បត្តិ', 'latin' => 'Sou Sambath', 'gender' => 'ប្រុស', 'dob' => '2009-03-08', 'parent' => 'ស៊ូ ហេង', 'rel' => 'ឪពុក', 'p_phone' => '089 222 135'],
        ];

        $studentModels11D = [];
        foreach ($class11DStudents as $idx => $s) {
            $cardId = sprintf('STD-2026-%03d', $idx + 101);
            $u = User::updateOrCreate(
                ['email' => "std.11d.{$idx}@pdt.edu.kh"],
                [
                    'name' => $s['name'],
                    'password' => Hash::make('123456'),
                ]
            );
            $u->roles()->syncWithoutDetaching([$studentRole->id]);

            $stu = Student::updateOrCreate(
                ['student_id_card' => $cardId],
                [
                    'user_id' => $u->id,
                    'name_khmer' => $s['name'],
                    'name_latin' => $s['latin'],
                    'grade' => '11D',
                    'gender' => $s['gender'],
                    'date_of_birth' => $s['dob'],
                    'phone' => sprintf('096 %03d %03d', rand(100, 999), rand(100, 999)),
                    'parent_name' => $s['parent'],
                    'parent_relationship' => $s['rel'],
                    'parent_phone' => $s['p_phone'],
                    'emergency_contact' => $s['p_phone'],
                    'academic_year_id' => $ayCurrent->id,
                    'enrollment_date' => '2025-10-01',
                    'status' => 'active',
                ]
            );
            $studentModels11D[] = $stu;
        }

        $studentModels10A = [];
        foreach ($class10AStudents as $idx => $s) {
            $cardId = sprintf('STD-2026-%03d', $idx + 1);
            $u = User::updateOrCreate(
                ['email' => "std.10a.{$idx}@pdt.edu.kh"],
                [
                    'name' => $s['name'],
                    'password' => Hash::make('123456'),
                ]
            );
            $u->roles()->syncWithoutDetaching([$studentRole->id]);

            $stu = Student::updateOrCreate(
                ['student_id_card' => $cardId],
                [
                    'user_id' => $u->id,
                    'name_khmer' => $s['name'],
                    'name_latin' => $s['latin'],
                    'grade' => '10A',
                    'gender' => $s['gender'],
                    'date_of_birth' => $s['dob'],
                    'phone' => sprintf('015 %03d %03d', rand(100, 999), rand(100, 999)),
                    'parent_name' => $s['parent'],
                    'parent_relationship' => $s['rel'],
                    'parent_phone' => $s['p_phone'],
                    'emergency_contact' => $s['p_phone'],
                    'academic_year_id' => $ayCurrent->id,
                    'enrollment_date' => '2025-10-01',
                    'status' => 'active',
                ]
            );
            $studentModels10A[] = $stu;
        }

        // 7. EXAMINATIONS & SCORES (3 SESSIONS)
        $examsData = [
            [
                'title' => 'ការប្រឡងប្រចាំខែមករា',
                'exam_type' => 'monthly',
                'term' => 'ឆមាសទី ១',
                'semester' => 1,
                'start_date' => '2026-01-20',
                'end_date' => '2026-01-25',
                'is_published' => true,
            ],
            [
                'title' => 'ការប្រឡងឆមាសទី ១',
                'exam_type' => 'semester',
                'term' => 'ឆមាសទី ១',
                'semester' => 1,
                'start_date' => '2026-02-15',
                'end_date' => '2026-02-22',
                'is_published' => true,
            ],
            [
                'title' => 'ការប្រឡងប្រចាំខែមេសា',
                'exam_type' => 'monthly',
                'term' => 'ឆមាសទី ២',
                'semester' => 2,
                'start_date' => '2026-04-20',
                'end_date' => '2026-04-25',
                'is_published' => true,
            ],
        ];

        $examModels = [];
        foreach ($examsData as $eItem) {
            $exam = Examination::updateOrCreate(
                ['title' => $eItem['title']],
                [
                    'exam_type' => $eItem['exam_type'],
                    'semester' => $eItem['semester'],
                    'academic_year_id' => $ayCurrent->id,
                    'start_date' => $eItem['start_date'],
                    'end_date' => $eItem['end_date'],
                    'is_published' => $eItem['is_published'],
                ]
            );
            $examModels[] = $exam;
        }

        // Generate complete scores for ALL students in 11D (52) and 10A (35)
        $evaluatedClassGroups = [
            '11D' => $studentModels11D,
            '10A' => $studentModels10A,
        ];

        foreach ($examModels as $examIdx => $exam) {
            foreach ($evaluatedClassGroups as $className => $studentGroup) {
                foreach ($studentGroup as $stIdx => $student) {
                    // Realistic tiered scores distribution so ranking and grade letters are diverse
                    $tier = $stIdx % 5;
                    $baseRange = match ($tier) {
                        0 => [88, 98], // Top performers (A)
                        1 => [80, 89], // Very good (B)
                        2 => [70, 79], // Good (C)
                        3 => [60, 69], // Medium (D)
                        default => [50, 59], // Passing (E)
                    };

                    // Let 2 students fail slightly in monthly exams to reflect real pass-rates
                    if ($tier === 4 && $stIdx > 40 && $examIdx === 0) {
                        $baseRange = [42, 48];
                    }

                    foreach ($subjectsData as $subIdx => $sData) {
                        $subModel = $subjectModels[$sData['code']] ?? null;
                        if (!$subModel) continue;

                        // Variation per subject
                        $seedVal = ($stIdx * 7 + $subIdx * 11 + $examIdx * 13) % ($baseRange[1] - $baseRange[0] + 1);
                        $scoreFloat = (float)($baseRange[0] + $seedVal);
                        if ($scoreFloat > 100) $scoreFloat = 100.0;
                        if ($scoreFloat < 0) $scoreFloat = 0.0;

                        $gradeLetter = ExaminationScore::calculateGradeLetter($scoreFloat, 100);

                        ExaminationScore::updateOrCreate(
                            [
                                'examination_id' => $exam->id,
                                'student_id' => $student->id,
                                'course_subject_id' => $subModel->id,
                            ],
                            [
                                'class_name' => $className,
                                'subject_name' => $subModel->name,
                                'score' => $scoreFloat,
                                'max_score' => 100.0,
                                'grade_letter' => $gradeLetter,
                            ]
                        );
                    }
                }
            }
        }

        // 8. ATTENDANCE (Students in 11D & 10A, Teachers for current month)
        $today = Carbon::today();
        $sampleDays = [
            $today->copy()->toDateString(),
            $today->copy()->subDays(1)->toDateString(),
            $today->copy()->subDays(2)->toDateString(),
            $today->copy()->subDays(3)->toDateString(),
        ];

        foreach ($sampleDays as $dayIndex => $dStr) {
            foreach (array_merge($studentModels11D, $studentModels10A) as $sIdx => $stu) {
                // Realistic attendance ratio: ~94% present, 3% late, 2% excused, 1% absent
                $randMod = ($sIdx + $dayIndex * 13) % 100;
                $status = 'present';
                $remark = 'មានវត្តមាន';

                if ($randMod === 7 || $randMod === 8) {
                    $status = 'late';
                    $remark = 'មកយឺត ១០ នាទី';
                } elseif ($randMod === 19 || $randMod === 20) {
                    $status = 'excused';
                    $remark = 'ច្បាប់ឈឺ (អាណាព្យាបាលសុំ)';
                } elseif ($randMod === 99) {
                    $status = 'absent';
                    $remark = 'អវត្តមានគ្មានច្បាប់';
                }

                Attendance::where('student_id', $stu->id)->whereDate('date', $dStr)->delete();
                Attendance::create([
                    'student_id' => $stu->id,
                    'date' => $dStr,
                    'class_name' => $stu->grade,
                    'status' => $status,
                    'remark' => $remark,
                    'academic_year_id' => $ayCurrent->id,
                ]);
            }

            // Teacher Attendance
            foreach ($teacherModels as $tIdx => $tch) {
                TeacherAttendance::where('teacher_id', $tch->id)->whereDate('date', $dStr)->delete();
                TeacherAttendance::create([
                    'teacher_id' => $tch->id,
                    'date' => $dStr,
                    'status' => $tIdx === 3 ? 'late' : ($tIdx === 7 ? 'excused' : 'present'),
                    'remark' => $tIdx === 3 ? 'យឺត ១៥ នាទី' : ($tIdx === 7 ? 'ច្បាប់ឈឺ' : 'មានវត្តមាន'),
                ]);
            }
        }

        // 9. SCHEDULE / TIMETABLE (11D and 10A Monday-Saturday)
        $daysOfWeek = ['ច័ន្ទ', 'អង្គារ', 'ពុធ', 'ព្រហស្បតិ៍', 'សុក្រ', 'សៅរ៍'];
        $timeSlots = [
            ['start' => '07:00:00', 'end' => '08:00:00'],
            ['start' => '08:00:00', 'end' => '09:00:00'],
            ['start' => '09:15:00', 'end' => '10:15:00'],
            ['start' => '10:15:00', 'end' => '11:15:00'],
            ['start' => '14:00:00', 'end' => '15:00:00'],
            ['start' => '15:00:00', 'end' => '16:00:00'],
        ];

        $subKeys = array_keys($subjectModels);
        $tchList = array_values($teacherModels);

        foreach (['11D', '10A'] as $cIdx => $targetClass) {
            foreach ($daysOfWeek as $dIdx => $dayName) {
                foreach ($timeSlots as $slotIdx => $slot) {
                    $pickedSubKey = $subKeys[($dIdx * 6 + $slotIdx + $cIdx * 3) % count($subKeys)];
                    $pickedSub = $subjectModels[$pickedSubKey];
                    $pickedTch = $tchList[($slotIdx + $dIdx) % count($tchList)];

                    Timetable::updateOrCreate(
                        [
                            'class_name' => $targetClass,
                            'day_of_week' => $dayName,
                            'start_time' => $slot['start'],
                        ],
                        [
                            'course_subject_id' => $pickedSub->id,
                            'teacher_id' => $pickedTch->id,
                            'end_time' => $slot['end'],
                            'room_number' => $targetClass === '11D' ? 'B-114' : 'B-101',
                            'academic_year_id' => $ayCurrent->id,
                        ]
                    );
                }
            }
        }

        // 10. FINANCE & TUITION
        $feeCategories = [
            ['name' => 'ថ្លៃសិក្សាប្រចាំឆមាស', 'amount' => 350.0, 'desc' => 'ការបង់ថ្លៃសិក្សាឆមាសនីមួយៗ'],
            ['name' => 'ថ្លៃចុះឈ្មោះចូលរៀន', 'amount' => 50.0, 'desc' => 'ការបង់ថ្លៃចុះឈ្មោះដំបូង'],
            ['name' => 'ថ្លៃឯកសណ្ឋាន និងសម្ភារៈ', 'amount' => 85.0, 'desc' => 'ឯកសណ្ឋាន និងសៀវភៅជំនួយស្មារតី'],
            ['name' => 'សេវារថយន្តដឹកជញ្ជូន', 'amount' => 120.0, 'desc' => 'សេវារថយន្តដឹកជញ្ជូនសិស្ស'],
        ];

        $feeCatModels = [];
        foreach ($feeCategories as $fc) {
            $feeCatModels[$fc['name']] = FeeCategory::updateOrCreate(
                ['name' => $fc['name']],
                ['default_amount' => $fc['amount'], 'description' => $fc['desc']]
            );
        }

        // Seed 30 Invoices & Payments for 11D & 10A students
        $tuitionCat = $feeCatModels['ថ្លៃសិក្សាប្រចាំឆមាស'];
        foreach (array_slice(array_merge($studentModels11D, $studentModels10A), 0, 30) as $idx => $stu) {
            $invNum = sprintf('INV-2026-%04d', $idx + 1);
            $isPaid = ($idx % 3 === 0);
            $isPartial = ($idx % 3 === 1);
            $paidAmt = $isPaid ? 350.0 : ($isPartial ? 150.0 : 0.0);
            $status = $isPaid ? 'paid' : ($isPartial ? 'partial' : 'unpaid');

            $inv = Invoice::updateOrCreate(
                ['invoice_number' => $invNum],
                [
                    'student_id' => $stu->id,
                    'fee_category_id' => $tuitionCat->id,
                    'title' => 'ថ្លៃសិក្សាឆមាសទី ១ (២០២៥-២០២៦)',
                    'total_amount' => 350.0,
                    'paid_amount' => $paidAmt,
                    'due_date' => Carbon::now()->addDays(20),
                    'status' => $status,
                    'currency' => 'USD',
                ]
            );

            if ($paidAmt > 0) {
                Payment::updateOrCreate(
                    ['receipt_number' => sprintf('RCP-2026-%04d', $idx + 1)],
                    [
                        'invoice_id' => $inv->id,
                        'amount' => $paidAmt,
                        'payment_method' => $idx % 2 === 0 ? 'bank_transfer' : 'cash',
                        'payment_date' => Carbon::now()->subDays(rand(1, 15)),
                        'note' => 'បានទូទាត់រួចរាល់តាមវិក្កយបត្រ ' . $invNum,
                    ]
                );
            }
        }

        // Expenses
        $expenses = [
            ['title' => 'ប្រាក់បៀវត្សរ៍គ្រូបង្រៀនប្រចាំខែ', 'cat' => 'ប្រាក់ខែ', 'amount' => 11200.0, 'days' => 5],
            ['title' => 'ថ្លៃប្រើប្រាស់ទឹក និងអគ្គិសនីសាលា', 'cat' => 'ទឹកភ្លើង', 'amount' => 650.0, 'days' => 4],
            ['title' => 'ទិញសម្ភារៈពិសោធន៍រូបវិទ្យា និងគីមី', 'cat' => 'សម្ភារៈ', 'amount' => 1250.0, 'days' => 3],
            ['title' => 'ជួសជុលបន្ទប់កុំព្យូទ័រ និងប្រព័ន្ធ WiFi', 'cat' => 'ជួសជុល', 'amount' => 480.0, 'days' => 2],
            ['title' => 'ការបោះពុម្ពក្រដាសប្រឡងឆមាស', 'cat' => 'សម្ភារៈ', 'amount' => 320.0, 'days' => 1],
        ];

        foreach ($expenses as $exp) {
            SchoolExpense::updateOrCreate(
                ['title' => $exp['title']],
                [
                    'category' => $exp['cat'],
                    'amount' => $exp['amount'],
                    'expense_date' => Carbon::now()->subDays($exp['days']),
                    'description' => 'ការចំណាយប្រតិបត្តិការសាលារៀន ជំនាន់ថ្មី PDT',
                ]
            );
        }

        // 11. LIBRARY & ANNOUNCEMENTS
        $catTextbook = BookCategory::firstOrCreate(['name' => 'សៀវភៅពុម្ពសិក្សា']);
        $catSci = BookCategory::firstOrCreate(['name' => 'វិទ្យាសាស្ត្រ និងបច្ចេកវិទ្យា']);
        $catLit = BookCategory::firstOrCreate(['name' => 'អក្សរសិល្ប៍ និងប្រវត្តិសាស្ត្រ']);
        $catEng = BookCategory::firstOrCreate(['name' => 'ភាសាបរទេស និងអង់គ្លេស']);

        $books = [
            ['title' => 'គណិតវិទ្យា ថ្នាក់ទី ១១ (កម្រិតខ្ពស់)', 'author' => 'ក្រសួងអប់រំ យុវជន និងកីឡា', 'cat' => $catTextbook->id, 'isbn' => '978-99950-11-01', 'qty' => 60, 'avail' => 55],
            ['title' => 'អក្សរសាស្ត្រខ្មែរ ថ្នាក់ទី ១១', 'author' => 'ក្រសួងអប់រំ យុវជន និងកីឡា', 'cat' => $catTextbook->id, 'isbn' => '978-99950-11-02', 'qty' => 60, 'avail' => 58],
            ['title' => 'រូបវិទ្យា និងពិសោធន៍ជាក់ស្តែង ថ្នាក់ទី ១១', 'author' => 'ក្រសួងអប់រំ យុវជន និងកីឡា', 'cat' => $catTextbook->id, 'isbn' => '978-99950-11-03', 'qty' => 50, 'avail' => 47],
            ['title' => 'គីមីវិទ្យា ថ្នាក់ទី ១១', 'author' => 'ក្រសួងអប់រំ យុវជន និងកីឡា', 'cat' => $catTextbook->id, 'isbn' => '978-99950-11-04', 'qty' => 50, 'avail' => 48],
            ['title' => 'មូលដ្ឋានគ្រឹះបញ្ញាសិប្បនិម្មិត និង Python', 'author' => 'វិទ្យាស្ថានបច្ចេកវិទ្យាកម្ពុជា', 'cat' => $catSci->id, 'isbn' => '978-99950-04-10', 'qty' => 25, 'avail' => 20],
            ['title' => 'ប្រវត្តិសាស្ត្រខ្មែរសម័យអង្គរ និងក្រោយអង្គរ', 'author' => 'ត្រឹង ងា', 'cat' => $catLit->id, 'isbn' => '978-99950-05-20', 'qty' => 30, 'avail' => 26],
            ['title' => 'រឿងកុលាបប៉ៃលិន និងវិភាគតួអង្គ', 'author' => 'ញ៉ុក ថែម', 'cat' => $catLit->id, 'isbn' => '978-99950-05-21', 'qty' => 20, 'avail' => 19],
            ['title' => 'English Grammar in Use for Cambodian Students', 'author' => 'Raymond Murphy', 'cat' => $catEng->id, 'isbn' => '978-05211-89-01', 'qty' => 35, 'avail' => 31],
            ['title' => 'Oxford Advanced Learner\'s Dictionary', 'author' => 'Oxford University Press', 'cat' => $catEng->id, 'isbn' => '978-01947-99-02', 'qty' => 15, 'avail' => 14],
            ['title' => 'ជីវវិទ្យា និងប្រព័ន្ធអេកូឡូស៊ីនៅកម្ពុជា', 'author' => 'ក្រសួងបរិស្ថាន', 'cat' => $catSci->id, 'isbn' => '978-99950-04-15', 'qty' => 20, 'avail' => 18],
        ];

        $bookModels = [];
        foreach ($books as $b) {
            $bm = Book::updateOrCreate(
                ['isbn' => $b['isbn']],
                [
                    'book_category_id' => $b['cat'],
                    'title' => $b['title'],
                    'author' => $b['author'],
                    'publisher' => 'រោងពុម្ពអប់រំជាតិ',
                    'quantity' => $b['qty'],
                    'available_copies' => $b['avail'],
                ]
            );
            $bookModels[] = $bm;
        }

        // Active Borrow Records
        if (!empty($bookModels) && !empty($studentModels11D)) {
            for ($i = 0; $i < 5; $i++) {
                BookBorrow::updateOrCreate(
                    [
                        'book_id' => $bookModels[$i]->id,
                        'student_id' => $studentModels11D[$i]->id,
                    ],
                    [
                        'borrow_date' => Carbon::now()->subDays(5),
                        'due_date' => Carbon::now()->addDays(9),
                        'status' => 'borrowed',
                    ]
                );
            }
        }

        // Announcements
        $announcements = [
            [
                'title' => 'ការបើកបវេសនកាលឆ្នាំសិក្សាថ្មី ២០២៥-២០២៦',
                'content' => 'សាលារៀន ជំនាន់ថ្មី PDT សូមជម្រាបជូនដំណឹងដល់លោកគ្រូ អ្នកគ្រូ សិស្សានុសិស្ស និងមាតាបិតាទាំងអស់ឲ្យបានជ្រាបថា ការបើកបវេសនកាលបានចាប់ផ្តើមជាផ្លូវការស្របតាមស្តង់ដារបស់ក្រសួងអប់រំ យុវជន និងកីឡា។',
                'category' => 'ដំណឹង',
                'target_audience' => 'all',
                'publish_date' => Carbon::now()->subDays(20),
                'is_active' => true,
            ],
            [
                'title' => 'កាលវិភាគប្រឡងប្រចាំខែមករា សម្រាប់ថ្នាក់ទី ១០ និង ១១',
                'content' => 'ការប្រឡងប្រចាំខែមករានឹងត្រូវប្រព្រឹត្តទៅចាប់ពីថ្ងៃទី ២០ ដល់ថ្ងៃទី ២៥ ខែមករា។ សិស្សានុសិស្សថ្នាក់ ១០A និង ១១D ត្រូវត្រៀមមុខវិជ្ជាស្នូលទាំង ៨ ឲ្យបានគ្រប់ជ្រុងជ្រោយ។',
                'category' => 'ការប្រឡង',
                'target_audience' => 'students',
                'publish_date' => Carbon::now()->subDays(15),
                'is_active' => true,
            ],
            [
                'title' => 'ការប្រឡងឆមាសទី ១ ប្រចាំឆ្នាំសិក្សា ២០២៥-២០២៦',
                'content' => 'សម័យប្រឡងឆមាសទី ១ នឹងត្រូវរៀបចំឡើងជាផ្លូវការដើម្បីវាយតម្លៃលទ្ធផលសិក្សារបស់សិស្សានុសិស្សគ្រប់កម្រិតថ្នាក់។ សូមពិនិត្យតារាងពិន្ទុរួម និងកាតរបាយការណ៍ក្រោយការប្រឡងបញ្ចប់។',
                'category' => 'ការប្រឡង',
                'target_audience' => 'all',
                'publish_date' => Carbon::now()->subDays(5),
                'is_active' => true,
            ],
            [
                'title' => 'កិច្ចប្រជុំមាតាបិតា និងអាណាព្យាបាលសិស្សប្រចាំឆមាស',
                'content' => 'សាលានឹងរៀបចំកិច្ចប្រជុំមាតាបិតាសិស្សដើម្បីពិភាក្សាអំពីវឌ្ឍនភាពសិក្សា វត្តមាន និងលទ្ធផលប្រឡងរបស់សិស្សានុសិស្សនៅថ្ងៃអាទិត្យចុងសប្តាហ៍នេះ។',
                'category' => 'ព្រឹត្តិការណ៍',
                'target_audience' => 'parents',
                'publish_date' => Carbon::now()->subDays(2),
                'is_active' => true,
            ],
            [
                'title' => 'សេចក្តីជូនដំណឹងស្តីពីការប្រើប្រាស់បណ្ណាល័យឌីជីថល និងសៀវភៅជំនួយស្មារតី',
                'content' => 'បណ្ណាល័យសាលាបានបន្ថែមសៀវភៅពុម្ព និងឯកសារស្រាវជ្រាវថ្មីៗជាង ៣៥០ ក្បាល។ សិស្សានុសិស្សអាចខ្ចីសៀវភៅតាមរយៈប្រព័ន្ធគ្រប់គ្រងបណ្ណាល័យបានរៀងរាល់ថ្ងៃធ្វើការ។',
                'category' => 'ដំណឹង',
                'target_audience' => 'all',
                'publish_date' => Carbon::now()->subDays(1),
                'is_active' => true,
            ],
        ];

        foreach ($announcements as $an) {
            Announcement::updateOrCreate(
                ['title' => $an['title']],
                $an
            );
        }
    }
}
