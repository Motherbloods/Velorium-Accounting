@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
    <div class="w-full max-w-sm bg-white rounded-xl shadow-md border border-slate-200 p-8">
        <h2 class="text-xl font-semibold text-accent mb-1">Masuk</h2>
        <p class="text-sm text-slate-500 mb-6">Masuk ke Sistem Akuntansi</p>

        @if ($errors->any())
            <div class="mb-4 px-4 py-3 rounded-lg bg-red-100 text-error text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-text mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-text mb-1">Password</label>
                <input type="password" name="password" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <label class="flex items-center gap-2 text-sm text-slate-500">
                <input type="checkbox" name="remember" class="rounded border-slate-300">
                Ingat saya
            </label>
            <button type="submit"
                class="w-full py-2 rounded-lg bg-primary text-white font-medium shadow-sm hover:bg-accent">
                Masuk
            </button>
        </form>

        <p class="mt-6 text-sm text-slate-500 text-center">
            Belum punya akun? <a href="{{ route('register') }}" class="text-primary font-medium">Daftar</a>
        </p>
    </div>
@endsection
