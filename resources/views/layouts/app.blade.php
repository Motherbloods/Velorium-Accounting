<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Akuntansi')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        background: '#F8FAFC',
                        primary: '#3B82F6',
                        secondary: '#60A5FA',
                        accent: '#1E40AF',
                        success: '#10B981',
                        warning: '#F59E0B',
                        error: '#EF4444',
                        text: '#1F2937'
                    }
                }
            }
        }
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="bg-background text-text antialiased">
    @auth
        <div class="flex h-screen overflow-hidden">
            <aside class="w-64 shrink-0 bg-white border-r border-slate-200 shadow-sm flex flex-col">
                <div class="px-6 py-5 border-b border-slate-200">
                    <span class="text-lg font-semibold text-accent">
                        Sistem Akuntansi
                    </span>
                </div>
                <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-primary' : 'text-text hover:bg-slate-50' }}">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        Dashboard
                    </a>
                    @if (auth()->user()->hasRole('admin', 'akuntan'))
                        <a href="{{ route('coa.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('coa.*') ? 'bg-blue-50 text-primary' : 'text-text hover:bg-slate-50' }}">
                            <i data-lucide="list-tree" class="w-4 h-4"></i>
                            Chart of Account
                        </a>
                        <a href="{{ route('fiscal-periods.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('fiscal-periods.*') ? 'bg-blue-50 text-primary' : 'text-text hover:bg-slate-50' }}">
                            <i data-lucide="calendar-range" class="w-4 h-4"></i>
                            Periode Akuntansi
                        </a>
                        <a href="{{ route('journal.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('journal.*') ? 'bg-blue-50 text-primary' : 'text-text hover:bg-slate-50' }}">
                            <i data-lucide="book-text" class="w-4 h-4"></i>
                            Jurnal Umum
                        </a>
                    @endif
                    @yield('nav-extra')
                </nav>
                <div class="px-3 py-4 border-t border-slate-200 shrink-0">
                    <div class="px-3 py-2 text-xs text-slate-500">
                        Masuk sebagai
                        <div class="text-sm font-medium text-text">{{ auth()->user()->name }}</div>
                        <span
                            class="inline-block mt-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-accent">{{ auth()->user()->role }}</span>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="mt-2">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-error hover:bg-red-50">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                            Keluar
                        </button>
                    </form>
                </div>
            </aside>
            <div class="flex-1 min-w-0 flex flex-col overflow-hidden">
                <header class="h-16 shrink-0 bg-white border-b border-slate-200 shadow-sm flex items-center px-6">
                    <h1 class="text-lg font-semibold text-text">
                        @yield('title', 'Dashboard')
                    </h1>
                </header>
                <main class="flex-1 overflow-y-auto p-6">
                    @include('components.flash-message')
                    @yield('content')
                </main>
            </div>
        </div>
    @else
        <main class="min-h-screen flex items-center justify-center px-4">
            @include('components.flash-message')
            @yield('content')
        </main>
    @endauth
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
        });
    </script>
</body>

</html>
