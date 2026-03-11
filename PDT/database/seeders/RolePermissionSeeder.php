<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Teacher;
use App\Models\Student;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // === 1. Basic permissions ===
        $basicPermissions = [
            ['name' => 'manage-teacher', 'display_name' => 'គ្រប់គ្រងគ្រូបង្រៀន'],
            ['name' => 'add-student', 'display_name' => 'បន្ថែមសិស្ស'],
            ['name' => 'view', 'display_name' => 'មើលទិន្នន័យ'],
            ['name' => 'edit', 'display_name' => 'កែប្រែទិន្នន័យ'],
            ['name' => 'delete', 'display_name' => 'លុបទិន្នន័យ'],
            ['name' => 'view-student', 'display_name' => 'មើលប្រវត្តិសិស្ស'],
        ];

        foreach ($basicPermissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm['name']],
                ['display_name' => $perm['display_name']]
            );
        }

        // === 2. Add permissions for each class (7A → 12H) ===
        $grades = [
            7 => ['A','B','C','D','E','F','G','H'],
            8 => ['A','B','C','D','E','F','G','H'],
            9 => ['A','B','C','D','E','F','G','H'],
            10 => ['A','B','C','D','E','F','G','H'],
            11 => ['A','B','C','D','E','F','G','H'],
            12 => ['A','B','C','D','E','F','G','H'],
        ];

        foreach ($grades as $grade => $sections) {
            foreach ($sections as $section) {
                $name = "class-{$grade}{$section}";
                $display = "ថ្នាក់ទី {$grade}{$section}";
                Permission::firstOrCreate(['name' => $name], ['display_name' => $display]);
            }
        }

        // === 3. Add subject permissions ===
        $subjects = [
            'math' => 'គណិតវិទ្យា',
            'khmer' => 'ភាសាខ្មែរ',
            'english' => 'ភាសាអង់គ្លេស',
            'history' => 'ប្រវត្តិវិទ្យា',
            'geography' => 'ភូមិវិទ្យា',
            'chemistry' => 'គីមីវិទ្យា',
            'physics' => 'រូបវិទ្យា',
            'biology' => 'ជីវវិទ្យា',
            'morality' => 'សីលធម៌',
            'sport' => 'កីឡា',
        ];

        foreach ($subjects as $key => $value) {
            Permission::firstOrCreate(
                ['name' => "subject-{$key}"],
                ['display_name' => $value]
            );
        }

        // === 4. Define roles and assign permissions ===
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'អ្នកគ្រប់គ្រងប្រព័ន្ធ',
                'description' => 'មានសិទ្ធិគ្រប់យ៉ាង',
                'permissions' => Permission::pluck('id')->toArray(), // Admin gets all
            ],
            [
                'name' => 'teacher',
                'display_name' => 'គ្រូបង្រៀន',
                'description' => 'គ្រូអាចគ្រប់គ្រងសិស្ស និងមើលថ្នាក់',
                'permissions' => Permission::where('name', 'like', 'subject-%')->pluck('id')->toArray(),
            ],
            [
                'name' => 'student',
                'display_name' => 'សិស្ស',
                'description' => 'អាចមើលប្រវត្តិផ្ទាល់ខ្លួនបានតែប៉ុណ្ណោះ',
                'permissions' => Permission::where('name', 'view-student')->pluck('id')->toArray(),
            ],
        ];

        foreach ($roles as $r) {
            $role = Role::firstOrCreate(
                ['name' => $r['name']],
                [
                    'display_name' => $r['display_name'],
                    'description' => $r['description'],
                ]
            );
            $role->permissions()->sync($r['permissions']);
        }

        // === 5. Create default users ===
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => Hash::make('password')]
        );
        $teacherUser = User::firstOrCreate(
            ['email' => 'teacher@example.com'],
            ['name' => 'Teacher', 'password' => Hash::make('password')]
        );
        $studentUser = User::firstOrCreate(
            ['email' => 'student@example.com'],
            ['name' => 'Student', 'password' => Hash::make('password')]
        );

        $adminUser->roles()->sync(Role::where('name', 'admin')->pluck('id'));
        $teacherUser->roles()->sync(Role::where('name', 'teacher')->pluck('id'));
        $studentUser->roles()->sync(Role::where('name', 'student')->pluck('id'));

        // === 6. Attach teacher/student models ===
        Teacher::firstOrCreate(['user_id' => $teacherUser->id], ['subject' => 'គណិតវិទ្យា']);
        Student::firstOrCreate(['user_id' => $studentUser->id], ['grade' => '7A']);
    }
}
