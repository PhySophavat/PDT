<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::withCount(['classes', 'students'])
            ->latest()
            ->paginate(10);

        return view('academic_year.index', compact('academicYears'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:academic_years,name',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'semester_1' => 'nullable|string|max:100',
            'semester_2' => 'nullable|string|max:100',
            'is_current' => 'nullable|boolean',
            'status' => 'required|in:active,inactive',
        ]);

        if (!empty($validated['is_current'])) {
            AcademicYear::where('is_current', true)->update(['is_current' => false]);
            $validated['is_current'] = true;
            \App\Models\SystemSetting::set('current_academic_year', $validated['name']);
        }

        $year = AcademicYear::create($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'បានបង្កើតឆ្នាំសិក្សាថ្មីដោយជោគជ័យ!', 'year' => $year]);
        }

        return redirect()->route('academic-years.index')->with('success', 'បានបង្កើតឆ្នាំសិក្សាថ្មីដោយជោគជ័យ!');
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:academic_years,name,' . $academicYear->id,
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'semester_1' => 'nullable|string|max:100',
            'semester_2' => 'nullable|string|max:100',
            'is_current' => 'nullable|boolean',
            'status' => 'required|in:active,inactive',
        ]);

        if (!empty($validated['is_current'])) {
            AcademicYear::where('id', '!=', $academicYear->id)->update(['is_current' => false]);
            $validated['is_current'] = true;
            \App\Models\SystemSetting::set('current_academic_year', $validated['name']);
        }

        $academicYear->update($validated);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'បានកែប្រែឆ្នាំសិក្សាដោយជោគជ័យ!', 'year' => $academicYear]);
        }

        return redirect()->route('academic-years.index')->with('success', 'បានកែប្រែឆ្នាំសិក្សាដោយជោគជ័យ!');
    }

    public function setCurrent(AcademicYear $academicYear)
    {
        AcademicYear::where('id', '!=', $academicYear->id)->update(['is_current' => false]);
        $academicYear->update(['is_current' => true, 'status' => 'active']);
        \App\Models\SystemSetting::set('current_academic_year', $academicYear->name);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'បានកំណត់ ' . $academicYear->name . ' ជាឆ្នាំសិក្សាបច្ចុប្បន្ន!', 'year' => $academicYear]);
        }

        return back()->with('success', 'បានកំណត់ ' . $academicYear->name . ' ជាឆ្នាំសិក្សាបច្ចុប្បន្ន!');
    }

    public function destroy(AcademicYear $academicYear)
    {
        if ($academicYear->is_current) {
            return back()->with('error', 'មិនអាចលុបឆ្នាំសិក្សាដែលកំពុងដំណើរការបច្ចុប្បន្នបានទេ!');
        }

        $academicYear->delete();

        return redirect()->route('academic-years.index')->with('success', 'បានលុបឆ្នាំសិក្សាដោយជោគជ័យ!');
    }
}
