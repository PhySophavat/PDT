@extends('layouts.app')

@section('content')
<div class="w-full p-8 bg-white rounded-lg shadow-lg ">
    <h1 class="text-2xl font-bold mb-6 text-gray-800 border-b-2 border-blue-600 pb-3">បន្ថែមគ្រូ</h1>

    <form action="{{ route('teacher.store') }}" method="POST">
        @csrf

        <!-- ព័ត៌មានគ្រូ -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold mb-4 text-blue-700 flex items-center">
                <span class="bg-blue-100 rounded-full w-8 h-8 flex items-center justify-center mr-2">1</span>
                ព័ត៌មានគ្រូ
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block font-medium mb-2 text-gray-700">ឈ្មោះ <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" 
                           class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                           placeholder="បញ្ចូលឈ្មោះ" required>
                    @error('name') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-medium mb-2 text-gray-700">អ៊ីមែល <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" 
                           class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                           placeholder="example@email.com" required>
                    @error('email') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-medium mb-2 text-gray-700">ពាក្យសម្ងាត់ <span class="text-red-500">*</span></label>
                    <input type="password" name="password" 
                           class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                           placeholder="យ៉ាងតិច ៨ តួអក្សរ" required>
                    @error('password') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-medium mb-2 text-gray-700">មុខវិជ្ជា <span class="text-red-500">*</span></label>
                    <input type="text" name="subject" value="{{ old('subject') }}" 
                           class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                           placeholder="បញ្ចូលមុខវិជ្ជា" required>
                    @error('subject') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- ព័ត៌មានទំនាក់ទំនង និង Optional -->
<!-- Optional Info -->
<div class="mb-8">
    <h2 class="text-lg font-semibold mb-4 text-blue-700 flex items-center">
        <span class="bg-blue-100 rounded-full w-8 h-8 flex items-center justify-center mr-2">2</span>
        ព័ត៌មានបន្ថែម
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block font-medium mb-2 text-gray-700">លេខទូរស័ព្ទ</label>
            <input type="text" name="phone" value="{{ old('phone') }}" 
                   class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                   placeholder="012 345 678">
        </div>

        <div>
            <label class="block font-medium mb-2 text-gray-700">ថ្ងៃខែឆ្នាំកំណើត</label>
            <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" 
                   class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>

        <div>
            <label class="block font-medium mb-2 text-gray-700">ភេទ</label>
            <select name="gender" class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">-- ជ្រើសរើស --</option>
                <option value="ប្រុស" {{ old('gender') == 'ប្រុស' ? 'selected' : '' }}>ប្រុស</option>
                <option value="ស្រី" {{ old('gender') == 'ស្រី' ? 'selected' : '' }}>ស្រី</option>
            </select>
        </div>

        <div>
            <label class="block font-medium mb-2 text-gray-700">គ្រប់គ្រងថ្នាក់</label>
            <input type="text" name="class_assigned" value="{{ old('class_assigned') }}" 
                   class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
    </div>

    <!-- Full Row Address -->
    <div class="mt-4">
        <label class="block font-medium mb-2 text-gray-700">ទីកន្លែងស្នាក់នៅ</label>
        <div class="flex gap-2">
            <input type="text" name="village" value="{{ old('village') }}" placeholder="ភូមិ" class="border border-gray-300 rounded-lg p-3 flex-1 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <input type="text" name="commune" value="{{ old('commune') }}" placeholder="ឃុំ/សង្កាត់" class="border border-gray-300 rounded-lg p-3 flex-1 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <input type="text" name="district" value="{{ old('district') }}" placeholder="ស្រុក/ខណ្ឌ" class="border border-gray-300 rounded-lg p-3 flex-1 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            <input type="text" name="province" value="{{ old('province') }}" placeholder="ខេត្ត/រាជធានី" class="border border-gray-300 rounded-lg p-3 flex-1 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
    </div>
</div>



        <!-- Buttons -->
        <div class="flex gap-4 pt-4 border-t">
            <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition duration-200 flex-1 font-semibold shadow-md">
                បន្ថែមគ្រូ
            </button>
            <a href="{{ route('teacher.index') }}" class="bg-gray-200 text-gray-700 px-8 py-3 rounded-lg hover:bg-gray-300 transition duration-200 flex-1 text-center font-semibold">
                បោះបង់
            </a>
        </div>
    </form>
</div>
@endsection
