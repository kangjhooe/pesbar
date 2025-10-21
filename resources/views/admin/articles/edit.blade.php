@extends('layouts.admin-simple')

@section('title', 'Edit Artikel - Admin Panel')
@section('page-title', 'Edit Artikel')
@section('page-subtitle', 'Edit artikel: ' . $article->title)

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.articles.update', $article) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')
        
        <!-- Basic Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Dasar</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        Judul Artikel <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="title" name="title" value="{{ old('title', $article->title) }}" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-500 @enderror"
                           placeholder="Masukkan judul artikel" required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <select id="category_id" name="category_id" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('category_id') border-red-500 @enderror" required>
                        <option value="">Pilih Kategori</option>
                        @forelse($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $article->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @empty
                            <option value="" disabled>Tidak ada kategori tersedia</option>
                        @endforelse
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipe <span class="text-red-500">*</span>
                    </label>
                    <select id="type" name="type" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('type') border-red-500 @enderror" required>
                        <option value="">Pilih Tipe</option>
                        <option value="berita" {{ old('type', $article->type) == 'berita' ? 'selected' : '' }}>Berita</option>
                        <option value="artikel" {{ old('type', $article->type) == 'artikel' ? 'selected' : '' }}>Artikel</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select id="status" name="status" 
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror" required>
                        <option value="draft" {{ old('status', $article->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $article->status) == 'published' ? 'selected' : '' }}>Dipublikasi</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="published_at" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Publikasi
                    </label>
                    <input type="datetime-local" id="published_at" name="published_at" 
                           value="{{ old('published_at', $article->published_at ? $article->published_at->format('Y-m-d\TH:i') : '') }}" 
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('published_at') border-red-500 @enderror">
                    @error('published_at')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6">
                <label for="excerpt" class="block text-sm font-medium text-gray-700 mb-2">
                    Ringkasan <span class="text-red-500">*</span>
                </label>
                <textarea id="excerpt" name="excerpt" rows="3" 
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('excerpt') border-red-500 @enderror"
                          placeholder="Ringkasan singkat artikel" required>{{ old('excerpt', $article->excerpt ?? '') }}</textarea>
                @error('excerpt')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Featured Image -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Gambar Utama</h3>
            
            <div class="space-y-4">
                @if($article->featured_image && file_exists(public_path('storage/' . $article->featured_image)))
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Saat Ini</label>
                    <img src="{{ asset('storage/' . $article->featured_image) }}" alt="Current image" 
                         class="max-w-xs h-48 object-cover rounded-lg border">
                </div>
                @elseif($article->featured_image)
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Gambar Saat Ini</label>
                    <div class="max-w-xs h-48 bg-gray-200 rounded-lg border flex items-center justify-center">
                        <span class="text-gray-500 text-sm">Gambar tidak ditemukan</span>
                    </div>
                </div>
                @endif
                
                <div>
                    <label for="featured_image" class="block text-sm font-medium text-gray-700 mb-2">
                        Upload Gambar Baru
                    </label>
                    <input type="file" id="featured_image" name="featured_image" accept="image/*"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('featured_image') border-red-500 @enderror"
                           onchange="previewImage(this)">
                    @error('featured_image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-gray-500">Kosongkan jika tidak ingin mengubah gambar</p>
                </div>
                
                <div id="image-preview" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Preview Gambar Baru</label>
                    <img id="preview" src="" alt="Preview" class="max-w-xs h-48 object-cover rounded-lg border">
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Konten Artikel</h3>
            
            <div>
                <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                    Isi Artikel <span class="text-red-500">*</span>
                </label>
                <div id="editor" class="min-h-[300px] border border-gray-300 rounded-lg focus-within:ring-2 focus-within:ring-blue-500 focus-within:border-blue-500 @error('content') border-red-500 @enderror"></div>
                <textarea id="content" name="content" style="display: none;" required>{{ old('content', $article->content) }}</textarea>
                @error('content')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Tags -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Tags</h3>
            
            @if($tags->count() > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                    @foreach($tags as $tag)
                    <label class="flex items-center">
                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}" 
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                               {{ in_array($tag->id, old('tags', $article->tags->pluck('id')->toArray())) ? 'checked' : '' }}>
                        <span class="ml-2 text-sm text-gray-700">{{ $tag->name }}</span>
                    </label>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-tags text-4xl mb-4"></i>
                    <p>Belum ada tags tersedia. Silakan tambahkan tags terlebih dahulu.</p>
                </div>
            @endif
        </div>

        <!-- Options -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Opsi Tambahan</h3>
            
            <div class="space-y-4">
                <label class="flex items-center">
                    <input type="checkbox" name="is_featured" value="1" 
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                           {{ old('is_featured', $article->is_featured) ? 'checked' : '' }}>
                    <span class="ml-2 text-sm text-gray-700">Tandai sebagai Featured</span>
                </label>
                
                <label class="flex items-center">
                    <input type="checkbox" name="is_breaking" value="1" 
                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                           {{ old('is_breaking', $article->is_breaking) ? 'checked' : '' }}>
                    <span class="ml-2 text-sm text-gray-700">Tandai sebagai Breaking News</span>
                </label>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('admin.articles.index') }}" 
               class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                Batal
            </a>
            <button type="submit" 
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                Update Artikel
            </button>
        </div>
    </form>
</div>

@push('scripts')
<!-- Quill.js CDN -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Quill editor
    const quill = new Quill('#editor', {
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

    // Update hidden textarea when Quill content changes
    quill.on('text-change', function() {
        document.getElementById('content').value = quill.root.innerHTML;
    });

    // Set initial content from existing article
    @if(old('content'))
        quill.root.innerHTML = {!! json_encode(old('content')) !!};
        document.getElementById('content').value = quill.root.innerHTML;
    @else
        quill.root.innerHTML = {!! json_encode($article->content ?? '') !!};
        document.getElementById('content').value = quill.root.innerHTML;
    @endif

    // Ensure Quill content is saved before form submission
    document.querySelector('form').addEventListener('submit', function(e) {
        document.getElementById('content').value = quill.root.innerHTML;
    });
});

function previewImage(input) {
    const preview = document.getElementById('preview');
    const previewContainer = document.getElementById('image-preview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.classList.remove('hidden');
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        previewContainer.classList.add('hidden');
    }
}
</script>
@endpush
@endsection
