<?php

namespace App\Http\Controllers;

use App\Enums\AttendanceType;
use App\Models\Attendance;
use App\Models\Stand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use App\Models\Shift;

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

        $todayAttendances = Attendance::with('shift')
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

    public function checkIn(Request $request): RedirectResponse|JsonResponse
    {
        return $this->recordAttendance($request, AttendanceType::CheckIn);
    }

    public function checkOut(Request $request): RedirectResponse|JsonResponse
    {
        return $this->recordAttendance($request, AttendanceType::CheckOut);
    }

    private function recordAttendance(Request $request, AttendanceType $type): RedirectResponse|JsonResponse
    {
        // Validasi input
        $validated = $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // max 5MB
        ], [
            'photo.required' => 'Foto selfie wajib diunggah.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.mimes' => 'Foto harus dalam format jpeg, png, jpg, atau gif.',
            'photo.max' => 'Ukuran foto tidak boleh lebih dari 5MB.',
        ]);

        $standId = $request->session()->get('selected_stand_id');

        if (! $standId) {
            $message = 'Silakan pilih stand terlebih dahulu.';
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 400);
            }
            return redirect()->route('dashboard')->with('error', $message);
        }

        $stand = Stand::query()
            ->where('is_active', true)
            ->findOrFail($standId);

        $user = $request->user();
        $today = Carbon::today();

        $shift = null;

        if ($type === AttendanceType::CheckIn) {

            $currentTime = now()->format('H:i:s');

            $shift = Shift::where('is_active', true)
                ->where('checkin_start', '<=', $currentTime)
                ->where('checkin_end', '>=', $currentTime)
                ->first();

            if (!$shift) {

                $message = 'Sekarang bukan jam absensi.';

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message,
                    ], 400);
                }

                return back()->with('error', $message);
            }
        }

        $existing = Attendance::query()
            ->where('user_id', $user->id)
            ->where('stand_id', $stand->id)
            ->where('type', $type)
            ->whereDate('attended_at', $today)
            ->exists();

        if ($existing) {
            $message = "Anda sudah melakukan absensi {$type->label()} hari ini.";
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 400);
            }
            return back()->with('error', $message);
        }

        if ($type === AttendanceType::CheckOut) {
            $hasCheckIn = Attendance::query()
                ->where('user_id', $user->id)
                ->where('stand_id', $stand->id)
                ->where('type', AttendanceType::CheckIn)
                ->whereDate('attended_at', $today)
                ->exists();

            if (! $hasCheckIn) {
                $message = 'Anda harus absen masuk terlebih dahulu.';
                
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 400);
                }
                return back()->with('error', $message);
            }

            // Validasi waktu checkout
            $checkInAttendance = Attendance::where('user_id', $user->id)
                ->where('stand_id', $stand->id)
                ->whereDate('attended_at', today())
                ->where('type', AttendanceType::CheckIn)
                ->first();

            $shift = $checkInAttendance->shift;
                
            if ($shift) {

                $checkoutTime = Carbon::createFromFormat(
                    'H:i',
                    $shift->checkout_time
                )->setDate(now()->year, now()->month, now()->day);

                if (now()->lt($checkoutTime)) {

                    return back()->with(
                        'error',
                        "Anda tidak bisa pulang sebelum pukul {$shift->checkout_time}"
                    );
                }
            }
        }


        // Upload foto
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = 'attendance_' . $user->id . '_' . $type->value . '_' . now()->timestamp . '.' . $file->getClientOriginalExtension();
            $photoPath = $file->storeAs('attendance-photos', $filename, 'public');
        }

        Attendance::query()->create([
            'user_id' => $user->id,
            'stand_id' => $stand->id,
            'shift_id' => $shift->id,
            'type' => $type,
            'attended_at' => now(),
            'photo' => $photoPath,
        ]);

        $successMessage = "Absensi {$type->label()} berhasil dicatat pada stand {$stand->name}. Foto telah tersimpan.";

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $successMessage]);
        }
        return back()->with('success', $successMessage);
    
    }
}