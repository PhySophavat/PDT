<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\CourseSubject;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;

class ProductionSetupSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Roles & Permissions + Admin User
        $this->call(RolePermissionSeeder::class);

        // Ensure Admin user has password 'password' or existing
        $adminRole = Role::firstOrCreate(['name' => 'admin'], ['display_name' => 'អ្នកគ្រប់គ្រងប្រព័ន្ធ']);
        $admin = User::firstOrCreate(
            ['email' => 'admin@pdt-school.edu.kh'],
            [
                'name' => 'PDT Admin',
                'password' => Hash::make('admin1234'),
            ]
        );
        $admin->roles()->syncWithoutDetaching([$adminRole->id]);

        // 2. Academic Years
        $ayCurrent = AcademicYear::updateOrCreate(
            ['name' => '2026-2027'],
            [
                'start_date' => '2026-10-01',
                'end_date' => '2027-07-31',
                'semester_1' => 'Oct-Feb',
                'semester_2' => 'Mar-Jul',
                'is_current' => true,
                'status' => 'active',
            ]
        );

        AcademicYear::updateOrCreate(
            ['name' => '2025-2026'],
            [
                'start_date' => '2025-10-01',
                'end_date' => '2026-07-31',
                'semester_1' => 'Oct-Feb',
                'semester_2' => 'Mar-Jul',
                'is_current' => false,
                'status' => 'active',
            ]
        );

        // 3. School Settings
        $settings = [
            'school_name' => 'សាលារៀន ជំនាន់ថ្មី PDT',
            'school_name_en' => 'PDT New Generation School',
            'school_address' => 'មហាវិថីសហព័ន្ធរុស្ស៊ី សង្កាត់ទឹកល្អក់១ ខណ្ឌទួលគោក រាជធានីភ្នំពេញ',
            'school_phone' => '023 880 880 / 012 345 678',
            'school_email' => 'contact@pdt-school.edu.kh',
            'school_website' => 'https://pdt-school.edu.kh',
            'school_logo' => '/images/logo.png',
            'current_academic_year' => '2026-2027',
            'current_semester' => '1',
            'demo_mode' => '0',
            'grading_system' => 'A:90-100, B:80-89, C:70-79, D:60-69, E:50-59, F:0-49',
        ];

        foreach ($settings as $k => $v) {
            SystemSetting::set($k, $v);
        }

        // 4. Subjects & Curriculum
        $subjectsData = [
            ['code' => 'KHM-01', 'name' => 'អក្សរសាស្ត្រខ្មែរ', 'credit' => 2.0, 'max_score' => 100.0, 'credit_hours' => 5],
            ['code' => 'MTH-01', 'name' => 'គណិតវិទ្យា', 'credit' => 2.0, 'max_score' => 100.0, 'credit_hours' => 5],
            ['code' => 'PHY-01', 'name' => 'រូបវិទ្យា', 'credit' => 1.5, 'max_score' => 100.0, 'credit_hours' => 3],
            ['code' => 'CHM-01', 'name' => 'គីមីវិទ្យា', 'credit' => 1.5, 'max_score' => 100.0, 'credit_hours' => 3],
            ['code' => 'BIO-01', 'name' => 'ជីវវិទ្យា', 'credit' => 1.5, 'max_score' => 100.0, 'credit_hours' => 3],
            ['code' => 'HIS-01', 'name' => 'ប្រវត្តិវិទ្យា', 'credit' => 1.0, 'max_score' => 100.0, 'credit_hours' => 2],
            ['code' => 'ETH-01', 'name' => 'សីលធម៌-ពលរដ្ឋ', 'credit' => 1.0, 'max_score' => 100.0, 'credit_hours' => 2],
            ['code' => 'ENG-01', 'name' => 'ភាសាអង់គ្លេស', 'credit' => 1.0, 'max_score' => 100.0, 'credit_hours' => 3],
            ['code' => 'GEO-01', 'name' => 'ភូមិវិទ្យា', 'credit' => 1.0, 'max_score' => 100.0, 'credit_hours' => 2],
            ['code' => 'ICT-01', 'name' => 'ព័ត៌មានវិទ្យា (ICT & Coding)', 'credit' => 1.5, 'max_score' => 100.0, 'credit_hours' => 3],
            ['code' => 'SPO-01', 'name' => 'អប់រំកាយ និងកីឡា', 'credit' => 1.0, 'max_score' => 100.0, 'credit_hours' => 2],
        ];

        foreach ($subjectsData as $s) {
            CourseSubject::updateOrCreate(
                ['code' => $s['code']],
                [
                    'name' => $s['name'],
                    'credit' => $s['credit'],
                    'max_score' => $s['max_score'],
                    'credit_hours' => $s['credit_hours'],
                    'grade_level' => '7-12',
                    'description' => 'កម្មវិធីសិក្សាជំនាន់ថ្មី PDT',
                ]
            );
        }

        // 5. 48 Classrooms (Grade 7 - 12, Sections A - H)
        $grades = [7, 8, 9, 10, 11, 12];
        $sections = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];

        foreach ($grades as $g) {
            foreach ($sections as $s) {
                $className = $g . $s;
                $room = 'B-' . $g . '0' . (ord($s) - 64);
                SchoolClass::updateOrCreate(
                    ['name' => $className],
                    [
                        'grade' => (string)$g,
                        'section' => $s,
                        'room_number' => $room,
                        'capacity' => 45,
                        'teacher_id' => null,
                        'academic_year_id' => $ayCurrent->id,
                        'description' => "ថ្នាក់រៀនទី {$g} បន្ទប់ {$s} (ជំនាន់ថ្មី PDT)",
                    ]
                );
            }
        }
    }
}
