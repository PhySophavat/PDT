@extends('layouts.app')

@php
    $sysSettings = \App\Models\SystemSetting::allSettings();
    $schoolName = $sysSettings['school_name'] ?? 'សាលារៀន ជំនាន់ថ្មី';
    $path = trim(request()->path(), '/');

    $pageTitles = [
        'library' => 'បណ្ណាល័យឌីជីថល (Digital Library) | ' . $schoolName,
        'dashboard' => 'ផ្ទាំងគ្រប់គ្រងទូទៅ | ' . $schoolName,
        'attendance' => 'ការគ្រប់គ្រងវត្តមានសិស្ស | ' . $schoolName,
        'examinations' => 'ការប្រឡង និងតារាងចំណាត់ថ្នាក់ | ' . $schoolName,
        'students' => 'បញ្ជីឈ្មោះសិស្សានុសិស្ស | ' . $schoolName,
        'teachers' => 'បញ្ជីឈ្មោះលោកគ្រូ-អ្នកគ្រូ | ' . $schoolName,
    ];
    $pageTitle = $pageTitles[$path] ?? ($schoolName . ' | ប្រព័ន្ធគ្រប់គ្រងសាលារៀន និងបណ្ណាល័យឌីជីថល');

    $pageDescriptions = [
        'library' => 'ស្វែងរក និងអានសៀវភៅសិក្សាគោលក្រសួងអប់រំ យុវជន និងកីឡា ថ្នាក់ទី ៧ ដល់ទី ១២ គីមីវិទ្យា គណិតវិទ្យា រូបវិទ្យា និងជីវវិទ្យា នៃ' . $schoolName . '។',
        'dashboard' => 'ប្រព័ន្ធគ្រប់គ្រងសាលារៀន និងបណ្ណាល័យឌីជីថលកម្ពុជា ' . $schoolName . ' - វត្តមាន ពិន្ទុ និងព័ត៌មានទូទៅ។',
    ];
    $pageDesc = $pageDescriptions[$path] ?? ('ប្រព័ន្ធគ្រប់គ្រងសាលារៀន និងបណ្ណាល័យឌីជីថល ' . $schoolName . ' ផ្តល់ជូនសៀវភៅសិក្សាគោល MoEYS ពីថ្នាក់ទី ៧ ដល់ទី ១២។');
@endphp

@section('title', $pageTitle)
@section('meta_description', $pageDesc)
@section('og_title', $pageTitle)
@section('og_description', $pageDesc)
