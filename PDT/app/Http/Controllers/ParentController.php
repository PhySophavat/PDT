<?php

namespace App\Http\Controllers;

use App\Models\SchoolParent;
use App\Models\Student;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ParentController extends Controller
{
    /**
     * Show the public parent registration form.
     */
    public function showPublicRegistration()
    {
        $classes = SchoolClass::all();
        $relationships = [
            'ឪពុក (Father)',
            'ម្តាយ (Mother)',
            'អាណាព្យាបាល (Guardian)',
            'បងប្អូនបង្កើត (Sibling)'
        ];

        return view('public.register_parent', compact('classes', 'relationships'));
    }

    /**
     * Handle public parent registration submission.
     */
    public function submitPublicRegistration(Request $request)
    {
        $validated = $request->validate([
            'name_khmer' => 'required|string|max:255',
            'name_latin' => 'nullable|string|max:255',
            'gender' => 'required|in:male,female',
            'relationship' => 'required|string|max:100',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'occupation' => 'nullable|string|max:255',
            'address' => 'required|string|max:500',
            'child_name' => 'required|string|max:255',
            'child_grade' => 'nullable|string|max:50',
            'photo' => 'nullable|image|max:3072',
        ], [
            'name_khmer.required' => 'សូមបញ្ចូលឈ្មោះពេញជាភាសាខ្មែរ',
            'gender.required' => 'សូមជ្រើសរើសភេទ',
            'relationship.required' => 'សូមជ្រើសរើសទំនាក់ទំនង (ឪពុក, ម្តាយ, អាណាព្យាបាល)',
            'phone.required' => 'សូមបញ្ចូលលេខទូរស័ព្ទ',
            'address.required' => 'សូមបញ្ចូលអាសយដ្ឋានបច្ចុប្បន្ន',
            'child_name.required' => 'សូមបញ្ចូលឈ្មោះសិស្ស (កូន ឬកុមារក្នុងបន្ទុក)',
            'photo.image' => 'ឯកសាររូបថតត្រូវតែជាប្រភេទរូបភាព (JPG, PNG, WebP)',
            'photo.max' => 'រូបថតមិនត្រូវលើសពី 3MB ឡើយ',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('parents/photos', 'public');
        }

        $year = date('Y');
        $last = SchoolParent::whereNotNull('parent_id_card')
            ->where('parent_id_card', 'like', "PAR-{$year}-%")
            ->orderBy('id', 'desc')
            ->first();
        $nextNum = 1;
        if ($last && preg_match('/PAR-\d{4}-(\d+)/', $last->parent_id_card, $matches)) {
            $nextNum = intval($matches[1]) + 1;
        } else {
            $nextNum = SchoolParent::whereNotNull('parent_id_card')->count() + 1;
        }
        $parentIdCard = sprintf('PAR-%s-%03d', $year, $nextNum);

        $parent = SchoolParent::create([
            'parent_id_card' => $parentIdCard,
            'name' => $validated['name_khmer'],
            'name_khmer' => $validated['name_khmer'],
            'name_latin' => $validated['name_latin'] ?? null,
            'gender' => $validated['gender'],
            'relationship' => $validated['relationship'],
            'phone' => $validated['phone'],
            'email' => $validated['email'] ?? null,
            'occupation' => $validated['occupation'] ?? null,
            'address' => $validated['address'],
            'child_name' => $validated['child_name'],
            'child_grade' => $validated['child_grade'] ?? null,
            'photo' => $photoPath,
            'status' => 'pending',
        ]);

        // Auto-link with existing student if matching child_name
        $student = Student::where('name_khmer', 'like', '%' . $validated['child_name'] . '%')
            ->orWhere('name_latin', 'like', '%' . $validated['child_name'] . '%')
            ->first();
        if ($student) {
            $parent->students()->syncWithoutDetaching([$student->id => ['relationship' => $validated['relationship']]]);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'ការចុះឈ្មោះអាណាព្យាបាលត្រូវបានបញ្ជូនដោយជោគជ័យ! សូមរង់ចាំការត្រួតពិនិត្យពីសាលា។',
                'parent_id' => $parent->id
            ]);
        }

        return redirect()->route('public.register-parent')->with('registration_success', true);
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));

        $parents = SchoolParent::with('students.user')
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $allStudents = Student::with('user')->get();

        return response()->json(['parents' => $parents, 'allStudents' => $allStudents]);
    }

    public function show(SchoolParent $parent)
    {
        $parent->load([
            'students.user',
            'students.attendances' => fn ($q) => $q->latest('date')->take(10),
            'students.examinationScores.examination',
            'students.invoices.payments',
        ]);

        return response()->json(['parent' => $parent]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'occupation' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'relationship' => 'required|string|max:50',
            'student_ids' => 'nullable|array',
        ]);

        $parent = SchoolParent::create($validated);

        if (!empty($validated['student_ids'])) {
            $parent->students()->sync($validated['student_ids']);
        }

        return response()->json(['success' => true, 'message' => 'បានបន្ថែមអាណាព្យាបាលថ្មីដោយជោគជ័យ!', 'parent' => $parent]);
    }

    public function update(Request $request, SchoolParent $parent)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'occupation' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'relationship' => 'required|string|max:50',
            'student_ids' => 'nullable|array',
        ]);

        $parent->update($validated);

        if (isset($validated['student_ids'])) {
            $parent->students()->sync($validated['student_ids']);
        }

        return response()->json(['success' => true, 'message' => 'បានកែប្រែព័ត៌មានអាណាព្យាបាលដោយជោគជ័យ!', 'parent' => $parent]);
    }

    public function destroy(SchoolParent $parent)
    {
        $parent->students()->detach();
        $parent->delete();

        return response()->json(['success' => true, 'message' => 'បានលុបព័ត៌មានអាណាព្យាបាលដោយជោគជ័យ!']);
    }
}
