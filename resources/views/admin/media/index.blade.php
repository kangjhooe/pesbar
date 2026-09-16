@extends('layouts.admin-simple')

@section('title', 'Media Library - Admin Panel')
@section('page-title', 'Media Library')
@section('page-subtitle', 'Kelola file media admin (terpisah dari media penulis)')

@section('content')
<div class="space-y-6">
    <div class="border border-news-line border-l-4 border-l-amber-500 bg-amber-50 px-4 py-3 text-sm text-news-ink">
        Media Library admin hanya menampilkan file di <code class="text-xs">media/admin/</code>
        (dan unggahan lama di root storage). Media penulis di
        <code class="text-xs">media/penulis/</code> serta gambar artikel
        <strong>tidak bisa dihapus dari sini</strong> — kelola lewat modul Artikel / Penulis.
    </div>

    <!-- Upload Section -->
    <div class="bg-white border border-news-line p-6">
        <h3 class="text-lg font-semibold text-news-ink mb-4">Upload Media</h3>
        <form action="{{ route('admin.media.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="flex items-center space-x-4">
                <input type="file" name="file" id="file" accept="image/*,video/*,audio/*,.pdf,.doc,.docx"
                       class="block w-full text-sm text-news-muted file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-news-paper file:text-news-accent hover:file:bg-red-50" required>
                <button type="submit" class="bg-news-accent text-white px-6 py-2 rounded-lg hover:bg-news-ink transition-colors flex items-center shrink-0">
                    <i class="fas fa-upload mr-2"></i>
                    Upload
                </button>
            </div>
            @error('file')
                <p class="text-sm text-red-600">{{ $message }}</p>
            @enderror
            <p class="text-sm text-news-muted">Maksimal 10MB. Format: JPG, PNG, GIF, MP4, PDF, DOC, DOCX</p>
        </form>
    </div>

    <!-- Media Grid -->
    <div class="bg-white border border-news-line">
        <div class="px-6 py-4 border-b border-news-line">
            <h3 class="text-lg font-semibold text-news-ink">Media Files</h3>
            <p class="text-sm text-news-muted">{{ $mediaFiles->count() }} file</p>
        </div>

        @if($mediaFiles->count() > 0)
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($mediaFiles as $file)
                <div class="group relative bg-news-paper rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                    @if(str_starts_with($file['type'], 'image/'))
                        <img src="{{ $file['url'] }}"
                             alt="{{ $file['name'] }}"
                             class="w-full h-32 object-cover">
                    @elseif(str_starts_with($file['type'], 'video/'))
                        <div class="w-full h-32 bg-news-paper flex items-center justify-center">
                            <i class="fas fa-video text-3xl text-news-muted"></i>
                        </div>
                    @elseif(str_starts_with($file['type'], 'audio/'))
                        <div class="w-full h-32 bg-news-paper flex items-center justify-center">
                            <i class="fas fa-music text-3xl text-news-muted"></i>
                        </div>
                    @elseif($file['type'] === 'application/pdf')
                        <div class="w-full h-32 bg-red-100 flex items-center justify-center">
                            <i class="fas fa-file-pdf text-3xl text-red-500"></i>
                        </div>
                    @elseif(str_contains($file['type'], 'document') || str_contains($file['type'], 'word'))
                        <div class="w-full h-32 bg-news-paper flex items-center justify-center">
                            <i class="fas fa-file-word text-3xl text-news-accent"></i>
                        </div>
                    @else
                        <div class="w-full h-32 bg-news-paper flex items-center justify-center">
                            <i class="fas fa-file text-3xl text-news-muted"></i>
                        </div>
                    @endif

                    <div class="p-2">
                        <p class="text-xs text-news-muted truncate" title="{{ $file['name'] }}">{{ $file['name'] }}</p>
                        <p class="text-xs text-news-muted">{{ number_format($file['size'] / 1024, 1) }} KB</p>
                        <p class="text-xs text-news-muted">{{ date('d/m/Y', $file['modified']) }}</p>
                        @if(($file['scope'] ?? '') === 'legacy')
                            <p class="text-[10px] uppercase tracking-wide text-amber-700 mt-0.5">Legacy</p>
                        @endif
                        <div class="mt-2 flex flex-wrap gap-1 lg:hidden">
                            <a href="{{ $file['url'] }}" target="_blank"
                               class="inline-flex items-center px-2 py-1 text-[11px] border border-news-line bg-white text-news-ink">
                                <i class="fas fa-eye mr-1"></i>Lihat
                            </a>
                            <button type="button" onclick="copyToClipboard(@js($file['url']))"
                                    class="inline-flex items-center px-2 py-1 text-[11px] border border-news-line bg-white text-news-ink">
                                <i class="fas fa-copy mr-1"></i>Copy
                            </button>
                            <form action="{{ route('admin.media.delete') }}" method="POST" class="inline js-delete-media-form">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="path" value="{{ $file['path'] }}">
                                <button type="submit"
                                        class="inline-flex items-center px-2 py-1 text-[11px] border border-red-200 bg-white text-red-600">
                                    <i class="fas fa-trash mr-1"></i>Hapus
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 items-center justify-center opacity-0 group-hover:opacity-100 hidden lg:flex">
                        <div class="flex space-x-2">
                            <a href="{{ $file['url'] }}"
                               target="_blank"
                               class="bg-white text-news-ink p-2 rounded-full hover:bg-news-paper transition-colors"
                               title="Lihat">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button type="button"
                                    onclick="copyToClipboard(@js($file['url']))"
                                    class="bg-white text-news-ink p-2 rounded-full hover:bg-news-paper transition-colors"
                                    title="Copy URL">
                                <i class="fas fa-copy"></i>
                            </button>
                            <form action="{{ route('admin.media.delete') }}" method="POST" class="inline js-delete-media-form">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="path" value="{{ $file['path'] }}">
                                <button type="submit"
                                        class="bg-white text-red-600 p-2 rounded-full hover:bg-red-50 transition-colors"
                                        title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="p-12 text-center">
            <div class="text-news-muted">
                <i class="fas fa-images text-4xl mb-4"></i>
                <p class="text-lg font-medium">Belum ada file media admin</p>
                <p class="text-sm">Upload file pertama Anda</p>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 bg-news-ink text-white px-4 py-2 z-50';
        toast.textContent = 'URL berhasil disalin!';
        document.body.appendChild(toast);
        setTimeout(() => document.body.removeChild(toast), 3000);
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
    });
}

document.querySelectorAll('.js-delete-media-form').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const formEl = this;
        window.pesbarConfirm('Hapus file media admin ini?', { danger: true }).then(function(ok) {
            if (ok) formEl.submit();
        });
    });
});

document.getElementById('file')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    if (file.size > 10 * 1024 * 1024) {
        if (window.pesbarAlert) {
            window.pesbarAlert('File terlalu besar. Maksimal 10MB.');
        } else {
            alert('File terlalu besar. Maksimal 10MB.');
        }
        e.target.value = '';
    }
});
</script>
@endpush
@endsection
