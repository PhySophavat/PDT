<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ensure super_admin and admin roles exist in roles table
        $superAdminRole = Role::firstOrCreate(
            ['name' => 'super_admin'],
            [
                'display_name' => 'អភិបាលជាន់ខ្ពស់ (Super Admin)',
                'description' => 'មានសិទ្ធិមើល Activity Logs និងគ្រប់គ្រងប្រព័ន្ធកម្រិតខ្ពស់'
            ]
        );
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'អ្នកគ្រប់គ្រង (Admin)',
                'description' => 'គ្រប់គ្រងទិន្នន័យទូទៅ'
            ]
        );

        // Ensure superadmin@example.com exists with super_admin role
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Administrator',
                'password' => \Illuminate\Support\Facades\Hash::make('password')
            ]
        );
        $superAdmin->roles()->syncWithoutDetaching([$superAdminRole->id, $adminRole->id]);

        // Keep admin@example.com as regular admin
        $admin = User::where('email', 'admin@example.com')->first();
        if ($admin) {
            $admin->roles()->sync([$adminRole->id]);
        }

        // 2. Insert realistic sample activity logs for demonstration
        $samples = [
            [
                'user_id' => $admin?->id ?: 1,
                'user_name' => $admin?->name ?: 'School Administrator',
                'user_role' => 'Super Admin',
                'action' => 'LOGIN',
                'module' => 'ចូលប្រើប្រព័ន្ធ',
                'target_id' => '1',
                'description' => 'បានចូលប្រើប្រព័ន្ធដោយជោគជ័យ (admin@example.com)',
                'device_name' => 'Windows 11/10 - Chrome',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36',
                'payload' => null,
                'created_at' => now()->subMinutes(45),
            ],
            [
                'user_id' => $admin?->id ?: 1,
                'user_name' => $admin?->name ?: 'School Administrator',
                'user_role' => 'Super Admin',
                'action' => 'CREATED',
                'module' => 'បណ្ណាល័យ',
                'target_id' => '99',
                'description' => 'បានបន្ថែមសៀវភៅថ្មី «រឿងកុលាបប៉ៃលិន» ចូលក្នុងបណ្ណាល័យឌីជីថល',
                'device_name' => 'Windows 11/10 - Chrome',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36',
                'payload' => [
                    'old_values' => [],
                    'new_values' => [
                        'title' => 'រឿងកុលាបប៉ៃលិន',
                        'author' => 'ញ៉ុក ថែម',
                        'grade' => 'ទូទៅ',
                        'level' => 'អក្សរសិល្ប៍',
                        'publisher' => 'ក្រសួងអប់រំ យុវជន និងកីឡា',
                        'isbn' => '978-99950-LIT-02',
                        'quantity' => 10,
                    ]
                ],
                'created_at' => now()->subMinutes(30),
            ],
            [
                'user_id' => $admin?->id ?: 1,
                'user_name' => 'លោកគ្រូ សុខ វិសាល',
                'user_role' => 'គ្រូបង្រៀន (Teacher)',
                'action' => 'UPDATED',
                'module' => 'ពិន្ទុប្រឡង',
                'target_id' => '245',
                'description' => 'បានកែប្រែពិន្ទុប្រឡងប្រចាំខែវិច្ឆិកា មុខវិជ្ជាគណិតវិទ្យា ថ្នាក់ទី១១ក',
                'device_name' => 'macOS - Safari',
                'ip_address' => '192.168.1.105',
                'user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Safari/605.1.15',
                'payload' => [
                    'old_values' => [
                        'score' => 74.5,
                        'grade_letter' => 'C',
                        'updated_by' => 'Auto-Import',
                    ],
                    'new_values' => [
                        'score' => 88.0,
                        'grade_letter' => 'B',
                        'updated_by' => 'គ្រូ សុខ វិសាល',
                    ]
                ],
                'created_at' => now()->subMinutes(18),
            ],
            [
                'user_id' => null,
                'user_name' => 'ភ្ញៀវ (Guest)',
                'user_role' => 'ភ្ញៀវ (Guest)',
                'action' => 'FAILED_LOGIN',
                'module' => 'ចូលប្រើប្រព័ន្ធ',
                'target_id' => null,
                'description' => 'ការប៉ុនប៉ងចូលប្រើប្រព័ន្ធមិនជោគជ័យ (Email: user_test@school.com) - ពាក្យសម្ងាត់មិនត្រឹមត្រូវ',
                'device_name' => 'Android - Chrome',
                'ip_address' => '103.216.48.12',
                'user_agent' => 'Mozilla/5.0 (Linux; Android 14; SM-S928B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/127.0.6533.103 Mobile Safari/537.36',
                'payload' => null,
                'created_at' => now()->subMinutes(12),
            ],
            [
                'user_id' => $admin?->id ?: 1,
                'user_name' => $admin?->name ?: 'School Administrator',
                'user_role' => 'Super Admin',
                'action' => 'UPDATED',
                'module' => 'ការកំណត់ប្រព័ន្ធ',
                'target_id' => '1',
                'description' => 'បានកែប្រែកាលវិភាគសិក្សា និងឆ្នាំសិក្សាគោល ២០២៥-២០២៦',
                'device_name' => 'Windows 11/10 - Chrome',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/128.0.0.0 Safari/537.36',
                'payload' => [
                    'old_values' => [
                        'current_academic_year' => '2024-2025',
                        'pwa_enabled' => false,
                    ],
                    'new_values' => [
                        'current_academic_year' => '2025-2026',
                        'pwa_enabled' => true,
                    ]
                ],
                'created_at' => now()->subMinutes(5),
            ],
        ];

        foreach ($samples as $s) {
            ActivityLog::create($s);
        }
    }
}

