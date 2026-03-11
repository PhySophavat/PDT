@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6 bg-white rounded-lg shadow-md">
    <h1 class="text-2xl font-bold mb-6 text-center">ព័ត៌មានអំពីសិស្ស</h1>

    <!-- User Info -->
    <div class="mb-6">
        <h2 class="text-xl font-semibold mb-2 border-b pb-1">ព័ត៌មានអ្នកប្រើប្រាស់</h2>
        <table class="w-full text-left border border-gray-200">
            <tbody>
                <tr class="border-b">
                    <td class="px-4 py-2 font-medium">ឈ្មោះ:</td>
                    <td class="px-4 py-2">{{ Auth::user()->name }}</td>
                </tr>
                <tr class="border-b">
                    <td class="px-4 py-2 font-medium">អ៊ីម៉ែល:</td>
                    <td class="px-4 py-2">{{ Auth::user()->email }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    @if(Auth::user()->student)
    <!-- Student Info -->
    <div class="mb-6">
        <h2 class="text-xl font-semibold mb-2 border-b pb-1">ព័ត៌មានសិស្ស</h2>
        <table class="w-full text-left border border-gray-200">
            <tbody>
                <tr class="border-b">
                    <td class="px-4 py-2 font-medium">ថ្នាក់:</td>
                    <td class="px-4 py-2">{{ Auth::user()->student->grade }}</td>
                </tr>
                <tr class="border-b">
                    <td class="px-4 py-2 font-medium">ថ្ងៃខែឆ្នាំកំណើត:</td>
                    <td class="px-4 py-2">{{ Auth::user()->student->date_of_birth }}</td>
                </tr>
                <tr class="border-b">
                    <td class="px-4 py-2 font-medium">ភេទ:</td>
                    <td class="px-4 py-2">{{ Auth::user()->student->gender }}</td>
                </tr>

                <tr class="border-b">
                    <td class="px-4 py-2 font-medium">ភូមិ:</td>
                    <td class="px-4 py-2">{{ Auth::user()->student->village }}</td>
                </tr>
                <tr class="border-b">
                    <td class="px-4 py-2 font-medium">ឃុំ/សង្កាត់:</td>
                    <td class="px-4 py-2">{{ Auth::user()->student->commune }}</td>
                </tr>
                <tr class="border-b">
                    <td class="px-4 py-2 font-medium">ស្រុក/ខេត្ត:</td>
                    <td class="px-4 py-2">{{ Auth::user()->student->district }}, {{ Auth::user()->student->province }}</td>
                </tr>

                <tr class="border-b">
                    <td class="px-4 py-2 font-medium">ទូរស័ព្ទ:</td>
                    <td class="px-4 py-2">{{ Auth::user()->student->phone }}</td>
                </tr>
                <tr class="border-b">
                    <td class="px-4 py-2 font-medium">វិទ្យាល័យ:</td>
                    <td class="px-4 py-2">{{ Auth::user()->student->high_school }}</td>
                </tr>

                <tr class="border-b">
                    <td class="px-4 py-2 font-medium">ឈ្មោះម្ដាយ:</td>
                    <td class="px-4 py-2">{{ Auth::user()->student->mother_name }}</td>
                </tr>
                <tr class="border-b">
                    <td class="px-4 py-2 font-medium">ទូរស័ព្ទម្ដាយ:</td>
                    <td class="px-4 py-2">{{ Auth::user()->student->mother_phone }}</td>
                </tr>
                <tr class="border-b">
                    <td class="px-4 py-2 font-medium">ឈ្មោះឪពុក:</td>
                    <td class="px-4 py-2">{{ Auth::user()->student->father_name }}</td>
                </tr>
                <tr>
                    <td class="px-4 py-2 font-medium">ទូរស័ព្ទឪពុក:</td>
                    <td class="px-4 py-2">{{ Auth::user()->student->father_phone }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    @else
        <p class="text-red-500 font-semibold">មិនមានព័ត៌មានសិស្សទេ។</p>
    @endif
</div>
@endsection
