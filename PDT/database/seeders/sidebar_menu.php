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
        'label' => 'តួនាទី',
        'route' => 'role.index',
        'icon' => 'fa-solid fa-user-shield',
        'roles' => ['admin'],
        'active_patterns' => ['role.*', 'roles.*'],
    ],
    [
        'label' => 'គ្រូ',
        'route' => 'teacher.index',
        'icon' => 'fa-solid fa-chalkboard-teacher',
        'ability' => 'canManageTeachers',
        'active_patterns' => ['teacher.*'],
    ],
    [
        'label' => 'សិស្ស',
        'route' => 'students.index',
        'icon' => 'fa-solid fa-graduation-cap',
        'ability' => 'canViewStudents',
        'active_patterns' => ['students.*', 'student.*'],
    ],
    [
        'label' => 'ថ្នាក់',
        'route' => 'class.index',
        'icon' => 'fa-solid fa-school',
        'ability' => 'canAccessClassesOverview',
        'active_patterns' => ['class.*', 'classes.*'],
    ],
    [
        'label' => 'លទ្ធផល',
        'route' => 'result.index',
        'icon' => 'fa-solid fa-file',
        'ability' => 'canAccessResultsOverview',
        'active_patterns' => ['result.*', 'rank.*'],
    ],
];
