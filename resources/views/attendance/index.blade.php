@extends('layouts.app')

@section('title', 'Absensi')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-emerald-900">Absensi Hari Ini</h1>
        <p class="mt-2 text-slate-600">Catat waktu masuk dan keluar Anda di stand berikut.</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-2xl border border-emerald-200 bg-white p-6 shadow-sm lg:col-span-2">
            <p class="text-sm font-medium uppercase tracking-wide text-emerald-600">Stand Aktif</p>
            <h2 class="mt-1 text-2xl font-semibold text-slate-900">{{ $stand->name }}</h2>
            <p class="mt-1 text-slate-600">{{ $stand->region }} @if($stand->address) &bull; {{ $stand->address }} @endif</p>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-xl border border-slate-200 p-4">
                    <p class="text-sm text-slate-500">Absen Masuk</p>
                    <p class="mt-2 text-lg font-semibold text-slate-900">
                        {{ $checkIn?->attended_at?->format('H:i') ?? 'Belum absen' }}
                    </p>
                </div>
                <div class="rounded-xl border border-slate-200 p-4">
                    <p class="text-sm text-slate-500">Absen Keluar</p>
                    <p class="mt-2 text-lg font-semibold text-slate-900">
                        {{ $checkOut?->attended_at?->format('H:i') ?? 'Belum absen' }}
                    </p>
                </div>
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                <form method="POST" action="{{ route('attendance.check-in') }}">
                    @csrf
                    <button
                        type="submit"
                        @disabled($checkIn)
                        class="rounded-xl px-5 py-3 text-sm font-semibold {{ $checkIn ? 'cursor-not-allowed bg-slate-100 text-slate-400' : 'bg-emerald-600 text-white hover:bg-emerald-700' }}"
                    >
                        Absen Masuk
                    </button>
                </form>

                <form method="POST" action="{{ route('attendance.check-out') }}">
                    @csrf
                    <button
                        type="submit"
                        @disabled(! $checkIn || $checkOut)
                        class="rounded-xl px-5 py-3 text-sm font-semibold {{ (! $checkIn || $checkOut) ? 'cursor-not-allowed bg-slate-100 text-slate-400' : 'bg-amber-500 text-white hover:bg-amber-600' }}"
                    >
                        Absen Keluar
                    </button>
                </form>

                <a href="{{ route('dashboard') }}" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Ganti Stand
                </a>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-slate-900">Riwayat Terakhir</h3>
            <div class="mt-4 space-y-3">
                @forelse ($recentAttendances as $attendance)
                    <div class="rounded-xl border border-slate-100 p-3">
                        <p class="font-medium text-slate-800">{{ $attendance->type->label() }}</p>
                        <p class="text-sm text-slate-600">{{ $attendance->stand->name }}</p>
                        <p class="mt-1 text-xs text-slate-500">{{ $attendance->attended_at->format('d M Y, H:i') }}</p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Belum ada riwayat absensi.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
