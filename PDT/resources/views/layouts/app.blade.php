<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">

  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Kantumruy+Pro:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">

  <title>@yield('title', 'PDT')</title>
  @include('partials.theme-head')
</head>
@php
    $pageTitle = trim($__env->yieldContent('page_title'));
    $pageSubtitle = trim($__env->yieldContent('page_subtitle'));
    $hasPageHeader = $pageTitle !== '' || $pageSubtitle !== '' || $__env->hasSection('page_actions');
@endphp
<body>
  <div class="app-shell">
    <aside class="app-sidebar sidebar overflow-y-auto">
      <div class="workspace-dropdown">
        <div class="workspace-dropdown__summary">
          <div class="workspace-dropdown__head">
            <div class="sidebar-user__avatar">
              <i class="fa-solid fa-layer-group"></i>
            </div>
            <div class="workspace-dropdown__body">
              <h2 class="workspace-dropdown__title">ទំព័រដើម</h2>
              <p class="workspace-dropdown__meta">School Management</p>
            </div>
          </div>
        </div>

        <div class="workspace-dropdown__menu">
          <div class="workspace-dropdown__account">
            <div class="sidebar-user__avatar">
              <i class="fa-solid fa-user"></i>
            </div>
            <div class="workspace-dropdown__account-meta">
              <p class="workspace-dropdown__account-name">{{ Auth::user()->name }}</p>
              <p class="workspace-dropdown__account-email">{{ Auth::user()->email }}</p>
            </div>
          </div>

          <a href="{{ route('dashboard') }}" class="workspace-dropdown__link">
            <i class="fa-solid fa-house"></i>
            <span>ទំព័រដើម</span>
          </a>

          <a href="{{ route('auth.profile') }}" class="workspace-dropdown__link">
            <i class="fa-solid fa-id-badge"></i>
            <span>មើលប្រវត្តិរូប</span>
          </a>

          <button type="button" class="theme-toggle" data-theme-toggle aria-label="Switch theme">
            <span class="theme-toggle__icon">
              <i data-theme-icon class="fa-solid fa-moon"></i>
            </span>
            <span data-theme-label>Dark mode</span>
          </button>
        </div>
      </div>

      <div class="flex-1">
        <p class="sidebar-label">Navigation</p>
        <nav class="flex flex-col gap-2">
          @foreach ($sidebarMenuItems as $item)
            @php
                $patterns = $item['active_patterns'] ?? [$item['route']];
                $isActive = collect($patterns)->contains(fn ($pattern) => request()->routeIs($pattern));
            @endphp
            <a href="{{ route($item['route']) }}" class="menu-link {{ $isActive ? 'is-active' : '' }}">
              <i class="{{ $item['icon'] }}"></i>
              <span>{{ $item['label'] }}</span>
            </a>
          @endforeach
        </nav>
      </div>

      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="logout-button">
          <i class="fa-solid fa-sign-out-alt"></i>
          <span>ចាកចេញ</span>
        </button>
      </form>
    </aside>

    <div class="app-main">
      @if ($hasPageHeader)
        <header class="app-main__header">
          <div>
            @if ($pageTitle !== '')
              <h1 class="app-main__title">{{ $pageTitle }}</h1>
            @endif
            @if ($pageSubtitle !== '')
              <p class="app-main__subtitle">{{ $pageSubtitle }}</p>
            @endif
          </div>

          @hasSection('page_actions')
            <div class="app-main__actions">
              @yield('page_actions')
            </div>
          @endif
        </header>
      @endif

      <main class="app-content">
        @yield('content')
      </main>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>
