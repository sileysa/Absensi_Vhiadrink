<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceType;
use App\Models\Attendance;
use App\Models\Stand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $standId = $request->session()->get('selected_stand_id');

        if (! $standId) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Silakan pilih stand terlebih dahulu.');
        }

        $stand = Stand::query()
            ->where('is_active', true)
            ->findOrFail($standId);

        $today = Carbon::today();
        $user = $request->user();

        $todayAttendances = Attendance::query()
            ->where('user_id', $user->id)
            ->where('stand_id', $stand->id)
            ->whereDate('attended_at', $today)
            ->orderBy('attended_at')
            ->get();

        $checkIn = $todayAttendances->firstWhere('type', AttendanceType::CheckIn);
        $checkOut = $todayAttendances->firstWhere('type', AttendanceType::CheckOut);

        return view('attendance.index', [
            'stand' => $stand,
            'checkIn' => $checkIn,
            'checkOut' => $checkOut,
            'recentAttendances' => Attendance::query()
                ->where('user_id', $user->id)
                ->with('stand')
                ->latest('attended_at')
                ->limit(10)
                ->get(),
        ]);
    }

    public function checkIn(Request $request): RedirectResponse
    {
        return $this->recordAttendance($request, AttendanceType::CheckIn);
    }

    public function checkOut(Request $request): RedirectResponse
    {
        return $this->recordAttendance($request, AttendanceType::CheckOut);
    }

    private function recordAttendance(Request $request, AttendanceType $type): RedirectResponse
    {
        $standId = $request->session()->get('selected_stand_id');

        if (! $standId) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'Silakan pilih stand terlebih dahulu.');
        }

        $stand = Stand::query()
            ->where('is_active', true)
            ->findOrFail($standId);

        $user = $request->user();
        $today = Carbon::today();

        $existing = Attendance::query()
            ->where('user_id', $user->id)
            ->where('stand_id', $stand->id)
            ->where('type', $type)
            ->whereDate('attended_at', $today)
            ->exists();

        if ($existing) {
            return back()->with('error', "Anda sudah melakukan absensi {$type->label()} hari ini.");
        }

        if ($type === AttendanceType::CheckOut) {
            $hasCheckIn = Attendance::query()
                ->where('user_id', $user->id)
                ->where('stand_id', $stand->id)
                ->where('type', AttendanceType::CheckIn)
                ->whereDate('attended_at', $today)
                ->exists();

            if (! $hasCheckIn) {
                return back()->with('error', 'Anda harus absen masuk terlebih dahulu.');
            }
        }

        Attendance::query()->create([
            'user_id' => $user->id,
            'stand_id' => $stand->id,
            'type' => $type,
            'attended_at' => now(),
        ]);

        return back()->with('success', "Absensi {$type->label()} berhasil dicatat pada stand {$stand->name}.");
    }
}
