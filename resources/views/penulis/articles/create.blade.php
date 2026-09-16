@extends('layouts.penulis')

@section('title', 'Buat Artikel Baru')
@section('page-title', 'Buat Artikel Baru')
@section('page-subtitle', 'Tulis artikel menarik untuk dibaca oleh pengunjung')

@section('content')
<div class="max-w-4xl">
 <div class="mb-8">
 <h1 class="text-3xl font-bold text-news-ink mb-2">Buat Artikel Baru</h1>
 <p class="text-news-muted">Tulis artikel menarik untuk dibaca oleh pengunjung</p>
 </div>

 @if(session('success'))
 <div class="bg-news-paper border border-news-line text-news-ink px-4 py-3 rounded mb-6">
 {{ session('success') }}
 </div>
 @endif

 <form action="{{ route('penulis.articles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
 @csrf
 
 <div class="bg-white border border-news-line p-6">
 <div class="mb-6">
 <label for="title" class="block text-sm font-medium text-news-ink mb-2">
 Judul Artikel <span class="text-news-accent">*</span>
 </label>
 <input 
 type="text" 
 id="title" 
 name="title" 
 value="{{ old('title') }}"
 class="w-full px-3 py-2 border border-news-line rounded-md focus:outline-none focus:ring-2 focus:ring-news-accent/30 focus:border-news-accent @error('title') border-news-accent @enderror"
 placeholder="Masukkan judul artikel yang menarik..."
 required
 >
 @error('title')
 <p class="mt-1 text-sm text-news-accent">{{ $message }}</p>
 @enderror
 </div>

 <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
 <div>
 <label for="category_id" class="block text-sm font-medium text-news-ink mb-2">
 Kategori <span class="text-news-accent">*</span>
 </label>
 <select 
 id="category_id" 
 name="category_id" 
 class="w-full px-3 py-2 border border-news-line rounded-md focus:outline-none focus:ring-2 focus:ring-news-accent/30 focus:border-news-accent @error('category_id') border-news-accent @enderror"
 required
 >
 <option value="">Pilih Kategori</option>
 @foreach($categories as $category)
 <option value="{{ $category->id }}" data-slug="{{ $category->slug }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
 {{ $category->name }}
 </option>
 @endforeach
 </select>
 @error('category_id')
 <p class="mt-1 text-sm text-news-accent">{{ $message }}</p>
 @enderror
 </div>
 </div>

 <div class="mb-6">
 <label for="excerpt" class="block text-sm font-medium text-news-ink mb-2">
 Ringkasan <span class="text-news-accent">*</span>
 </label>
 <textarea 
 id="excerpt" 
 name="excerpt" 
 rows="3" 
 maxlength="500"
 class="w-full px-3 py-2 border border-news-line rounded-md focus:outline-none focus:ring-2 focus:ring-news-accent/30 focus:border-news-accent @error('excerpt') border-news-accent @enderror"
 placeholder="Ringkasan singkat artikel (maksimal 500 karakter)"
 required
 >{{ old('excerpt') }}</textarea>
 <p class="mt-1 text-sm text-news-muted"><span id="excerpt-count">0</span>/500 karakter</p>
 @error('excerpt')
 <p class="mt-1 text-sm text-news-accent">{{ $message }}</p>
 @enderror
 </div>

 <div class="mb-6">
 <label for="featured_image" class="block text-sm font-medium text-news-ink mb-2">
 Gambar Utama
 </label>
 <input 
 type="file" 
 id="featured_image" 
 name="featured_image" 
 accept="image/*"
 class="w-full px-3 py-2 border border-news-line rounded-md focus:outline-none focus:ring-2 focus:ring-news-accent/30 focus:border-news-accent @error('featured_image') border-news-accent @enderror"
 >
 @error('featured_image')
 <p class="mt-1 text-sm text-news-accent">{{ $message }}</p>
 @enderror
 <p class="mt-1 text-sm text-news-muted">Format: JPG, PNG, GIF. Maksimal 2MB</p>
 </div>

 <div class="mb-6">
 <label for="content" class="block text-sm font-medium text-news-ink mb-2">
 Konten Artikel <span class="text-news-accent">*</span>
 </label>
 <!-- Quill Editor Container -->
 <div id="editor-container" class="border border-news-line rounded-md @error('content') border-news-accent @enderror" style="min-height: 400px;">
 <!-- Quill editor will be initialized here -->
 </div>
 <!-- Hidden textarea for form submission -->
 <textarea 
 id="content" 
 name="content" 
 style="display: none;"
 required
 >{{ old('content') }}</textarea>
 <!-- Hidden input for HTML content -->
 <input type="hidden" id="content_html" name="content_html" value="{{ old('content') }}">
 @error('content')
 <p class="mt-1 text-sm text-news-accent">{{ $message }}</p>
 @enderror
 <p class="mt-2 text-sm text-news-muted">
 <i class="fas fa-info-circle mr-1"></i>
 Gunakan toolbar di atas untuk memformat teks, menambahkan link, gambar, dan elemen lainnya.
 </p>
 </div>

 <!-- Tags -->
 <div class="mb-6">
 <label class="block text-sm font-medium text-news-ink mb-4">
 Tags
 </label>
 
 @if($tags->count() > 0)
 <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
 @foreach($tags as $tag)
 <label class="flex items-center">
 <input type="checkbox" name="tags[]" value="{{ $tag->id }}" 
 class="rounded border-news-line text-news-accent focus:ring-news-accent/30"
 {{ in_array($tag->id, old('tags', [])) ? 'checked' : '' }}>
 <span class="ml-2 text-sm text-news-ink">{{ $tag->name }}</span>
 </label>
 @endforeach
 </div>
 @else
 <div class="text-center py-4 text-news-muted">
 <i class="fas fa-tags text-2xl mb-2"></i>
 <p class="text-sm">Belum ada tags tersedia</p>
 </div>
 @endif
 @error('tags')
 <p class="mt-1 text-sm text-news-accent">{{ $message }}</p>
 @enderror
 </div>

 <!-- SEO Fields -->
 <div class="bg-news-paper rounded-lg p-6 mb-6">
 <h3 class="text-lg font-semibold text-news-ink mb-4">SEO Settings</h3>
 
 <div class="mb-4">
 <label for="slug" class="block text-sm font-medium text-news-ink mb-2">
 Custom Slug (URL)
 </label>
 <input 
 type="text" 
 id="slug" 
 name="slug" 
 value="{{ old('slug') }}"
 class="w-full px-3 py-2 border border-news-line rounded-md focus:outline-none focus:ring-2 focus:ring-news-accent/30 focus:border-news-accent @error('slug') border-news-accent @enderror"
 placeholder="Akan otomatis dibuat dari judul jika kosong"
 >
 @error('slug')
 <p class="mt-1 text-sm text-news-accent">{{ $message }}</p>
 @enderror
 <p class="mt-1 text-sm text-news-muted">URL artikel: {{ url('/') }}/<span id="category-slug-preview">kategori</span>/<span id="slug-preview">slug-akan-muncul-di-sini</span></p>
 </div>

 <div class="mb-4">
 <label for="meta_description" class="block text-sm font-medium text-news-ink mb-2">
 Meta Description
 </label>
 <textarea 
 id="meta_description" 
 name="meta_description" 
 rows="3" 
 maxlength="500"
 class="w-full px-3 py-2 border border-news-line rounded-md focus:outline-none focus:ring-2 focus:ring-news-accent/30 focus:border-news-accent @error('meta_description') border-news-accent @enderror"
 placeholder="Deskripsi singkat untuk SEO (maksimal 500 karakter)"
 >{{ old('meta_description') }}</textarea>
 <p class="mt-1 text-sm text-news-muted"><span id="meta-desc-count">0</span>/500 karakter</p>
 @error('meta_description')
 <p class="mt-1 text-sm text-news-accent">{{ $message }}</p>
 @enderror
 </div>

 <div>
 <label for="meta_keywords" class="block text-sm font-medium text-news-ink mb-2">
 Meta Keywords
 </label>
 <input 
 type="text" 
 id="meta_keywords" 
 name="meta_keywords" 
 value="{{ old('meta_keywords') }}"
 class="w-full px-3 py-2 border border-news-line rounded-md focus:outline-none focus:ring-2 focus:ring-news-accent/30 focus:border-news-accent @error('meta_keywords') border-news-accent @enderror"
 placeholder="Kata kunci dipisahkan koma, contoh: berita, pesisir barat, lampung"
 >
 @error('meta_keywords')
 <p class="mt-1 text-sm text-news-accent">{{ $message }}</p>
 @enderror
 <p class="mt-1 text-sm text-news-muted">Pisahkan dengan koma untuk optimasi SEO</p>
 </div>
 </div>

 <!-- Scheduled Publish -->
 <div class="mb-6">
 <label for="scheduled_at" class="block text-sm font-medium text-news-ink mb-2">
 <i class="fas fa-clock mr-1"></i>
 Jadwalkan Publikasi (Opsional)
 </label>
 <input 
 type="datetime-local" 
 id="scheduled_at" 
 name="scheduled_at" 
 value="{{ old('scheduled_at') }}"
 min="{{ now()->format('Y-m-d\TH:i') }}"
 class="w-full px-3 py-2 border border-news-line rounded-md focus:outline-none focus:ring-2 focus:ring-news-accent/30 focus:border-news-accent @error('scheduled_at') border-news-accent @enderror"
 >
 @error('scheduled_at')
 <p class="mt-1 text-sm text-news-accent">{{ $message }}</p>
 @enderror
 <p class="mt-1 text-sm text-news-muted">Artikel akan otomatis dipublikasikan pada waktu yang ditentukan</p>
 </div>

 @if(auth()->user()->isPublishRestricted())
 <div class="bg-amber-50 border border-news-line rounded-lg p-4 mb-6">
 <div class="flex">
 <div class="flex-shrink-0">
 <svg class="h-5 w-5 text-amber-700" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
 </svg>
 </div>
 <div class="ml-3">
 <h3 class="text-sm font-medium text-news-ink">Publish langsung dibatasi</h3>
 <div class="mt-2 text-sm text-news-muted">
 <p>Artikel Anda akan masuk antrean review redaksi sampai pembatasan dicabut.</p>
 </div>
 </div>
 </div>
 </div>
 @else
 <div class="bg-news-paper border border-news-line rounded-lg p-4 mb-6">
 <div class="flex">
 <div class="flex-shrink-0">
 <svg class="h-5 w-5 text-news-ink" fill="currentColor" viewBox="0 0 20 20">
 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
 </svg>
 </div>
 <div class="ml-3">
 <h3 class="text-sm font-medium text-news-ink">Penulis Terverifikasi</h3>
 <div class="mt-2 text-sm text-news-ink">
 <p>Artikel Anda akan langsung dipublikasikan setelah disimpan.</p>
 </div>
 </div>
 </div>
 </div>
 @endif

 <div class="flex justify-between items-center">
 <div class="flex items-center space-x-4">
 <label class="flex items-center">
 <input type="checkbox" name="save_as_draft" value="1" class="rounded border-news-line text-news-accent focus:ring-news-accent/30">
 <span class="ml-2 text-sm text-news-ink">Simpan sebagai draft</span>
 </label>
 <button type="button" onclick="previewArticle()" class="px-4 py-2 border border-news-line rounded-md text-news-ink hover:bg-news-paper transition-colors text-sm">
 <i class="fas fa-eye mr-1"></i>
 Preview
 </button>
 <span id="auto-save-status" class="text-xs text-news-muted"></span>
 </div>
 <div class="flex space-x-4">
 <a href="{{ route('penulis.articles.index') }}" class="px-6 py-2 border border-news-line rounded-md text-news-ink hover:bg-news-paper transition-colors">
 Batal
 </a>
 <button type="submit" class="px-6 py-2 bg-news-ink text-white rounded-md hover:bg-news-accent transition-colors">
 Simpan Artikel
 </button>
 </div>
 </div>
 </div>
 </form>
</div>

@push('styles')
<!-- Quill.js CSS -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<style>
 #editor-container {
 min-height: 400px;
 background: white;
 }
 #editor-container .ql-editor {
 min-height: 350px;
 font-size: 16px;
 line-height: 1.6;
 }
 #editor-container .ql-toolbar {
 border-top: 1px solid #ccc;
 border-left: 1px solid #ccc;
 border-right: 1px solid #ccc;
 border-bottom: none;
 border-radius: 4px 4px 0 0;
 }
 #editor-container .ql-container {
 border-bottom: 1px solid #ccc;
 border-left: 1px solid #ccc;
 border-right: 1px solid #ccc;
 border-top: none;
 border-radius: 0 0 4px 4px;
 }
</style>
@endpush

@push('scripts')
<!-- Quill.js JS -->
<script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

<script>
let quill;
let autoSaveInterval;
let lastSaveTime = null;

// Wait for DOM and Quill to be ready
document.addEventListener('DOMContentLoaded', function() {
 // Check if editor container exists
 const editorContainer = document.getElementById('editor-container');
 if (!editorContainer) {
 console.error('Editor container not found!');
 return;
 }

 // Initialize Quill on the container div
 try {
 quill = new Quill('#editor-container', {
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

 // Set initial content if exists
 @if(old('content'))
 const oldContent = {!! json_encode(old('content')) !!};
 if (oldContent) {
 quill.root.innerHTML = oldContent;
 document.getElementById('content_html').value = oldContent;
 document.getElementById('content').value = oldContent;
 }
 @endif

 // Update hidden inputs when Quill content changes
 quill.on('text-change', function() {
 const htmlContent = quill.root.innerHTML;
 document.getElementById('content_html').value = htmlContent;
 document.getElementById('content').value = htmlContent;
 triggerAutoSave();
 });
 } catch (error) {
 console.error('Error initializing Quill:', error);
 }

 // Auto-save every 30 seconds
 autoSaveInterval = setInterval(autoSaveDraft, 30000);

 // Slug auto-generate from title
 const titleInput = document.getElementById('title');
 const slugInput = document.getElementById('slug');
 const slugPreview = document.getElementById('slug-preview');
 const categorySelect = document.getElementById('category_id');
 const categorySlugPreview = document.getElementById('category-slug-preview');

 function updateCategorySlugPreview() {
 if (!categorySelect || !categorySlugPreview) return;
 const selected = categorySelect.options[categorySelect.selectedIndex];
 categorySlugPreview.textContent = (selected && selected.dataset.slug) ? selected.dataset.slug : 'kategori';
 }

 if (categorySelect) {
 categorySelect.addEventListener('change', updateCategorySlugPreview);
 updateCategorySlugPreview();
 }
 
 titleInput.addEventListener('input', function() {
 if (!slugInput.value || slugInput.dataset.autoGenerated === 'true') {
 const slug = this.value.toLowerCase()
 .replace(/[^a-z0-9]+/g, '-')
 .replace(/(^-|-$)/g, '');
 slugInput.value = slug;
 slugInput.dataset.autoGenerated = 'true';
 slugPreview.textContent = slug || 'slug-akan-muncul-di-sini';
 }
 });

 slugInput.addEventListener('input', function() {
 slugInput.dataset.autoGenerated = 'false';
 slugPreview.textContent = this.value || 'slug-akan-muncul-di-sini';
 });

 // Meta description counter
 const metaDescInput = document.getElementById('meta_description');
 const metaDescCount = document.getElementById('meta-desc-count');
 if (metaDescInput) {
 metaDescInput.addEventListener('input', function() {
 metaDescCount.textContent = this.value.length;
 });
 }

 // Excerpt counter
 const excerptInput = document.getElementById('excerpt');
 const excerptCount = document.getElementById('excerpt-count');
 if (excerptInput) {
 excerptInput.addEventListener('input', function() {
 excerptCount.textContent = this.value.length;
 });
 // Set initial count
 excerptCount.textContent = excerptInput.value.length;
 }
});

// Auto-save function
function autoSaveDraft() {
 const form = document.querySelector('form');
 const formData = new FormData(form);
 formData.append('_token', '{{ csrf_token() }}');
 formData.append('save_as_draft', '1');
 formData.append('content', document.getElementById('content_html').value);

 fetch('{{ route("penulis.articles.save-draft-create") }}', {
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
 // Update form action if article_id is returned (for subsequent auto-saves)
 if (data.article_id) {
 // Store article_id for future auto-saves
 window.currentArticleId = data.article_id;
 }
 }
 })
 .catch(error => {
 console.error('Auto-save error:', error);
 });
}

function triggerAutoSave() {
 // Reset auto-save timer
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
 const title = document.getElementById('title').value || 'Judul Artikel';
 const content = quill.root.innerHTML || '<p>Konten artikel...</p>';
 const category = document.getElementById('category_id').options[document.getElementById('category_id').selectedIndex]?.text || 'Kategori';
 
 const previewWindow = window.open('', '_blank', 'width=900,height=700');
 previewWindow.document.write(`
 <!DOCTYPE html>
 <html>
 <head>
 <title>Preview: ${title}</title>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <style>
 * {
 margin: 0;
 padding: 0;
 box-sizing: border-box;
 }
 body { 
 font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; 
 padding: 30px 20px; 
 max-width: 800px; 
 margin: 0 auto;
 line-height: 1.7;
 color: #333;
 background: #fafafa;
 }
 .preview-container {
 background: white;
 padding: 40px;
 border-radius: 8px;
 box-shadow: 0 2px 8px rgba(0,0,0,0.1);
 }
 h1 { 
 color: #0a0a0a; 
 border-bottom: 3px solid #b91c1c; 
 padding-bottom: 15px;
 margin-bottom: 20px;
 font-size: 2em;
 }
 h2 {
 color: #0a0a0a;
 margin-top: 1.5em;
 margin-bottom: 0.75em;
 font-size: 1.5em;
 }
 h3 {
 color: #5c5c5c;
 margin-top: 1.25em;
 margin-bottom: 0.5em;
 font-size: 1.25em;
 }
 .meta { 
 color: #5c5c5c; 
 font-size: 14px; 
 margin-bottom: 30px;
 padding: 10px;
 background: #fafafa;
 border-radius: 6px;
 }
 .content { 
 line-height: 1.8;
 color: #5c5c5c;
 }
 .content p {
 margin-bottom: 1.25em;
 }
 .content img {
 max-width: 100%;
 height: auto;
 border-radius: 6px;
 margin: 1.5em 0;
 }
 /* List Styling - Enhanced with !important */
 .content ul,
 .content ol {
 margin-top: 1.25em !important;
 margin-bottom: 1.25em !important;
 padding-left: 2em !important;
 list-style-position: outside !important;
 }
 .content ul {
 list-style-type: disc !important;
 }
 .content ol {
 list-style-type: decimal !important;
 }
 .content ul ul,
 .content ol ol,
 .content ul ol,
 .content ol ul {
 margin-top: 0.75em !important;
 margin-bottom: 0.75em !important;
 }
 .content li {
 margin-top: 0.75em !important;
 margin-bottom: 0.75em !important;
 padding-left: 0.5em !important;
 line-height: 1.8 !important;
 display: list-item !important;
 list-style-position: outside !important;
 }
 .content ul > li {
 list-style-type: disc !important;
 }
 .content ol > li {
 list-style-type: decimal !important;
 }
 .content ul > li::marker {
 color: #b91c1c !important;
 font-size: 1.2em !important;
 font-weight: normal !important;
 }
 .content ol > li::marker {
 color: #b91c1c !important;
 font-weight: 600 !important;
 }
 /* Nested lists */
 .content ul ul {
 list-style-type: circle !important;
 }
 .content ul ul ul {
 list-style-type: square !important;
 }
 .content ol ol {
 list-style-type: lower-alpha !important;
 }
 .content ol ol ol {
 list-style-type: lower-roman !important;
 }
 /* Fix for Quill editor classes */
 .content ul[class*="ql-"],
 .content ol[class*="ql-"] {
 list-style-type: disc !important;
 padding-left: 2em !important;
 }
 .content ol[class*="ql-"] {
 list-style-type: decimal !important;
 }
 .content blockquote {
 border-left: 4px solid #b91c1c;
 padding-left: 1.5em;
 margin: 1.5em 0;
 color: #5c5c5c;
 font-style: italic;
 }
 .content a {
 color: #b91c1c;
 text-decoration: underline;
 }
 .content a:hover {
 color: #991b1b;
 }
 .content strong {
 font-weight: 600;
 color: #0a0a0a;
 }
 .content em {
 font-style: italic;
 }
 .content code {
 background: #fafafa;
 padding: 2px 6px;
 border-radius: 4px;
 font-family: 'Courier New', monospace;
 font-size: 0.9em;
 }
 .content pre {
 background: #0a0a0a;
 color: #fafafa;
 padding: 1.5em;
 border-radius: 6px;
 overflow-x: auto;
 margin: 1.5em 0;
 }
 .content pre code {
 background: transparent;
 padding: 0;
 color: inherit;
 }
 </style>
 </head>
 <body>
 <div class="preview-container">
 <h1>${title}</h1>
 <div class="meta">Kategori: ${category}</div>
 <div class="content">${content}</div>
 </div>
 </body>
 </html>
 `);
 previewWindow.document.close();
}

// Ensure Quill content is saved before form submission
document.addEventListener('DOMContentLoaded', function() {
 const form = document.querySelector('form');
 if (form) {
 form.addEventListener('submit', function(e) {
 if (quill) {
 const htmlContent = quill.root.innerHTML;
 document.getElementById('content_html').value = htmlContent;
 document.getElementById('content').value = htmlContent;
 }
 });
 }
});
</script>
@endpush
@endsection
