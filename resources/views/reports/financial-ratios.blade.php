@extends('layouts.app')

@section('title', 'Dashboard Rasio Keuangan')

@section('content')
    <x-card class="shadow-md mb-6 bg-blue-50 border-l-4 border-l-primary">
        <form method="GET" action="{{ route('reports.financial-ratios') }}" class="flex items-end gap-3">
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Periode</label>
                <select name="fiscal_period_id"
                    class="px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    @foreach ($periods as $period)
                        <option value="{{ $period->id }}" @selected(optional($selectedPeriod)->id === $period->id)>{{ $period->nama_periode }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit"
                class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
                Tampilkan
            </button>
        </form>
    </x-card>

    @if ($report)
        <p class="text-sm text-slate-500 mb-4">Periode: {{ $selectedPeriod->nama_periode }}</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-card class="shadow-md bg-blue-50 border-l-4 border-l-primary">
                <p class="text-sm font-semibold text-accent mb-4">Rasio Likuiditas</p>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-600">Current Ratio</span>
                        <span
                            class="text-lg font-semibold text-text">{{ $report['current_ratio'] !== null ? number_format($report['current_ratio'], 2) : 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-600">Quick Ratio</span>
                        <span
                            class="text-lg font-semibold text-text">{{ $report['quick_ratio'] !== null ? number_format($report['quick_ratio'], 2) : 'N/A' }}</span>
                    </div>
                </div>
            </x-card>

            <x-card class="shadow-md bg-emerald-50 border-l-4 border-l-success">
                <p class="text-sm font-semibold text-accent mb-4">Rasio Profitabilitas</p>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-600">Gross Profit Margin</span>
                        <span
                            class="text-lg font-semibold text-text">{{ $report['gross_profit_margin'] !== null ? number_format($report['gross_profit_margin'], 2) . '%' : 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-600">Net Profit Margin</span>
                        <span
                            class="text-lg font-semibold text-text">{{ $report['net_profit_margin'] !== null ? number_format($report['net_profit_margin'], 2) . '%' : 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-600">Return on Assets (ROA)</span>
                        <span
                            class="text-lg font-semibold text-text">{{ $report['roa'] !== null ? number_format($report['roa'], 2) . '%' : 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-600">Return on Equity (ROE)</span>
                        <span
                            class="text-lg font-semibold text-text">{{ $report['roe'] !== null ? number_format($report['roe'], 2) . '%' : 'N/A' }}</span>
                    </div>
                </div>
            </x-card>

            <x-card class="shadow-md bg-amber-50 border-l-4 border-l-warning">
                <p class="text-sm font-semibold text-accent mb-4">Rasio Solvabilitas</p>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-600">Debt to Equity Ratio</span>
                        <span
                            class="text-lg font-semibold text-text">{{ $report['debt_to_equity'] !== null ? number_format($report['debt_to_equity'], 2) : 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-600">Debt to Asset Ratio</span>
                        <span
                            class="text-lg font-semibold text-text">{{ $report['debt_to_asset'] !== null ? number_format($report['debt_to_asset'], 2) : 'N/A' }}</span>
                    </div>
                </div>
            </x-card>

            <x-card class="shadow-md bg-blue-50 border-l-4 border-l-secondary">
                <p class="text-sm font-semibold text-accent mb-4">Rasio Aktivitas</p>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-600">Inventory Turnover</span>
                        <span
                            class="text-lg font-semibold text-text">{{ $report['inventory_turnover'] !== null ? number_format($report['inventory_turnover'], 2) . 'x' : 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-slate-600">Receivable Turnover</span>
                        <span
                            class="text-lg font-semibold text-text">{{ $report['receivable_turnover'] !== null ? number_format($report['receivable_turnover'], 2) . 'x' : 'N/A' }}</span>
                    </div>
                </div>
            </x-card>
        </div>
    @else
        <x-card class="shadow-sm">
            <p class="text-sm text-slate-500">Belum ada periode akuntansi. Silakan tambahkan periode terlebih dahulu.</p>
        </x-card>
    @endif
@endsection
