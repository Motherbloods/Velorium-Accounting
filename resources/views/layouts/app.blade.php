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
                <nav class="flex-1 px-3 py-4 space-y-1">
                    <a href="{{ route('dashboard') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-primary' : 'text-text hover:bg-slate-50' }}">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        Dashboard
                    </a>

                    @if (auth()->user()->hasRole('admin', 'kasir'))
                        <a href="{{ route('cash.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('cash.*') ? 'bg-blue-50 text-primary' : 'text-text hover:bg-slate-50' }}">
                            <i data-lucide="wallet" class="w-4 h-4"></i>
                            Kas & Bank
                        </a>
                    @endif
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

                        <a href="{{ route('receivables.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('receivables.*') ? 'bg-blue-50 text-primary' : 'text-text hover:bg-slate-50' }}">
                            <i data-lucide="hand-coins" class="w-4 h-4"></i>
                            Piutang
                        </a>
                        <a href="{{ route('payables.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('payables.*') ? 'bg-blue-50 text-primary' : 'text-text hover:bg-slate-50' }}">
                            <i data-lucide="landmark" class="w-4 h-4"></i>
                            Hutang
                        </a>

                        <a href="{{ route('sales.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('sales.*') ? 'bg-blue-50 text-primary' : 'text-text hover:bg-slate-50' }}">
                            <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                            Penjualan
                        </a>
                        <a href="{{ route('purchases.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('purchases.*') ? 'bg-blue-50 text-primary' : 'text-text hover:bg-slate-50' }}">
                            <i data-lucide="shopping-bag" class="w-4 h-4"></i>
                            Pembelian
                        </a>

                        <a href="{{ route('tax.ppn') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('tax.ppn') ? 'bg-blue-50 text-primary' : 'text-text hover:bg-slate-50' }}">
                            <i data-lucide="receipt" class="w-4 h-4"></i>
                            PPN
                        </a>
                        <a href="{{ route('tax.pph') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('tax.pph') ? 'bg-blue-50 text-primary' : 'text-text hover:bg-slate-50' }}">
                            <i data-lucide="file-text" class="w-4 h-4"></i>
                            PPh Final
                        </a>

                        <a href="{{ route('bank-accounts.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('bank-accounts.*') ? 'bg-blue-50 text-primary' : 'text-text hover:bg-slate-50' }}">
                            <i data-lucide="credit-card" class="w-4 h-4"></i>
                            Rekening Bank
                        </a>
                        <a href="{{ route('bank-reconciliations.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('bank-reconciliations.*') ? 'bg-blue-50 text-primary' : 'text-text hover:bg-slate-50' }}">
                            <i data-lucide="git-compare" class="w-4 h-4"></i>
                            Rekonsiliasi Bank
                        </a>

                        <a href="{{ route('tax.settings') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('tax.settings') ? 'bg-blue-50 text-primary' : 'text-text hover:bg-slate-50' }}">
                            <i data-lucide="settings-2" class="w-4 h-4"></i>
                            Pengaturan Pajak
                        </a>

                        <p class="px-3 pt-4 pb-1 text-xs font-semibold text-slate-400 uppercase">Master Data</p>
                        <a href="{{ route('customers.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('customers.*') ? 'bg-blue-50 text-primary' : 'text-text hover:bg-slate-50' }}">
                            <i data-lucide="users" class="w-4 h-4"></i>
                            Customer
                        </a>
                        <a href="{{ route('suppliers.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('suppliers.*') ? 'bg-blue-50 text-primary' : 'text-text hover:bg-slate-50' }}">
                            <i data-lucide="truck" class="w-4 h-4"></i>
                            Supplier
                        </a>
                        <a href="{{ route('products.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('products.*') ? 'bg-blue-50 text-primary' : 'text-text hover:bg-slate-50' }}">
                            <i data-lucide="package" class="w-4 h-4"></i>
                            Produk
                        </a>
                        <a href="{{ route('stock.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('stock.*') ? 'bg-blue-50 text-primary' : 'text-text hover:bg-slate-50' }}">
                            <i data-lucide="boxes" class="w-4 h-4"></i>
                            Persediaan
                        </a>
                        <a href="{{ route('fixed-assets.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('fixed-assets.*') ? 'bg-blue-50 text-primary' : 'text-text hover:bg-slate-50' }}">
                            <i data-lucide="building" class="w-4 h-4"></i>
                            Aset Tetap
                        </a>
                        <a href="{{ route('adjusting-entries.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('adjusting-entries.*') ? 'bg-blue-50 text-primary' : 'text-text hover:bg-slate-50' }}">
                            <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
                            Jurnal Penyesuaian
                        </a>
                        <a href="{{ route('branches.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('branches.*') ? 'bg-blue-50 text-primary' : 'text-text hover:bg-slate-50' }}">
                            <i data-lucide="building-2" class="w-4 h-4"></i>
                            Cabang / Gudang
                        </a>

                        <p class="px-3 pt-4 pb-1 text-xs font-semibold text-slate-400 uppercase">Laporan</p>
                        <a href="{{ route('reports.general-ledger') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('reports.general-ledger') ? 'bg-blue-50 text-primary' : 'text-text hover:bg-slate-50' }}">
                            <i data-lucide="book-open" class="w-4 h-4"></i>
                            Buku Besar
                        </a>
                        <a href="{{ route('reports.trial-balance') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('reports.trial-balance') ? 'bg-blue-50 text-primary' : 'text-text hover:bg-slate-50' }}">
                            <i data-lucide="scale" class="w-4 h-4"></i>
                            Neraca Saldo
                        </a>

                        <a href="{{ route('reports.income-statement') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('reports.income-statement') ? 'bg-blue-50 text-primary' : 'text-text hover:bg-slate-50' }}">
                            <i data-lucide="trending-up" class="w-4 h-4"></i>
                            Laba Rugi
                        </a>
                        <a href="{{ route('reports.balance-sheet') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('reports.balance-sheet') ? 'bg-blue-50 text-primary' : 'text-text hover:bg-slate-50' }}">
                            <i data-lucide="landmark" class="w-4 h-4"></i>
                            Neraca
                        </a>

                        <a href="{{ route('reports.equity-change') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('reports.equity-change') ? 'bg-blue-50 text-primary' : 'text-text hover:bg-slate-50' }}">
                            <i data-lucide="pie-chart" class="w-4 h-4"></i>
                            Perubahan Modal
                        </a>
                        <a href="{{ route('reports.cash-flow') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('reports.cash-flow') ? 'bg-blue-50 text-primary' : 'text-text hover:bg-slate-50' }}">
                            <i data-lucide="waves" class="w-4 h-4"></i>
                            Arus Kas
                        </a>
                        <a href="{{ route('reports.financial-ratios') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('reports.financial-ratios') ? 'bg-blue-50 text-primary' : 'text-text hover:bg-slate-50' }}">
                            <i data-lucide="gauge" class="w-4 h-4"></i>
                            Rasio Keuangan
                        </a>

                        <a href="{{ route('financial-notes.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('financial-notes.*') ? 'bg-blue-50 text-primary' : 'text-text hover:bg-slate-50' }}">
                            <i data-lucide="notebook-text" class="w-4 h-4"></i>
                            CALK
                        </a>
                        <a href="{{ route('financial-report-export.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('financial-report-export.*') ? 'bg-blue-50 text-primary' : 'text-text hover:bg-slate-50' }}">
                            <i data-lucide="download" class="w-4 h-4"></i>
                            Ekspor Laporan Lengkap
                        </a>
                    @endif
                    @if (auth()->user()->hasRole('admin', 'staff_konsinyasi'))
                        <p class="px-3 pt-4 pb-1 text-xs font-semibold text-slate-400 uppercase">Konsinyasi</p>
                        <a href="{{ route('consignment.shipments.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('consignment.shipments.*') ? 'bg-blue-50 text-primary' : 'text-text hover:bg-slate-50' }}">
                            <i data-lucide="package-check" class="w-4 h-4"></i>
                            Pengiriman Konsinyasi
                        </a>
                        <a href="{{ route('consignees.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('consignees.*') ? 'bg-blue-50 text-primary' : 'text-text hover:bg-slate-50' }}">
                            <i data-lucide="store" class="w-4 h-4"></i>
                            Consignee
                        </a>
                    @endif
                    @if (auth()->user()->hasRole('admin'))
                        <p class="px-3 pt-4 pb-1 text-xs font-semibold text-slate-400 uppercase">Sistem</p>
                        <a href="{{ route('audit-logs.index') }}"
                            class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('audit-logs.*') ? 'bg-blue-50 text-primary' : 'text-text hover:bg-slate-50' }}">
                            <i data-lucide="shield-check" class="w-4 h-4"></i>
                            Audit Log
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
