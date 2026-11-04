<?php

return [
    [
        'label' => 'ទំព័រដើម',
        'route' => 'dashboard',
        'icon' => 'fa-solid fa-house',
        'roles' => ['admin'],
        'active_patterns' => ['dashboard'],
    ],
    [
        'label' => 'ការសិក្សា & ថ្នាក់',
        'icon' => 'fa-solid fa-school',
        'roles' => ['admin', 'teacher'],
        'active_patterns' => ['class.*', 'classes.*', 'course-subjects.*', 'academic-years.*', 'timetables.*'],
        'children' => [
            [
                'label' => 'គ្រប់គ្រងថ្នាក់រៀន',
                'route' => 'class.index',
                'icon' => 'fa-solid fa-chalkboard',
                'active_patterns' => ['class.*', 'classes.*'],
            ],
            [
                'label' => 'មុខវិជ្ជាសិក្សា',
                'route' => 'course-subjects.index',
                'icon' => 'fa-solid fa-book-open',
                'active_patterns' => ['course-subjects.*'],
            ],
            [
                'label' => 'ឆ្នាំសិក្សា & ឆមាស',
                'route' => 'academic-years.index',
                'icon' => 'fa-solid fa-calendar-days',
                'active_patterns' => ['academic-years.*'],
            ],
            [
                'label' => 'កាលវិភាគបង្រៀន',
                'route' => 'timetables.index',
                'icon' => 'fa-solid fa-clock',
                'active_patterns' => ['timetables.*'],
            ],
        ],
    ],
    [
        'label' => 'សិស្ស & គ្រូបង្រៀន',
        'icon' => 'fa-solid fa-users',
        'roles' => ['admin', 'teacher'],
        'active_patterns' => ['students.*', 'student.*', 'teacher.*', 'parents.*'],
        'children' => [
            [
                'label' => 'គ្រប់គ្រងសិស្ស',
                'route' => 'students.index',
                'icon' => 'fa-solid fa-graduation-cap',
                'active_patterns' => ['students.*', 'student.*'],
            ],
            [
                'label' => 'គ្រប់គ្រងគ្រូ',
                'route' => 'teacher.index',
                'icon' => 'fa-solid fa-chalkboard-user',
                'active_patterns' => ['teacher.*'],
            ],
            [
                'label' => 'អាណាព្យាបាល',
                'route' => 'parents.index',
                'icon' => 'fa-solid fa-user-group',
                'active_patterns' => ['parents.*'],
            ],
        ],
    ],
    [
        'label' => 'វត្តមាន & ការប្រឡង',
        'icon' => 'fa-solid fa-clipboard-check',
        'roles' => ['admin', 'teacher'],
        'active_patterns' => ['attendance.*', 'examinations.*', 'result.*', 'rank.*', 'subject.*'],
        'children' => [
            [
                'label' => 'កត់ត្រាវត្តមានសិស្ស',
                'route' => 'attendance.index',
                'icon' => 'fa-solid fa-user-check',
                'active_patterns' => ['attendance.index'],
            ],
            [
                'label' => 'វត្តមានលោកគ្រូអ្នកគ្រូ',
                'route' => 'attendance.teachers',
                'icon' => 'fa-solid fa-clipboard-user',
                'active_patterns' => ['attendance.teachers'],
            ],
            [
                'label' => 'ការប្រឡង & បញ្ចូលពិន្ទុ',
                'route' => 'examinations.index',
                'icon' => 'fa-solid fa-square-poll-vertical',
                'active_patterns' => ['examinations.*'],
            ],
            [
                'label' => 'លទ្ធផល & ចំណាត់ថ្នាក់',
                'route' => 'result.index',
                'icon' => 'fa-solid fa-award',
                'active_patterns' => ['result.*', 'rank.*', 'subject.*'],
            ],
        ],
    ],
    [
        'label' => 'ហិរញ្ញវត្ថុ & បណ្ណាល័យ',
        'icon' => 'fa-solid fa-sack-dollar',
        'roles' => ['admin'],
        'active_patterns' => ['finance.*', 'library.*'],
        'children' => [
            [
                'label' => 'គ្រប់គ្រងហិរញ្ញវត្ថុ',
                'route' => 'finance.index',
                'icon' => 'fa-solid fa-file-invoice-dollar',
                'active_patterns' => ['finance.*'],
            ],
            [
                'label' => 'បណ្ណាល័យ & ខ្ចីសៀវភៅ',
                'route' => 'library.index',
                'icon' => 'fa-solid fa-book',
                'active_patterns' => ['library.*'],
            ],
        ],
    ],
    [
        'label' => 'ដំណឹង & របាយការណ៍',
        'icon' => 'fa-solid fa-chart-line',
        'roles' => ['admin', 'teacher'],
        'active_patterns' => ['announcements.*', 'reports.*'],
        'children' => [
            [
                'label' => 'សេចក្តីជូនដំណឹង',
                'route' => 'announcements.index',
                'icon' => 'fa-solid fa-bullhorn',
                'active_patterns' => ['announcements.*'],
            ],
            [
                'label' => 'របាយការណ៍សរុប',
                'route' => 'reports.index',
                'icon' => 'fa-solid fa-chart-pie',
                'active_patterns' => ['reports.*'],
            ],
        ],
    ],
    [
        'label' => 'ការកំណត់ & សិទ្ធិ',
        'icon' => 'fa-solid fa-gear',
        'roles' => ['admin'],
        'active_patterns' => ['role.*', 'roles.*', 'settings.*'],
        'children' => [
            [
                'label' => 'តួនាទី & សិទ្ធិ',
                'route' => 'role.index',
                'icon' => 'fa-solid fa-user-shield',
                'active_patterns' => ['role.*', 'roles.*'],
            ],
            [
                'label' => 'ការកំណត់ប្រព័ន្ធ',
                'route' => 'settings.index',
                'icon' => 'fa-solid fa-sliders',
                'active_patterns' => ['settings.*'],
            ],
        ],
    ],
];
