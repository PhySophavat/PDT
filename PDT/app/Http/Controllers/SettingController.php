<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = SystemSetting::pluck('value', 'key')->toArray();
        $academicYears = AcademicYear::all();

        return view('setting.index', compact('settings', 'academicYears'));
    }

    public function update(Request $request)
    {
        $keys = [
            'school_name',
            'school_address',
            'school_phone',
            'school_email',
            'school_website',
            'current_academic_year',
            'current_semester',
            'currency',
            'currency_symbol',
            'grading_system',
        ];

        foreach ($keys as $k) {
            if ($request->has($k)) {
                SystemSetting::set($k, $request->input($k));
            }
        }

        return back()->with('success', 'បានរក្សាទុកការកំណត់ប្រព័ន្ធដោយជោគជ័យ!');
    }
}
