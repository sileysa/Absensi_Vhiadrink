@extends('layouts.app')

@section('title', 'Absensi')

@section('content')
    <style>
        #cameraPreview {
            width: 100%;
            max-width: 400px;
            border-radius: 0.75rem;
            background: #000;
        }

        #capturedPhoto {
            max-width: 400px;
            border-radius: 0.75rem;
        }

        .camera-modal-hidden {
            display: none !important;
        }
    </style>

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-pink-900">Absensi Hari Ini</h1>
        <p class="mt-2 text-slate-600">Catat waktu masuk dan keluar Anda di stand berikut dengan foto selfie.</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-3">
        <div class="rounded-2xl border border-pink-200 bg-white p-6 shadow-sm lg:col-span-2">
            <p class="text-sm font-medium uppercase tracking-wide text-pink-600">Stand Aktif</p>
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

            @if($checkIn?->shift && !$checkOut)
                <div class="mt-4 rounded-xl border border-pink-200 bg-pink-50 px-4 py-3">

                    <p class="text-sm text-pink-800">
                        <strong>Shift:</strong> {{ $checkIn->shift->name }}
                    </p>

                    <p class="text-sm text-pink-800">
                        <strong>Waktu Pulang:</strong>
                        {{ \Carbon\Carbon::parse($checkIn->shift->checkout_time)->format('H:i') }}
                    </p>

                    @php
                        $checkoutTime = \Carbon\Carbon::parse($checkIn->shift->checkout_time)
                            ->setDate(now()->year, now()->month, now()->day);

                        $isCheckoutTimeValid = now()->gte($checkoutTime);
                    @endphp

                    @unless($isCheckoutTimeValid)
                        <p class="mt-2 text-sm text-red-600">
                            ⚠️ Anda tidak bisa pulang sebelum pukul
                            {{ \Carbon\Carbon::parse($checkIn->shift->checkout_time)->format('H:i') }}
                        </p>
                    @endunless

                </div>
            @endif

            <div class="mt-6 space-y-4">
                @if(!$checkIn)
                    <button
                        onclick="openCameraModal('check-in')"
                        class="w-full rounded-xl border-2 border-pink-200 bg-pink-50 px-5 py-3 text-sm font-semibold text-pink-700 hover:bg-pink-100"
                    >
                        Absen Masuk
                    </button>
                @else
                    <div class="rounded-xl border-2 border-pink-200 bg-pink-50 p-4">
                        <p class="text-sm font-medium text-pink-800">✓ Anda sudah absen masuk</p>
                        @if($checkIn->photo)
                            <p class="mt-2 text-xs text-slate-600">📷 Foto telah diupload</p>
                        @endif
                    </div>
                @endif

                @if($checkIn && !$checkOut)
                    <button
                        onclick="openCameraModal('check-out')"
                        @disabled(!($isCheckoutTimeValid ?? true))
                        class="w-full rounded-xl px-5 py-3 text-sm font-semibold {{ ($isCheckoutTimeValid ?? true) ? 'border-2 border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100' : 'cursor-not-allowed bg-slate-100 text-slate-400' }}"
                    >
                        Absen Keluar
                    </button>
                @elseif($checkOut)
                    <div class="rounded-xl border-2 border-amber-200 bg-amber-50 p-4">
                        <p class="text-sm font-medium text-amber-800">✓ Anda sudah absen keluar</p>
                        @if($checkOut->photo)
                            <p class="mt-2 text-xs text-slate-600">📷 Foto telah diupload</p>
                        @endif
                    </div>
                @endif

                <a href="{{ route('dashboard') }}" class="block rounded-xl border border-slate-200 px-5 py-3 text-center text-sm font-semibold text-slate-700 hover:bg-slate-50">
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
                        @if($attendance->photo)
                            <p class="mt-2 text-xs text-pink-600">📷 Foto tersimpan</p>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Belum ada riwayat absensi.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Camera Modal -->
    <div id="cameraModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 camera-modal-hidden">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
            <h2 class="text-xl font-bold text-slate-900">Ambil Foto Selfie</h2>
            <p class="mt-2 text-sm text-slate-600">Posisikan wajah Anda di depan kamera untuk absensi.</p>

            <div class="mt-4 space-y-4">
                <!-- Camera Preview -->
                <div id="previewContainer" class="hidden">
                    <video id="cameraPreview" autoplay playsinline></video>
                </div>

                <!-- Captured Photo -->
                <div id="capturedContainer" class="hidden">
                    <canvas id="capturedPhoto" style="display: none;"></canvas>
                    <img id="capturedPhotoImg" alt="Foto yang diambil" />
                </div>

                <!-- Error Message -->
                <div id="errorMessage" class="hidden rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700"></div>

                <!-- Buttons -->
                <div class="flex gap-3">
                    <button
                        type="button"
                        id="captureBtn"
                        onclick="capturePhoto()"
                        class="flex-1 rounded-lg bg-pink-600 px-4 py-3 text-sm font-semibold text-white hover:bg-pink-700"
                    >
                        📷 Ambil Foto
                    </button>

                    <button
                        type="button"
                        id="retakeBtn"
                        onclick="retakePhoto()"
                        class="hidden flex-1 rounded-lg border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                    >
                        Ambil Ulang
                    </button>

                    <button
                        type="button"
                        id="submitBtn"
                        onclick="submitPhoto()"
                        class="hidden flex-1 rounded-lg bg-green-600 px-4 py-3 text-sm font-semibold text-white hover:bg-green-700"
                    >
                        Simpan
                    </button>

                    <button
                        type="button"
                        onclick="closeCameraModal()"
                        class="flex-1 rounded-lg border border-slate-300 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                    >
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Form for Photo Submission -->
    <form id="photoForm" method="POST" style="display: none;">
        @csrf
        <input type="hidden" id="photoInput" name="photo" />
    </form>

    <script>
        let stream = null;
        let canvas = null;
        let context = null;
        let currentAttendanceType = null;

        async function openCameraModal(type) {
            currentAttendanceType = type;
            const modal = document.getElementById('cameraModal');
            const previewContainer = document.getElementById('previewContainer');
            const capturedContainer = document.getElementById('capturedContainer');
            const video = document.getElementById('cameraPreview');
            const errorMessage = document.getElementById('errorMessage');

            // Reset UI
            previewContainer.classList.remove('hidden');
            capturedContainer.classList.add('hidden');
            document.getElementById('captureBtn').classList.remove('hidden');
            document.getElementById('retakeBtn').classList.add('hidden');
            document.getElementById('submitBtn').classList.add('hidden');
            errorMessage.classList.add('hidden');

            // Show modal
            modal.classList.remove('camera-modal-hidden');

            try {
                // Request camera access
                stream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'user' },
                    audio: false
                });

                video.srcObject = stream;

                // Initialize canvas
                canvas = document.getElementById('capturedPhoto');
                context = canvas.getContext('2d');
            } catch (err) {
                console.error('Error accessing camera:', err);
                errorMessage.textContent = 'Gagal mengakses kamera. Pastikan Anda telah memberikan izin akses kamera.';
                errorMessage.classList.remove('hidden');
            }
        }

        function capturePhoto() {
            const video = document.getElementById('cameraPreview');
            const canvas = document.getElementById('capturedPhoto');
            const context = canvas.getContext('2d');

            // Set canvas dimensions
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;

            // Draw video frame to canvas
            context.drawImage(video, 0, 0);

            // Display captured photo
            const photoImg = document.getElementById('capturedPhotoImg');
            photoImg.src = canvas.toDataURL('image/jpeg', 0.9);

            // Update UI
            document.getElementById('previewContainer').classList.add('hidden');
            document.getElementById('capturedContainer').classList.remove('hidden');
            document.getElementById('captureBtn').classList.add('hidden');
            document.getElementById('retakeBtn').classList.remove('hidden');
            document.getElementById('submitBtn').classList.remove('hidden');

            // Stop video stream
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
        }

        function retakePhoto() {
            const modal = document.getElementById('cameraModal');
            closeCameraModal();
            setTimeout(() => openCameraModal(currentAttendanceType), 100);
        }

        function closeCameraModal() {
            const modal = document.getElementById('cameraModal');
            modal.classList.add('camera-modal-hidden');

            // Stop video stream
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
        }

        function submitPhoto() {
            const canvas = document.getElementById('capturedPhoto');
            const photoInput = document.getElementById('photoInput');

            // Convert canvas to blob and submit
            canvas.toBlob(blob => {
                const file = new File([blob], 'attendance_photo.jpg', { type: 'image/jpeg' });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);

                // Submit form
                const form = document.getElementById('photoForm');
                const route = currentAttendanceType === 'check-in' 
                    ? '{{ route("attendance.check-in") }}'
                    : '{{ route("attendance.check-out") }}';

                form.action = route;
                form.method = 'POST';

                // Create a proper FormData
                const formData = new FormData(form);
                formData.set('photo', file);

                fetch(route, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeCameraModal();
                        // Reload page to show updated status
                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
                    } else {
                        alert('Error: ' + (data.message || 'Gagal menyimpan foto'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal mengirim foto. Silakan coba lagi.');
                });
            }, 'image/jpeg', 0.9);
        }
    </script>
@endsection
