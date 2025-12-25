@extends('layouts.penulis')

@section('title', 'Edit Artikel')
@section('page-title', 'Edit Artikel')
@section('page-subtitle', 'Perbarui artikel Anda')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-4xl">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Artikel</h1>
        <p class="text-gray-600">Perbarui artikel Anda</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('penulis.articles.update', $article) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="mb-6">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                    Judul Artikel <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    value="{{ old('title', $article->title) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('title') border-red-500 @enderror"
                    placeholder="Masukkan judul artikel yang menarik..."
                    required
                >
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Kategori <span class="text-red-500">*</span>
                </label>
                <select 
                    id="category_id" 
                    name="category_id" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('category_id') border-red-500 @enderror"
                    required
                >
                    <option value="">Pilih Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $article->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="featured_image" class="block text-sm font-medium text-gray-700 mb-2">
                    Gambar Utama
                </label>
                @if($article->featured_image)
                    <div class="mb-3">
                        <img src="{{ Storage::url($article->featured_image) }}" alt="Current image" class="w-32 h-32 object-cover rounded-lg">
                        <p class="text-sm text-gray-600 mt-1">Gambar saat ini</p>
                    </div>
                @endif
                <input 
                    type="file" 
                    id="featured_image" 
                    name="featured_image" 
                    accept="image/*"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('featured_image') border-red-500 @enderror"
                >
                @error('featured_image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Format: JPG, PNG, GIF. Maksimal 2MB</p>
            </div>

            <div class="mb-6">
                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                    Konten Artikel <span class="text-red-500">*</span>
                </label>
                <textarea 
                    id="content" 
                    name="content" 
                    rows="15" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('content') border-red-500 @enderror"
                    placeholder="Tulis konten artikel Anda di sini..."
                    required
                >{{ old('content', $article->content) }}</textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="tags" class="block text-sm font-medium text-gray-700 mb-2">
                    Tag
                </label>
                <input 
                    type="text" 
                    id="tags" 
                    name="tags" 
                    value="{{ old('tags', $article->tags->pluck('name')->join(', ')) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tags') border-red-500 @enderror"
                    placeholder="Pisahkan tag dengan koma, contoh: teknologi, laravel, php"
                >
                @error('tags')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Pisahkan tag dengan koma untuk memudahkan pencarian</p>
            </div>

            <!-- SEO Fields -->
            <div class="bg-gray-50 rounded-lg p-6 mb-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">SEO Settings</h3>
                
                <div class="mb-4">
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                        Custom Slug (URL)
                    </label>
                    <input 
                        type="text" 
                        id="slug" 
                        name="slug" 
                        value="{{ old('slug', $article->slug) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('slug') border-red-500 @enderror"
                        placeholder="Akan otomatis dibuat dari judul jika kosong"
                    >
                    @error('slug')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">URL artikel: {{ url('/articles/') }}/<span id="slug-preview">{{ $article->slug }}</span></p>
                </div>

                <div class="mb-4">
                    <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">
                        Meta Description
                    </label>
                    <textarea 
                        id="meta_description" 
                        name="meta_description" 
                        rows="3" 
                        maxlength="500"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('meta_description') border-red-500 @enderror"
                        placeholder="Deskripsi singkat untuk SEO (maksimal 500 karakter)"
                    >{{ old('meta_description', $article->meta_description) }}</textarea>
                    <p class="mt-1 text-sm text-gray-500"><span id="meta-desc-count">{{ strlen($article->meta_description ?? '') }}</span>/500 karakter</p>
                    @error('meta_description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-2">
                        Meta Keywords
                    </label>
                    <input 
                        type="text" 
                        id="meta_keywords" 
                        name="meta_keywords" 
                        value="{{ old('meta_keywords', $article->meta_keywords) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('meta_keywords') border-red-500 @enderror"
                        placeholder="Kata kunci dipisahkan koma, contoh: berita, pesisir barat, lampung"
                    >
                    @error('meta_keywords')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Pisahkan dengan koma untuk optimasi SEO</p>
                </div>
            </div>

            <!-- Scheduled Publish -->
            <div class="mb-6">
                <label for="scheduled_at" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-clock mr-1"></i>
                    Jadwalkan Publikasi (Opsional)
                </label>
                <input 
                    type="datetime-local" 
                    id="scheduled_at" 
                    name="scheduled_at" 
                    value="{{ old('scheduled_at', $article->scheduled_at ? $article->scheduled_at->format('Y-m-d\TH:i') : '') }}"
                    min="{{ now()->format('Y-m-d\TH:i') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('scheduled_at') border-red-500 @enderror"
                >
                @error('scheduled_at')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-sm text-gray-500">Artikel akan otomatis dipublikasikan pada waktu yang ditentukan</p>
            </div>

            <div class="mb-6">
                <div class="flex items-center">
                    <span class="text-sm font-medium text-gray-700 mr-4">Status:</span>
                    @if($article->status === 'published')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Terbit
                        </span>
                    @elseif($article->status === 'pending_review')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            Menunggu Review
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            Ditolak
                        </span>
                    @endif
                </div>
            </div>

            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    @if($article->status !== 'published')
                        <label class="flex items-center">
                            <input type="checkbox" name="save_as_draft" value="1" {{ $article->status === 'draft' ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Simpan sebagai draft</span>
                        </label>
                    @endif
                    <button type="button" onclick="previewArticle()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors text-sm">
                        <i class="fas fa-eye mr-1"></i>
                        Preview
                    </button>
                    <span id="auto-save-status" class="text-xs text-gray-500"></span>
                </div>
                <div class="flex space-x-4">
                    <a href="{{ route('penulis.dashboard') }}" class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        Update Artikel
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<!-- Quill.js CDN -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

<script>
let quill;
let autoSaveInterval;
let lastSaveTime = null;

// Initialize Quill editor
document.addEventListener('DOMContentLoaded', function() {
    quill = new Quill('#content', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'indent': '-1'}, { 'indent': '+1' }],
                [{ 'align': [] }],
                ['link', 'image', 'video'],
                ['blockquote', 'code-block'],
                ['clean']
            ]
        },
        placeholder: 'Tulis isi artikel di sini...'
    });

    // Hide the original textarea
    document.getElementById('content').style.display = 'none';

    // Create a hidden input to store the HTML content
    const hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = 'content';
    hiddenInput.id = 'content_html';
    document.querySelector('form').appendChild(hiddenInput);

    // Update hidden input when Quill content changes
    quill.on('text-change', function() {
        document.getElementById('content_html').value = quill.root.innerHTML;
        triggerAutoSave();
    });

    // Set initial content from existing article
    @if(old('content'))
        quill.root.innerHTML = {!! json_encode(old('content')) !!};
    @else
        quill.root.innerHTML = {!! json_encode($article->content) !!};
    @endif

    // Auto-save every 30 seconds
    autoSaveInterval = setInterval(autoSaveDraft, 30000);

    // Slug update
    const slugInput = document.getElementById('slug');
    const slugPreview = document.getElementById('slug-preview');
    
    slugInput.addEventListener('input', function() {
        slugPreview.textContent = this.value || '{{ $article->slug }}';
    });

    // Meta description counter
    const metaDescInput = document.getElementById('meta_description');
    const metaDescCount = document.getElementById('meta-desc-count');
    if (metaDescInput) {
        metaDescInput.addEventListener('input', function() {
            metaDescCount.textContent = this.value.length;
        });
    }
});

// Auto-save function
function autoSaveDraft() {
    const form = document.querySelector('form');
    const formData = new FormData(form);
    formData.append('_token', '{{ csrf_token() }}');
    formData.append('_method', 'PUT');
    formData.append('save_as_draft', '1');
    formData.append('content', document.getElementById('content_html').value);

    fetch('{{ route("penulis.articles.save-draft", $article) }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            lastSaveTime = new Date();
            updateAutoSaveStatus('Tersimpan otomatis ' + lastSaveTime.toLocaleTimeString());
        }
    })
    .catch(error => {
        console.error('Auto-save error:', error);
    });
}

function triggerAutoSave() {
    clearInterval(autoSaveInterval);
    autoSaveInterval = setInterval(autoSaveDraft, 30000);
    updateAutoSaveStatus('Menyimpan...');
}

function updateAutoSaveStatus(message) {
    const statusEl = document.getElementById('auto-save-status');
    if (statusEl) {
        statusEl.textContent = message;
        setTimeout(() => {
            if (statusEl.textContent === message) {
                statusEl.textContent = '';
            }
        }, 3000);
    }
}

// Preview function
function previewArticle() {
    const title = document.getElementById('title').value || '{{ $article->title }}';
    const content = quill.root.innerHTML || {!! json_encode($article->content) !!};
    const category = document.getElementById('category_id').options[document.getElementById('category_id').selectedIndex]?.text || '{{ $article->category->name ?? "Kategori" }}';
    
    const previewWindow = window.open('', '_blank', 'width=800,height=600');
    previewWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Preview: ${title}</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; max-width: 800px; margin: 0 auto; }
                h1 { color: #333; border-bottom: 2px solid #3498db; padding-bottom: 10px; }
                .meta { color: #666; font-size: 14px; margin-bottom: 20px; }
                .content { line-height: 1.6; }
            </style>
        </head>
        <body>
            <h1>${title}</h1>
            <div class="meta">Kategori: ${category}</div>
            <div class="content">${content}</div>
        </body>
        </html>
    `);
    previewWindow.document.close();
}

// Ensure Quill content is saved before form submission
document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('form').addEventListener('submit', function(e) {
        if (quill) {
            document.getElementById('content_html').value = quill.root.innerHTML;
        }
    });
});
</script>
@endpush
@endsection
