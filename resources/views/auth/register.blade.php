@extends('layouts.app')

@section('title', 'Daftar')

@section('content')
    <div class="w-full max-w-sm bg-white rounded-xl shadow-md border border-slate-200 p-8">
        <h2 class="text-xl font-semibold text-accent mb-1">Daftar Akun</h2>
        <p class="text-sm text-slate-500 mb-6">Buat akun baru untuk Sistem Akuntansi</p>

        @if ($errors->any())
            <div class="mb-4 px-4 py-3 rounded-lg bg-red-100 text-error text-sm">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-text mb-1">Nama</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-text mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-text mb-1">Role</label>
                <select name="role" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
                    <option value="admin">Admin</option>
                    <option value="akuntan">Akuntan</option>
                    <option value="kasir" selected>Kasir</option>
                    <option value="staff_konsinyasi">Staff Konsinyasi</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-text mb-1">Password</label>
                <input type="password" name="password" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-text mb-1">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" required
                    class="w-full px-3 py-2 rounded-lg border border-slate-200 focus:outline-none focus:ring-2 focus:ring-primary">
            </div>
            <button type="submit"
                class="w-full py-2 rounded-lg bg-primary text-white font-medium shadow-sm hover:bg-accent">
                Daftar
            </button>
        </form>

        <p class="mt-6 text-sm text-slate-500 text-center">
            Sudah punya akun? <a href="{{ route('login') }}" class="text-primary font-medium">Masuk</a>
        </p>
    </div>
@endsection
