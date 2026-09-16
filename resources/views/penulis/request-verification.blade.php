@extends('layouts.penulis')

@section('title', 'Ajukan Verifikasi')
@section('page-title', 'Ajukan Verifikasi')
@section('page-subtitle', 'Ajukan permintaan verifikasi untuk publish artikel langsung')

@section('content')
<div>
 <div class="max-w-2xl mx-auto">
 <div class="mb-8">
 <h1 class="text-3xl font-bold text-news-ink mb-2">Ajukan Permintaan Verifikasi</h1>
 <p class="text-news-muted">Dengan verifikasi, Anda dapat mempublish artikel langsung tanpa menunggu review admin.</p>
 </div>

 @if(session('success'))
 <div class="bg-news-paper border border-news-line text-news-ink px-4 py-3 rounded mb-6">
 {{ session('success') }}
 </div>
 @endif

 @if(session('error'))
 <div class="bg-red-50 border border-news-line text-news-accent px-4 py-3 rounded mb-6">
 {{ session('error') }}
 </div>
 @endif

 <div class="bg-white border border-news-line p-6">
 <form method="POST" action="{{ route('penulis.verification.submit') }}" enctype="multipart/form-data">
 @csrf

 <div class="mb-6">
 <label for="verification_type" class="block text-sm font-medium text-news-ink mb-2">
 Tipe Verifikasi <span class="text-news-accent">*</span>
 </label>
 <select 
 id="verification_type" 
 name="verification_type" 
 class="w-full px-3 py-2 border border-news-line rounded-md shadow-sm focus:outline-none focus:ring-news-accent/30 focus:border-news-accent @error('verification_type') border-news-accent @enderror"
 required>
 <option value="">-- Pilih Tipe Verifikasi --</option>
 <option value="perorangan" {{ old('verification_type') == 'perorangan' ? 'selected' : '' }}>Perorangan</option>
 <option value="lembaga" {{ old('verification_type') == 'lembaga' ? 'selected' : '' }}>Lembaga (Sekolah/Perusahaan)</option>
 </select>
 @error('verification_type')
 <p class="mt-1 text-sm text-news-accent">{{ $message }}</p>
 @enderror
 <p class="mt-2 text-sm text-news-muted">
 <i class="fas fa-info-circle mr-1"></i>
 Pilih sesuai dengan status Anda: Perorangan atau Lembaga (Sekolah/Perusahaan)
 </p>
 </div>

 <div class="mb-6">
 <label for="verification_document" class="block text-sm font-medium text-news-ink mb-2">
 Upload Dokumen <span class="text-news-accent">*</span>
 </label>
 <div id="document-label" class="mb-2">
 <span class="text-sm text-news-muted" id="document-type-label">Pilih tipe verifikasi terlebih dahulu</span>
 </div>
 <input 
 type="file" 
 id="verification_document" 
 name="verification_document" 
 accept=".pdf,.jpg,.jpeg,.png"
 class="w-full px-3 py-2 border border-news-line rounded-md shadow-sm focus:outline-none focus:ring-news-accent/30 focus:border-news-accent @error('verification_document') border-news-accent @enderror"
 required>
 @error('verification_document')
 <p class="mt-1 text-sm text-news-accent">{{ $message }}</p>
 @enderror
 <p class="mt-2 text-sm text-news-muted">
 <i class="fas fa-info-circle mr-1"></i>
 <span id="document-hint">Format: PDF, JPG, PNG (Maksimal 5MB)</span>
 </p>
 </div>

 <div class="mb-6">
 <label for="reason" class="block text-sm font-medium text-news-ink mb-2">
 Alasan Permintaan Verifikasi <span class="text-news-muted">(Opsional)</span>
 </label>
 <textarea 
 id="reason" 
 name="reason" 
 rows="5" 
 class="w-full px-3 py-2 border border-news-line rounded-md shadow-sm focus:outline-none focus:ring-news-accent/30 focus:border-news-accent @error('reason') border-news-accent @enderror"
 placeholder="Jelaskan mengapa Anda ingin menjadi penulis terverifikasi...">{{ old('reason') }}</textarea>
 @error('reason')
 <p class="mt-1 text-sm text-news-accent">{{ $message }}</p>
 @enderror
 <p class="mt-2 text-sm text-news-muted">
 <i class="fas fa-info-circle mr-1"></i>
 Admin akan meninjau permintaan Anda. Setelah disetujui, artikel Anda akan langsung terpublish tanpa perlu menunggu review.
 </p>
 </div>

 <div class="bg-news-paper border border-news-line rounded-md p-4 mb-6">
 <h3 class="text-sm font-semibold text-news-ink mb-2">
 <i class="fas fa-check-circle mr-1"></i>
 Manfaat Penulis Terverifikasi:
 </h3>
 <ul class="text-sm text-news-ink space-y-1">
 <li>• Artikel langsung terpublish tanpa menunggu review</li>
 <li>• Badge "Penulis Terverifikasi" di profil Anda</li>
 <li>• Prioritas dalam pencarian penulis</li>
 </ul>
 </div>

 <div class="flex items-center justify-between">
 <a href="{{ route('penulis.dashboard') }}" class="text-news-muted hover:text-news-ink">
 <i class="fas fa-arrow-left mr-1"></i>
 Kembali ke Dashboard
 </a>
 <button type="submit" class="bg-news-ink hover:bg-news-accent text-white px-6 py-2 rounded-md font-medium">
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

