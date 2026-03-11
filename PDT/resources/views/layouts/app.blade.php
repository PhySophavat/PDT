<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

  <!-- Custom Font -->
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

  <title>@yield('title', 'Dashboard')</title>

  <style>
    body {
      font-family: 'Roboto', sans-serif;
    }
    /* Sidebar scrollbar */
    .sidebar::-webkit-scrollbar {
      width: 6px;
    }
    .sidebar::-webkit-scrollbar-thumb {
      background-color: rgba(107, 114, 128, 0.5);
      border-radius: 10px;
    }
  </style>
</head>
<body class="bg-gray-100 min-h-screen">

  <div class="flex min-h-screen">

    <!-- Sidebar -->
    <aside class="w-64 bg-white shadow-md p-6 flex flex-col sidebar overflow-y-auto">
      <div class="text-center mb-8">
        <a href="{{ route('auth.profile') }}" class="inline-block">
          <i class="fa-solid fa-user text-5xl text-blue-500 mb-2"></i>
          <h2 class="text-lg font-bold text-gray-800">{{ Auth::user()->name }}</h2>
          
        </a>
      </div>
       @if (Auth::user()->hasRole('admin'))
      <nav class="flex flex-col space-y-2 flex-1">
        <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded hover:bg-blue-100 font-semibold text-gray-800 text-lg flex items-center transition">
          <i class="fa-solid fa-house mr-3"></i> ទំព័រដើម
        </a>
        @endif

        @if (Auth::user()->hasRole('admin'))
        <a href="{{ route('role.index') }}" class="px-4 py-2 rounded hover:bg-blue-100 font-semibold text-gray-800 text-lg flex items-center transition">
          <i class="fa-solid fa-user-shield mr-3"></i> តួនាទី
        </a>
        @endif

        @if (Auth::user()->hasPermission('manage-teacher') || Auth::user()->hasRole('admin'))
        <a href="{{ route('teacher.index') }}" class="px-4 py-2 rounded hover:bg-blue-100 font-semibold text-gray-800 text-lg flex items-center transition">
          <i class="fa-solid fa-chalkboard-teacher mr-3"></i> គ្រូ
        </a>
        @endif

        @if ( Auth::user()->hasRole('admin'))
        <a href="{{ route('students.index') }}" class="px-4 py-2 rounded hover:bg-blue-100 font-semibold text-gray-800 text-lg flex items-center transition">
          <i class="fa-solid fa-graduation-cap mr-3"></i> សិស្ស
        </a>
        @endif

        @if (Auth::user()->hasPermission('view-classes') || Auth::user()->hasRole('admin') || Auth::user()->hasRole('teacher'))
        <a href="{{ route('class.index') }}" class="px-4 py-2 rounded hover:bg-blue-100 font-semibold text-gray-800 text-lg flex items-center transition">
          <i class="fa-solid fa-school mr-3"></i> ថ្នាក់
        </a>
        @endif

        @if ( Auth::user()->hasRole('admin') || Auth::user()->hasRole('teacher')|| Auth::User()->hasRole('student'))
        <a href="{{ route('result.index') }}" class="px-4 py-2 rounded hover:bg-blue-100 font-semibold text-gray-800 text-lg flex items-center transition">
          <i class="fa-solid fa-file mr-3"></i> លទ្ធផល
        </a>
        @endif
      </nav>

      <form class="mt-auto" action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="px-4 py-2 mt-4 rounded hover:bg-red-100 font-semibold text-red-700 flex items-center w-full text-left transition">
          <i class="fa-solid fa-sign-out-alt mr-3"></i> ចាកចេញ
        </button>
      </form>
    </aside>

    <!-- Main content -->
    <main class="flex-1 p-6 overflow-auto">
      @yield('content')
    </main>

  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>