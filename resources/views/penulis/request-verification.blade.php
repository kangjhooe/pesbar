@extends('layouts.penulis')

@section('title', 'Ajukan Verifikasi')
@section('page-title', 'Ajukan Verifikasi')
@section('page-subtitle', 'Ajukan permintaan verifikasi untuk publish artikel langsung')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Ajukan Permintaan Verifikasi</h1>
            <p class="text-gray-600">Dengan verifikasi, Anda dapat mempublish artikel langsung tanpa menunggu review admin.</p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow p-6">
            <form method="POST" action="{{ route('penulis.verification.submit') }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-6">
                    <label for="verification_type" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipe Verifikasi <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="verification_type" 
                        name="verification_type" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('verification_type') border-red-500 @enderror"
                        required>
                        <option value="">-- Pilih Tipe Verifikasi --</option>
                        <option value="perorangan" {{ old('verification_type') == 'perorangan' ? 'selected' : '' }}>Perorangan</option>
                        <option value="lembaga" {{ old('verification_type') == 'lembaga' ? 'selected' : '' }}>Lembaga (Sekolah/Perusahaan)</option>
                    </select>
                    @error('verification_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-sm text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Pilih sesuai dengan status Anda: Perorangan atau Lembaga (Sekolah/Perusahaan)
                    </p>
                </div>

                <div class="mb-6">
                    <label for="verification_document" class="block text-sm font-medium text-gray-700 mb-2">
                        Upload Dokumen <span class="text-red-500">*</span>
                    </label>
                    <div id="document-label" class="mb-2">
                        <span class="text-sm text-gray-600" id="document-type-label">Pilih tipe verifikasi terlebih dahulu</span>
                    </div>
                    <input 
                        type="file" 
                        id="verification_document" 
                        name="verification_document" 
                        accept=".pdf,.jpg,.jpeg,.png"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('verification_document') border-red-500 @enderror"
                        required>
                    @error('verification_document')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-sm text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        <span id="document-hint">Format: PDF, JPG, PNG (Maksimal 5MB)</span>
                    </p>
                </div>

                <div class="mb-6">
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Alasan Permintaan Verifikasi <span class="text-gray-500">(Opsional)</span>
                    </label>
                    <textarea 
                        id="reason" 
                        name="reason" 
                        rows="5" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 @error('reason') border-red-500 @enderror"
                        placeholder="Jelaskan mengapa Anda ingin menjadi penulis terverifikasi...">{{ old('reason') }}</textarea>
                    @error('reason')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-sm text-gray-500">
                        <i class="fas fa-info-circle mr-1"></i>
                        Admin akan meninjau permintaan Anda. Setelah disetujui, artikel Anda akan langsung terpublish tanpa perlu menunggu review.
                    </p>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-6">
                    <h3 class="text-sm font-semibold text-blue-900 mb-2">
                        <i class="fas fa-check-circle mr-1"></i>
                        Manfaat Penulis Terverifikasi:
                    </h3>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>• Artikel langsung terpublish tanpa menunggu review</li>
                        <li>• Badge "Penulis Terverifikasi" di profil Anda</li>
                        <li>• Prioritas dalam pencarian penulis</li>
                    </ul>
                </div>

                <div class="flex items-center justify-between">
                    <a href="{{ route('penulis.dashboard') }}" class="text-gray-600 hover:text-gray-800">
                        <i class="fas fa-arrow-left mr-1"></i>
                        Kembali ke Dashboard
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-medium">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Kirim Permintaan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const verificationType = document.getElementById('verification_type');
    const documentLabel = document.getElementById('document-type-label');
    const documentHint = document.getElementById('document-hint');
    
    verificationType.addEventListener('change', function() {
        if (this.value === 'perorangan') {
            documentLabel.textContent = 'Upload KTP (Kartu Tanda Penduduk)';
            documentHint.textContent = 'Format: PDF, JPG, PNG (Maksimal 5MB). Upload foto/scan KTP Anda yang masih berlaku.';
        } else if (this.value === 'lembaga') {
            documentLabel.textContent = 'Upload Izin Operasional Lembaga';
            documentHint.textContent = 'Format: PDF, JPG, PNG (Maksimal 5MB). Upload dokumen izin operasional lembaga (sekolah/perusahaan).';
        } else {
            documentLabel.textContent = 'Pilih tipe verifikasi terlebih dahulu';
            documentHint.textContent = 'Format: PDF, JPG, PNG (Maksimal 5MB)';
        }
    });
    
    // Trigger on page load if value exists
    if (verificationType.value) {
        verificationType.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection

