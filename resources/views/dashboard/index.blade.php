@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-pink-900">Pilih Stand Absensi</h1>
        <p class="mt-2 text-slate-600">
            Pilih stand Vhiadrink tempat Anda bekerja hari ini. Setelah memilih stand, login untuk melakukan absensi.
        </p>
    </div>

    @if ($selectedStand)
        <div class="mb-8 rounded-2xl border border-pink-200 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-medium uppercase tracking-wide text-pink-600">Stand Terpilih</p>
                    <h2 class="mt-1 text-2xl font-semibold text-slate-900">{{ $selectedStand->name }}</h2>
                    <p class="mt-1 text-slate-600">{{ $selectedStand->region }} @if($selectedStand->address) &bull; {{ $selectedStand->address }} @endif</p>
                </div>

                <div class="flex flex-wrap gap-3">
                    @auth
                        <a href="{{ route('attendance.index') }}" class="rounded-xl bg-pink-600 px-5 py-3 text-sm font-semibold text-white hover:bg-pink-700">
                            Lanjut Absensi
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-xl bg-pink-600 px-5 py-3 text-sm font-semibold text-white hover:bg-pink-700">
                            Login untuk Absensi
                        </a>
                    @endauth

                    <form method="POST" action="{{ route('stand.clear') }}">
                        @csrf
                        <button type="submit" class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            Ganti Stand
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if ($stands->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center">
            <p class="text-lg font-medium text-slate-700">Belum ada stand aktif.</p>
            <p class="mt-2 text-slate-500">Hubungi admin untuk menambahkan stand Vhiadrink.</p>
        </div>
    @else
        <div class="space-y-8">
            @foreach ($stands as $region => $regionStands)
                <section>
                    <h2 class="mb-4 text-xl font-semibold text-slate-900">{{ $region }}</h2>
                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($regionStands as $stand)
                            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:border-pink-300 hover:shadow-md">
                                <h3 class="text-lg font-semibold text-slate-900">{{ $stand->name }}</h3>
                                @if ($stand->address)
                                    <p class="mt-2 text-sm text-slate-600">{{ $stand->address }}</p>
                                @endif

                                <form method="POST" action="{{ route('stand.select') }}" class="mt-5">
                                    @csrf
                                    <input type="hidden" name="stand_id" value="{{ $stand->id }}">
                                    <button
                                        type="submit"
                                        class="w-full rounded-xl px-4 py-2.5 text-sm font-semibold {{ $selectedStand?->id === $stand->id ? 'bg-pink-100 text-pink-800' : 'bg-pink-600 text-white hover:bg-pink-700' }}"
                                    >
                                        {{ $selectedStand?->id === $stand->id ? 'Stand Dipilih' : 'Pilih Stand Ini' }}
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    @endif
@endsection
