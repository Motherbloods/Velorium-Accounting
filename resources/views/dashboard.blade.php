@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <x-card class="bg-blue-50 border-l-4 border-l-primary">
            <p class="text-sm text-slate-500">Selamat Datang</p>
            <p class="text-lg font-semibold text-text mt-1">{{ auth()->user()->name }}</p>
        </x-card>
        <x-card class="bg-emerald-50 border-l-4 border-l-success">
            <p class="text-sm text-slate-500">Role Anda</p>
            <p class="text-lg font-semibold text-text mt-1">{{ ucfirst(auth()->user()->role) }}</p>
        </x-card>
        <x-card class="bg-amber-50 border-l-4 border-l-warning">
            <p class="text-sm text-slate-500">Status Modul</p>
            <p class="text-lg font-semibold text-text mt-1">Tahap 1: Setup & Autentikasi</p>
        </x-card>
    </div>
@endsection
