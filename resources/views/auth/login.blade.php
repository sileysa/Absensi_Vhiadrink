@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="mx-auto max-w-md">
        <div class="rounded-2xl border border-slate-200 bg-white p-8 shadow-sm">
            <h1 class="text-2xl font-bold text-pink-900">Login Karyawan</h1>
            <p class="mt-2 text-sm text-slate-600">
                Masuk dengan akun karyawan untuk melakukan absensi di stand yang sudah dipilih.
            </p>

            <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-5">
                @csrf

                <div>
                    <label for="email" class="mb-2 block text-sm font-medium text-slate-700">Email</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-200"
                    >
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="mb-2 block text-sm font-medium text-slate-700">Password</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-200"
                    >
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" class="rounded border-slate-300 text-pink-600 focus:ring-pink-500">
                    Ingat saya
                </label>

                <button type="submit" class="w-full rounded-xl bg-pink-600 px-4 py-3 text-sm font-semibold text-white hover:bg-pink-700">
                    Masuk
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-slate-500">
                Belum pilih stand?
                <a href="{{ route('dashboard') }}" class="font-medium text-pink-700 hover:underline">Kembali ke dashboard</a>
            </p>
        </div>
    </div>
@endsection
