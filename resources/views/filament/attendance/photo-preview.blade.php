<div class="rounded-lg border border-gray-300 bg-gray-50 p-4">
    <label class="block text-sm font-bold text-gray-900 mb-4">📷 Foto Selfie Absensi</label>
    @if($record?->photo)
        <div class="space-y-4">
            <div class="flex justify-center">
                <img 
                    src="{{ asset('storage/' . $record->photo) }}" 
                    alt="Foto Selfie"
                    class="rounded-lg shadow-md border border-gray-200 object-cover"
                    style="max-height: 400px; max-width: 100%;"
                />
            </div>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="font-semibold text-gray-700">File:</p>
                    <p class="text-gray-600">{{ basename($record->photo) }}</p>
                </div>
                <div>
                    <p class="font-semibold text-gray-700">Waktu Ambil:</p>
                    <p class="text-gray-600">{{ $record->attended_at?->format('d M Y H:i') }}</p>
                </div>
                <div>
                    <p class="font-semibold text-gray-700">Tipe:</p>
                    <p class="text-gray-600">{{ $record->type?->label() }}</p>
                </div>
                <div>
                    <p class="font-semibold text-gray-700">Karyawan:</p>
                    <p class="text-gray-600">{{ $record->user->name }}</p>
                </div>
            </div>
            <div class="text-xs text-gray-500 bg-white p-2 rounded border border-gray-200">
                <p>✓ Foto telah diverifikasi dan tersimpan di sistem</p>
            </div>
        </div>
    @else
        <div class="text-center py-8">
            <p class="text-gray-500">❌ Tidak ada foto tersedia</p>
        </div>
    @endif
</div>
