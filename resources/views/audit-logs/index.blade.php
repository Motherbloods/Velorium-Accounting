@extends('layouts.app')

@section('title', 'Audit Log')

@section('content')
    <x-card class="shadow-md mb-6 bg-blue-50 border-l-4 border-l-primary">
        <form method="GET" action="{{ route('audit-logs.index') }}" class="flex items-end gap-3">
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Model</label>
                <select name="model_type"
                    class="px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="">— Semua Model —</option>
                    @foreach ($modelTypes as $type)
                        <option value="{{ $type }}" @selected(request('model_type') === $type)>{{ class_basename($type) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Aksi</label>
                <select name="action"
                    class="px-3 py-2 rounded-lg border border-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="">— Semua Aksi —</option>
                    <option value="created" @selected(request('action') === 'created')>Created</option>
                    <option value="updated" @selected(request('action') === 'updated')>Updated</option>
                    <option value="deleted" @selected(request('action') === 'deleted')>Deleted</option>
                </select>
            </div>
            <button type="submit"
                class="px-4 py-2 rounded-lg bg-primary text-white text-sm font-medium shadow-sm hover:bg-accent">
                Filter
            </button>
        </form>
    </x-card>

    <x-card class="shadow-md p-0 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left text-slate-500">
                <tr>
                    <th class="px-4 py-3">Waktu</th>
                    <th class="px-4 py-3">User</th>
                    <th class="px-4 py-3">Model</th>
                    <th class="px-4 py-3">Aksi</th>
                    <th class="px-4 py-3">Perubahan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach ($logs as $log)
                    <tr class="hover:bg-slate-50 align-top">
                        <td class="px-4 py-3 whitespace-nowrap">{{ $log->created_at->format('d M Y H:i') }}</td>
                        <td class="px-4 py-3">{{ $log->user->name ?? 'Sistem' }}</td>
                        <td class="px-4 py-3">{{ class_basename($log->model_type) }} #{{ $log->model_id }}</td>
                        <td class="px-4 py-3">
                            <span
                                class="px-2 py-0.5 rounded-full text-xs font-medium {{ $log->action === 'created' ? 'bg-emerald-100 text-emerald-700' : ($log->action === 'deleted' ? 'bg-red-100 text-error' : 'bg-amber-100 text-amber-700') }}">
                                {{ ucfirst($log->action) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            @if ($log->action === 'updated')
                                <div class="space-y-1">
                                    @foreach ($log->data_baru as $field => $newValue)
                                        <div class="text-xs">
                                            <span class="font-medium text-slate-600">{{ $field }}:</span>
                                            <span
                                                class="text-red-500 line-through">{{ is_scalar($log->data_lama[$field] ?? null) ? $log->data_lama[$field] : json_encode($log->data_lama[$field] ?? null) }}</span>
                                            →
                                            <span
                                                class="text-emerald-600">{{ is_scalar($newValue) ? $newValue : json_encode($newValue) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif ($log->action === 'created')
                                <span class="text-xs text-slate-400">Data baru dibuat</span>
                            @else
                                <span class="text-xs text-slate-400">Data dihapus</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </x-card>

    <div class="mt-4">
        {{ $logs->links() }}
    </div>
@endsection
