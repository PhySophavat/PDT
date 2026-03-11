@extends('layouts.app')

@section('content')
<div class="w-full mx-auto p-8 bg-white rounded-lg shadow-lg">
    <!-- Back Button -->
    <div class="mb-6 flex items-center justify-between">
   
    <h1 class="text-2xl font-bold text-gray-800 border-b-2 border-blue-600 pb-3">
        កែប្រែព័ត៌មានសិស្ស
    </h1>
      <a href="{{ route('student.index') }}" 
       class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
        <i class="fa-solid fa-arrow-left mr-2"></i> ត្រលប់ក្រោយ
    </a>

</div>



    <form action="{{ route('students.update', $student) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- ព័ត៌មានសិស្ស -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold mb-4 text-blue-700 flex items-center">
                <span class="bg-blue-100 rounded-full w-8 h-8 flex items-center justify-center mr-2">1</span>
                ព័ត៌មានសិស្ស
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block font-medium mb-2 text-gray-700">ឈ្មោះសិស្ស <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $student->user->name) }}" 
                           class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                           required>
                    @error('name') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-medium mb-2 text-gray-700">អ៊ីមែល <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $student->user->email) }}" 
                           class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                           required>
                    @error('email') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-medium mb-2 text-gray-700">ពាក្យសម្ងាត់</label>
                    <input type="password" name="password" 
                           class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                           placeholder="ទុកទំនេរ បើមិនចង់ប្តូរ">
                    @error('password') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block font-medium mb-2 text-gray-700">ថ្នាក់ <span class="text-red-500">*</span></label>
                    <input type="text" name="grade" value="{{ old('grade', $student->grade) }}" 
                           class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                           required>
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
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 mb-4">
                <input type="text" name="village" value="{{ old('village', $student->village) }}" 
                       class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                       placeholder="ភូមិ">
                <input type="text" name="commune" value="{{ old('commune', $student->commune) }}" 
                       class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                       placeholder="ឃុំ/សង្កាត់">
                <input type="text" name="district" value="{{ old('district', $student->district) }}" 
                       class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                       placeholder="ស្រុក/ខណ្ឌ">
                <input type="text" name="province" value="{{ old('province', $student->province) }}" 
                       class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                       placeholder="ខេត្ត/រាជធានី">
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium mb-2 text-gray-700">លេខទូរស័ព្ទ</label>
                    <input type="text" name="phone" value="{{ old('phone', $student->phone) }}" 
                           class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                           placeholder="012 345 678">
                </div>
                <div>
                    <label class="block font-medium mb-2 text-gray-700">មកពីអនុវិទ្យាល័យ</label>
                    <input type="text" name="high_school" value="{{ old('high_school', $student->high_school) }}" 
                           class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent" 
                           placeholder="ឈ្មោះអនុវិទ្យាល័យ">
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
                    <label class="block font-medium mb-2 text-gray-700">ឈ្មោះម្តាយ</label>
                    <input type="text" name="mother_name" value="{{ old('mother_name', $student->mother_name) }}" 
                           class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <label class="block font-medium mb-2 text-gray-700 mt-3">លេខទូរស័ព្ទម្តាយ</label>
                    <input type="text" name="mother_phone" value="{{ old('mother_phone', $student->mother_phone) }}" 
                           class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="font-semibold text-gray-700 mb-3">ព័ត៌មានឪពុក</h3>
                    <label class="block font-medium mb-2 text-gray-700">ឈ្មោះឪពុក</label>
                    <input type="text" name="father_name" value="{{ old('father_name', $student->father_name) }}" 
                           class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <label class="block font-medium mb-2 text-gray-700 mt-3">លេខទូរស័ព្ទឪពុក</label>
                    <input type="text" name="father_phone" value="{{ old('father_phone', $student->father_phone) }}" 
                           class="border border-gray-300 rounded-lg p-3 w-full focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700 font-semibold w-full">
            កែប្រែព័ត៌មានសិស្ស
        </button>
    </form>            
</div>
@endsection
