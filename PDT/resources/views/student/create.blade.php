@extends('layouts.app')

@section('content')
<div class="w-full mx-auto p-8 bg-white rounded-lg shadow-lg">
    <h1 class="text-2xl font-bold mb-6 text-gray-800 border-b-2 border-blue-600 pb-3">បន្ថែមសិស្ស</h1>
    <form action="{{ route('students.store') }}" method="POST">
        @csrf

        <!-- ព័ត៌មានសិស្ស -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold mb-4 text-blue-700 flex items-center">
                <span class="bg-blue-100 rounded-full w-8 h-8 flex items-center justify-center mr-2">1</span>
                ព័ត៌មានសិស្ស
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block font-medium mb-2 text-gray-700">ឈ្មោះសិស្ស <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" 
                           class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                           placeholder="បញ្ចូលឈ្មោះ" required>
                    @error('name') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-medium mb-2 text-gray-700">ថ្ងៃខែឆ្នាំកំណើត <span class="text-red-500">*</span></label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" 
                           class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                    @error('date_of_birth') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-medium mb-2 text-gray-700">ភេទ <span class="text-red-500">*</span></label>
                    <select name="gender" class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent" required>
                        <option value="">-- ជ្រើសរើស --</option>
                        <option value="ប្រុស" {{ old('gender') == 'ប្រុស' ? 'selected' : '' }}>ប្រុស</option>
                        <option value="ស្រី" {{ old('gender') == 'ស្រី' ? 'selected' : '' }}>ស្រី</option>
                    </select>
                    @error('gender') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-medium mb-2 text-gray-700">ថ្នាក់ <span class="text-red-500">*</span></label>
                    <input type="text" name="grade" value="{{ old('grade') }}" 
                           class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                           placeholder="ឧ. ៧A" required>
                    @error('grade') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- ព័ត៌មានទំនាក់ទំនង -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold mb-4 text-blue-700 flex items-center">
                <span class="bg-blue-100 rounded-full w-8 h-8 flex items-center justify-center mr-2">2</span>
                ព័ត៌មានទំនាក់ទំនង
            </h2>
            <div class="mb-4">
                <label class="block font-medium mb-2 text-gray-700">ទីកន្លែងស្នាក់នៅ</label>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
                    <input type="text" name="village" value="{{ old('village') }}" 
                           class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                           placeholder="ភូមិ">
                    <input type="text" name="commune" value="{{ old('commune') }}" 
                           class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                           placeholder="ឃុំ/សង្កាត់">
                    <input type="text" name="district" value="{{ old('district') }}" 
                           class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                           placeholder="ស្រុក/ខណ្ឌ">
                    <input type="text" name="province" value="{{ old('province') }}" 
                           class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                           placeholder="ខេត្ត/រាជធានី">
                </div>
                @error('village') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                @error('commune') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                @error('district') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                @error('province') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium mb-2 text-gray-700">លេខទូរស័ព្ទ</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" 
                           class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                           placeholder="012 345 678">
                    @error('phone') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block font-medium mb-2 text-gray-700">មកពីអនុវិទ្យាល័យ</label>
                    <input type="text" name="high_school" value="{{ old('high_school') }}" 
                           class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                           placeholder="ឈ្មោះអនុវិទ្យាល័យ">
                    @error('high_school') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- ព័ត៌មានឪពុកម្តាយ -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold mb-4 text-blue-700 flex items-center">
                <span class="bg-blue-100 rounded-full w-8 h-8 flex items-center justify-center mr-2">3</span>
                ព័ត៌មានឪពុកម្តាយ
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-gray-700 mb-3">ព័ត៌មានម្តាយ</h3>
                    <div class="mb-3">
                        <label class="block font-medium mb-2 text-gray-700">ឈ្មោះម្តាយ</label>
                        <input type="text" name="mother_name" value="{{ old('mother_name') }}" 
                               class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               placeholder="បញ្ចូលឈ្មោះម្តាយ">
                        @error('mother_name') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block font-medium mb-2 text-gray-700">លេខទូរស័ព្ទម្តាយ</label>
                        <input type="text" name="mother_phone" value="{{ old('mother_phone') }}" 
                               class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               placeholder="012 345 678">
                        @error('mother_phone') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-gray-700 mb-3">ព័ត៌មានឪពុក</h3>
                    <div class="mb-3">
                        <label class="block font-medium mb-2 text-gray-700">ឈ្មោះឪពុក</label>
                        <input type="text" name="father_name" value="{{ old('father_name') }}" 
                               class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               placeholder="បញ្ចូលឈ្មោះឪពុក">
                        @error('father_name') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block font-medium mb-2 text-gray-700">លេខទូរស័ព្ទឪពុក</label>
                        <input type="text" name="father_phone" value="{{ old('father_phone') }}" 
                               class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                               placeholder="012 345 678">
                        @error('father_phone') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- ព័ត៌មានគណនី -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold mb-4 text-blue-700 flex items-center">
                <span class="bg-blue-100 rounded-full w-8 h-8 flex items-center justify-center mr-2">4</span>
                ព័ត៌មានគណនី
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex gap-4 pt-4 border-t">
            <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition duration-200 flex-1 font-semibold shadow-md">
                បន្ថែមសិស្ស
            </button>
            <a href="{{ route('students.index') }}" class="bg-gray-200 text-gray-700 px-8 py-3 rounded-lg hover:bg-gray-300 transition duration-200 flex-1 text-center font-semibold">
                បោះបង់
            </a>
        </div>
    </form>
</div>
@endsection
