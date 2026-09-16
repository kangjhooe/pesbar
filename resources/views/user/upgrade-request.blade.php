@extends('layouts.user')

@section('title', 'Ajukan Menjadi Penulis')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white border border-news-line p-5 sm:p-8">
        @if(session('info'))
            <div class="bg-news-paper border border-news-line border-l-4 border-l-news-accent px-4 py-3 mb-6 text-sm text-news-ink">
                {{ session('info') }}
            </div>
        @endif

        <div class="mb-8 text-center sm:text-left">
            <h1 class="font-display text-2xl font-bold tracking-tight text-news-ink mb-2">
                Ajukan Menjadi Penulis
            </h1>
            <p class="text-news-muted text-sm">
                Lengkapi data di bawah. Role penulis baru aktif setelah admin menyetujui permintaan Anda.
            </p>
        </div>

        <form method="POST" action="{{ route('user.submit-upgrade-request') }}" enctype="multipart/form-data" class="space-y-6" id="upgradeForm">
            @csrf

            <div class="border border-news-line bg-news-paper p-4 sm:p-5">
                <label class="block text-sm font-semibold text-news-ink mb-3">
                    Pilih Tipe Akun <span class="text-news-accent">*</span>
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <label class="relative flex items-start gap-3 p-4 border-2 border-news-line bg-white cursor-pointer hover:border-news-ink transition-colors" id="perorangan-label">
                        <input type="radio"
                               name="verification_type"
                               value="perorangan"
                               required
                               class="sr-only peer"
                               {{ old('verification_type') === 'perorangan' ? 'checked' : '' }}
                               onchange="toggleDocumentFields()">
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-news-ink">Perorangan</div>
                            <div class="text-sm text-news-muted mt-0.5">Untuk individu</div>
                            <div class="text-xs text-news-muted mt-2">Dokumen: KTP</div>
                        </div>
                        <span class="mt-1 w-4 h-4 border-2 border-news-line rounded-full peer-checked:border-news-accent peer-checked:bg-news-accent shrink-0"></span>
                    </label>

                    <label class="relative flex items-start gap-3 p-4 border-2 border-news-line bg-white cursor-pointer hover:border-news-ink transition-colors" id="lembaga-label">
                        <input type="radio"
                               name="verification_type"
                               value="lembaga"
                               required
                               class="sr-only peer"
                               {{ old('verification_type') === 'lembaga' ? 'checked' : '' }}
                               onchange="toggleDocumentFields()">
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-news-ink">Lembaga</div>
                            <div class="text-sm text-news-muted mt-0.5">Untuk organisasi</div>
                            <div class="text-xs text-news-muted mt-2">Dokumen: Izin / SK Pendirian</div>
                        </div>
                        <span class="mt-1 w-4 h-4 border-2 border-news-line rounded-full peer-checked:border-news-accent peer-checked:bg-news-accent shrink-0"></span>
                    </label>
                </div>
                @error('verification_type')
                    <p class="mt-2 text-sm text-news-accent">{{ $message }}</p>
                @enderror
            </div>

            <div id="document-section" class="hidden">
                <div class="border border-amber-200 bg-amber-50 p-4 sm:p-5">
                    <h3 class="text-sm font-semibold text-news-ink mb-3" id="document-title">
                        Unggah Dokumen Identitas
                    </h3>
                    <label for="verification_document" class="block text-sm font-medium text-news-ink mb-2">
                        <span id="document-label-text">Upload Dokumen</span> <span class="text-news-accent">*</span>
                    </label>
                    <div class="border-2 border-dashed border-news-line bg-white p-6 text-center hover:border-news-accent transition-colors" id="upload-area">
                        <input type="file"
                               name="verification_document"
                               id="verification_document"
                               accept=".pdf,.jpg,.jpeg,.png"
                               class="hidden"
                               onchange="handleFileSelect(this)">
                        <label for="verification_document" class="cursor-pointer">
                            <i class="fas fa-cloud-upload-alt text-3xl text-news-muted mb-2"></i>
                            <p class="text-sm text-news-muted mb-1">
                                <span id="upload-text" class="text-news-accent font-semibold">Klik untuk upload</span> atau drag and drop
                            </p>
                            <p class="text-xs text-news-muted">PDF, JPG, PNG — maks. 5MB</p>
                        </label>
                        <div id="file-name" class="mt-2 text-sm text-news-ink hidden"></div>
                    </div>
                    <p class="mt-2 text-xs text-news-muted" id="document-hint">
                        <span id="document-hint-text">Pastikan dokumen jelas terbaca dan masih berlaku</span>
                    </p>
                    @error('verification_document')
                        <p class="mt-2 text-sm text-news-accent">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="bio" class="block text-sm font-medium text-news-ink mb-1.5">
                    Biografi <span class="text-news-accent">*</span>
                </label>
                <textarea name="bio"
                          id="bio"
                          rows="4"
                          required
                          class="w-full px-3 py-2.5 border border-news-line text-sm focus:outline-none focus:ring-2 focus:ring-news-accent/30 focus:border-news-accent @error('bio') border-news-accent @enderror"
                          placeholder="Ceritakan pengalaman menulis dan alasan ingin menjadi penulis...">{{ old('bio') }}</textarea>
                @error('bio')
                    <p class="mt-1 text-sm text-news-accent">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="avatar" class="block text-sm font-medium text-news-ink mb-1.5">Foto Profil</label>
                <input type="file"
                       name="avatar"
                       id="avatar"
                       accept="image/*"
                       class="w-full px-3 py-2 border border-news-line text-sm focus:outline-none focus:ring-2 focus:ring-news-accent/30 focus:border-news-accent @error('avatar') border-news-accent @enderror">
                <p class="mt-1 text-xs text-news-muted">JPG, PNG, GIF — maks. 2MB</p>
                @error('avatar')
                    <p class="mt-1 text-sm text-news-accent">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="website" class="block text-sm font-medium text-news-ink mb-1.5">Website/Blog (opsional)</label>
                <input type="url"
                       name="website"
                       id="website"
                       value="{{ old('website') }}"
                       class="w-full px-3 py-2.5 border border-news-line text-sm focus:outline-none focus:ring-2 focus:ring-news-accent/30 focus:border-news-accent @error('website') border-news-accent @enderror"
                       placeholder="https://example.com">
                @error('website')
                    <p class="mt-1 text-sm text-news-accent">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="location" class="block text-sm font-medium text-news-ink mb-1.5">Lokasi (opsional)</label>
                <input type="text"
                       name="location"
                       id="location"
                       value="{{ old('location') }}"
                       class="w-full px-3 py-2.5 border border-news-line text-sm focus:outline-none focus:ring-2 focus:ring-news-accent/30 focus:border-news-accent @error('location') border-news-accent @enderror"
                       placeholder="Kabupaten Pesisir Barat, Lampung">
                @error('location')
                    <p class="mt-1 text-sm text-news-accent">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-news-ink mb-2">Media Sosial (opsional)</label>
                <div class="space-y-2">
                    <input type="url" name="social_links[facebook]" value="{{ old('social_links.facebook') }}"
                           class="w-full px-3 py-2.5 border border-news-line text-sm focus:outline-none focus:ring-2 focus:ring-news-accent/30 focus:border-news-accent"
                           placeholder="Facebook URL">
                    <input type="url" name="social_links[twitter]" value="{{ old('social_links.twitter') }}"
                           class="w-full px-3 py-2.5 border border-news-line text-sm focus:outline-none focus:ring-2 focus:ring-news-accent/30 focus:border-news-accent"
                           placeholder="Twitter / X URL">
                    <input type="url" name="social_links[instagram]" value="{{ old('social_links.instagram') }}"
                           class="w-full px-3 py-2.5 border border-news-line text-sm focus:outline-none focus:ring-2 focus:ring-news-accent/30 focus:border-news-accent"
                           placeholder="Instagram URL">
                    <input type="url" name="social_links[linkedin]" value="{{ old('social_links.linkedin') }}"
                           class="w-full px-3 py-2.5 border border-news-line text-sm focus:outline-none focus:ring-2 focus:ring-news-accent/30 focus:border-news-accent"
                           placeholder="LinkedIn URL">
                </div>
            </div>

            <div class="border border-news-line bg-news-paper p-4">
                <h3 class="text-sm font-semibold text-news-ink mb-2">Syarat dan Ketentuan</h3>
                <ul class="text-sm text-news-muted space-y-1 list-disc pl-4">
                    <li>Konten harus berkualitas dan sesuai etika jurnalistik</li>
                    <li>Artikel melalui review sebelum dipublikasikan</li>
                    <li>Admin berhak menolak atau meminta revisi</li>
                    <li>Penulis bertanggung jawab atas keaslian konten</li>
                </ul>
            </div>

            <div class="flex items-center justify-between pt-2 gap-3">
                <a href="{{ route('user.dashboard') }}"
                   class="text-sm font-semibold text-news-muted hover:text-news-ink transition-colors">
                    Kembali
                </a>
                <button type="submit"
                        class="bg-news-accent hover:bg-red-800 text-white px-5 py-2.5 text-sm font-semibold transition-colors">
                    Ajukan Permintaan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleDocumentFields() {
    const perorangan = document.querySelector('input[name="verification_type"][value="perorangan"]');
    const lembaga = document.querySelector('input[name="verification_type"][value="lembaga"]');
    const documentSection = document.getElementById('document-section');
    const documentTitle = document.getElementById('document-title');
    const documentLabelText = document.getElementById('document-label-text');
    const documentHintText = document.getElementById('document-hint-text');
    const verificationDocument = document.getElementById('verification_document');

    if (perorangan.checked) {
        documentSection.classList.remove('hidden');
        documentTitle.textContent = 'Unggah KTP';
        documentLabelText.textContent = 'Upload KTP';
        documentHintText.textContent = 'Pastikan KTP jelas terbaca dan masih berlaku';
        verificationDocument.setAttribute('required', 'required');
    } else if (lembaga.checked) {
        documentSection.classList.remove('hidden');
        documentTitle.textContent = 'Unggah Izin Operasional / SK Pendirian';
        documentLabelText.textContent = 'Upload Izin Operasional / SK Pendirian';
        documentHintText.textContent = 'Pastikan dokumen resmi dan masih berlaku';
        verificationDocument.setAttribute('required', 'required');
    } else {
        documentSection.classList.add('hidden');
        verificationDocument.removeAttribute('required');
    }
}

function handleFileSelect(input) {
    const fileNameDiv = document.getElementById('file-name');

    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileSize = (file.size / 1024 / 1024).toFixed(2);
        fileNameDiv.textContent = `File terpilih: ${file.name} (${fileSize} MB)`;
        fileNameDiv.classList.remove('hidden');

        if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran file terlalu besar. Maksimal 5MB.');
            input.value = '';
            fileNameDiv.classList.add('hidden');
        }
    } else {
        fileNameDiv.classList.add('hidden');
    }
}

document.addEventListener('DOMContentLoaded', function () {
    toggleDocumentFields();

    document.querySelectorAll('input[name="verification_type"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            document.getElementById('perorangan-label').classList.remove('border-news-accent', 'bg-red-50');
            document.getElementById('lembaga-label').classList.remove('border-news-accent', 'bg-red-50');

            if (this.value === 'perorangan') {
                document.getElementById('perorangan-label').classList.add('border-news-accent', 'bg-red-50');
            } else {
                document.getElementById('lembaga-label').classList.add('border-news-accent', 'bg-red-50');
            }
        });

        if (radio.checked) {
            radio.dispatchEvent(new Event('change'));
        }
    });
});
</script>
@endsection
