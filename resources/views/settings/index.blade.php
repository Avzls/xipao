@extends('layouts.app')

@section('page-title', 'Pengaturan')
@section('page-subtitle', 'Kelola profil dan keamanan akun')

@section('content')
<div class="max-w-2xl space-y-6">

    @if(session('success'))
    <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-700 flex items-center gap-2">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span class="text-sm font-medium">{{ session('success') }}</span>
    </div>
    @endif

    <!-- Profile -->
    <div class="card">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900">Profil</h3>
                <p class="text-sm text-gray-500">Ubah nama dan email akun</p>
            </div>
        </div>

        <form action="{{ route('settings.profile') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="name" class="form-label">Nama</label>
                <input type="text" name="name" id="name" class="form-input" value="{{ old('name', auth()->user()->name) }}" required>
                @error('name')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-input" value="{{ old('email', auth()->user()->email) }}" required>
                @error('email')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="pt-2">
                <button type="submit" class="btn btn-primary">Simpan Profil</button>
            </div>
        </form>
    </div>

    <!-- Password -->
    <div class="card">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900">Ganti Password</h3>
                <p class="text-sm text-gray-500">Pastikan menggunakan password yang kuat</p>
            </div>
        </div>

        <form action="{{ route('settings.password') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="current_password" class="form-label">Password Lama</label>
                <input type="password" name="current_password" id="current_password" class="form-input" required>
                @error('current_password')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password" class="form-label">Password Baru</label>
                <input type="password" name="password" id="password" class="form-input" required>
                @error('password')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password_confirmation" class="form-label">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-input" required>
            </div>
            <div class="pt-2">
                <button type="submit" class="btn btn-primary">Ganti Password</button>
            </div>
        </form>
    </div>
</div>
@endsection
