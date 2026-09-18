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
                Akun tetap atas nama Anda. Untuk tipe lembaga, nama lembaga dipakai sebagai nama tampil publik setelah disetujui.
            </p>
        </div>

        <form method="POST" action="{{ route('user.submit-upgrade-request') }}" enctype="multipart/form-data" class="space-y-6" id="upgradeForm">
            @csrf

            <div class="border border-news-line bg-news-paper p-4 sm:p-5">
                <label class="block text-sm font-semibold text-news-ink mb-3">
                    Pilih Tipe Akun Penulis <span class="text-news-accent">*</span>
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <label class="relative flex items-start gap-3 p-4 border-2 border-news-line bg-white cursor-pointer hover:border-news-ink transition-colors" id="perorangan-label">
                        <input type="radio"
                               name="verification_type"
                               value="perorangan"
                               required
                               class="sr-only peer"
                               {{ old('verification_type', 'perorangan') === 'perorangan' ? 'checked' : '' }}
                               onchange="toggleUpgradeType()">
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-news-ink">Perorangan</div>
                            <div class="text-sm text-news-muted mt-0.5">Menulis atas nama pribadi</div>
                            <div class="text-xs text-news-muted mt-2">KTP + surat permohonan</div>
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
                               onchange="toggleUpgradeType()">
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-news-ink">Lembaga</div>
                            <div class="text-sm text-news-muted mt-0.5">Menulis mewakili organisasi</div>
                            <div class="text-xs text-news-muted mt-2">SK + permohonan + surat tugas</div>
                        </div>
                        <span class="mt-1 w-4 h-4 border-2 border-news-line rounded-full peer-checked:border-news-accent peer-checked:bg-news-accent shrink-0"></span>
                    </label>
                </div>
                @error('verification_type')
                    <p class="mt-2 text-sm text-news-accent">{{ $message }}</p>
                @enderror
            </div>

            <div id="organization-section" class="hidden">
                <label for="organization_name" class="block text-sm font-medium text-news-ink mb-1.5">
                    Nama Lembaga <span class="text-news-accent">*</span>
                </label>
                <input type="text"
                       name="organization_name"
                       id="organization_name"
                       value="{{ old('organization_name') }}"
                       class="w-full px-3 py-2.5 border border-news-line text-sm focus:outline-none focus:ring-2 focus:ring-news-accent/30 focus:border-news-accent @error('organization_name') border-news-accent @enderror"
                       placeholder="Contoh: Dinas Komunikasi dan Informatika">
                <p class="mt-1 text-xs text-news-muted">Nama ini akan tampil di byline artikel dan profil publik setelah disetujui.</p>
                @error('organization_name')
                    <p class="mt-1 text-sm text-news-accent">{{ $message }}</p>
                @enderror
            </div>

            <div id="document-section" class="space-y-4">
                <div class="border border-amber-200 bg-amber-50 p-4 sm:p-5 space-y-5">
                    <h3 class="text-sm font-semibold text-news-ink" id="document-title">
                        Unggah Dokumen
                    </h3>

                    <div id="docs-perorangan" class="space-y-4 hidden">
                        @include('user.partials.upgrade-document-field', [
                            'name' => 'documents[ktp]',
                            'id' => 'doc_ktp',
                            'label' => 'KTP',
                            'hint' => 'Pastikan KTP jelas terbaca dan masih berlaku',
                            'errorKey' => 'documents.ktp',
                        ])
                        @include('user.partials.upgrade-document-field', [
                            'name' => 'documents[application_letter]',
                            'id' => 'doc_application_perorangan',
                            'label' => 'Surat permohonan menjadi penulis',
                            'hint' => 'Surat resmi bermaterai atau bertanda tangan pemohon',
                            'errorKey' => 'documents.application_letter',
                        ])
                    </div>

                    <div id="docs-lembaga" class="space-y-4 hidden">
                        @include('user.partials.upgrade-document-field', [
                            'name' => 'documents[operational_permit]',
                            'id' => 'doc_operational_permit',
                            'label' => 'Izin operasional / SK pendirian',
                            'hint' => 'Dokumen resmi lembaga yang masih berlaku',
                            'errorKey' => 'documents.operational_permit',
                        ])
                        @include('user.partials.upgrade-document-field', [
                            'name' => 'documents[application_letter]',
                            'id' => 'doc_application_lembaga',
                            'label' => 'Surat permohonan menjadi penulis',
                            'hint' => 'Permohonan atas nama lembaga',
                            'errorKey' => 'documents.application_letter',
                        ])
                        @include('user.partials.upgrade-document-field', [
                            'name' => 'documents[assignment_letter]',
                            'id' => 'doc_assignment_letter',
                            'label' => 'Surat tugas dari pimpinan lembaga',
                            'hint' => 'Menugaskan Anda (pemegang akun) sebagai penulis mewakili lembaga',
                            'errorKey' => 'documents.assignment_letter',
                        ])
                    </div>
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
                <p class="mt-1 text-xs text-news-muted">JPG, PNG, GIF — maks. 2MB. Untuk lembaga, gunakan logo jika tersedia.</p>
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
                    <li>Untuk lembaga, pemegang akun adalah orang yang ditugaskan dan bertanggung jawab atas akun</li>
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
function toggleUpgradeType() {
    const perorangan = document.querySelector('input[name="verification_type"][value="perorangan"]');
    const lembaga = document.querySelector('input[name="verification_type"][value="lembaga"]');
    const orgSection = document.getElementById('organization-section');
    const orgInput = document.getElementById('organization_name');
    const docsPerorangan = document.getElementById('docs-perorangan');
    const docsLembaga = document.getElementById('docs-lembaga');
    const documentTitle = document.getElementById('document-title');

    const peroranganInputs = docsPerorangan.querySelectorAll('input[type="file"]');
    const lembagaInputs = docsLembaga.querySelectorAll('input[type="file"]');

    if (perorangan.checked) {
        orgSection.classList.add('hidden');
        orgInput.removeAttribute('required');
        docsPerorangan.classList.remove('hidden');
        docsLembaga.classList.add('hidden');
        documentTitle.textContent = 'Dokumen Perorangan';
        peroranganInputs.forEach(function (el) { el.setAttribute('required', 'required'); el.disabled = false; });
        lembagaInputs.forEach(function (el) { el.removeAttribute('required'); el.disabled = true; el.value = ''; });
    } else if (lembaga.checked) {
        orgSection.classList.remove('hidden');
        orgInput.setAttribute('required', 'required');
        docsPerorangan.classList.add('hidden');
        docsLembaga.classList.remove('hidden');
        documentTitle.textContent = 'Dokumen Lembaga';
        lembagaInputs.forEach(function (el) { el.setAttribute('required', 'required'); el.disabled = false; });
        peroranganInputs.forEach(function (el) { el.removeAttribute('required'); el.disabled = true; el.value = ''; });
    }
}

function handleFileSelect(input) {
    const fileNameDiv = document.getElementById(input.id + '_name');
    if (!fileNameDiv) return;

    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileSize = (file.size / 1024 / 1024).toFixed(2);
        fileNameDiv.textContent = 'File terpilih: ' + file.name + ' (' + fileSize + ' MB)';
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
    toggleUpgradeType();

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
