@extends('layouts.admin-simple')

@section('title', 'Edit Kategori - Admin Panel')
@section('page-title', 'Edit Kategori')
@section('page-subtitle', 'Ubah informasi kategori')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-news-line">
        <div class="px-6 py-4 border-b border-news-line">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-news-ink">Edit Kategori</h3>
                    <p class="text-sm text-news-muted">Ubah informasi kategori "{{ $category->name }}"</p>
                </div>
                <a href="{{ route('admin.categories.index') }}" 
                   class="text-news-muted hover:text-news-muted transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </a>
            </div>
        </div>

        <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="p-6">
            @csrf
            @method('PUT')
            
            <div class="space-y-6">
                <!-- Category Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-news-ink mb-2">
                        Nama Kategori <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $category->name) }}"
                           required
                           class="w-full border border-news-line rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent @error('name') border-red-500 @enderror"
                           placeholder="Masukkan nama kategori">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-news-ink mb-2">
                        Deskripsi
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="4"
                              class="w-full border border-news-line rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent @error('description') border-red-500 @enderror"
                              placeholder="Masukkan deskripsi kategori (opsional)">{{ old('description', $category->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category Color -->
                <div>
                    <label for="color" class="block text-sm font-medium text-news-ink mb-2">
                        Warna Kategori
                    </label>
                    <div class="flex items-center space-x-3">
                        <input type="color" 
                               id="color" 
                               name="color" 
                               value="{{ old('color', $category->color ?? '#b91c1c') }}"
                               class="w-12 h-10 border border-news-line rounded-lg cursor-pointer @error('color') border-red-500 @enderror">
                        <div class="flex-1">
                            <input type="text" 
                                   id="color-text" 
                                   value="{{ old('color', $category->color ?? '#b91c1c') }}"
                                   class="w-full border border-news-line rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent"
                                   placeholder="#b91c1c"
                                   readonly>
                        </div>
                    </div>
                    @error('color')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-news-muted">Pilih warna untuk kategori ini</p>
                </div>

                <label class="flex items-center gap-2 text-sm text-news-ink">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1"
                           {{ old('is_active', $category->is_active) ? 'checked' : '' }}
                           class="rounded border-news-line text-news-accent focus:ring-news-accent/30">
                    Kategori aktif (tampil di publik)
                </label>

                <!-- Category Stats -->
                <div class="bg-news-paper rounded-lg p-4">
                    <h4 class="text-sm font-medium text-news-ink mb-3">Informasi Kategori</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-news-muted">Jumlah Artikel:</span>
                            <span class="font-medium text-news-ink">{{ $category->articles_count ?? 0 }}</span>
                        </div>
                        <div>
                            <span class="text-news-muted">Status:</span>
                            <span class="font-medium text-news-ink">{{ $category->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                        </div>
                        <div>
                            <span class="text-news-muted">Dibuat:</span>
                            <span class="font-medium text-news-ink">{{ $category->created_at->format('d M Y') }}</span>
                        </div>
                        <div>
                            <span class="text-news-muted">Terakhir Diupdate:</span>
                            <span class="font-medium text-news-ink">{{ $category->updated_at->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-news-line mt-6">
                <a href="{{ route('admin.categories.index') }}" 
                   class="px-4 py-2 border border-news-line text-news-ink hover:border-news-ink hover:bg-white transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
                <div class="flex items-center space-x-3">
                    <button type="button" 
                            onclick="window.pesbarConfirm('Hapus kategori ini? Artikel terkait akan dialihkan ke kategori lain.', {danger:true}).then(ok=>{ if(ok) document.getElementById('delete-form').submit(); })"
                            class="px-4 py-2 text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                        <i class="fas fa-trash mr-2"></i>
                        Hapus
                    </button>
                    <button type="submit" 
                            class="px-6 py-2 bg-news-accent text-white rounded-lg hover:bg-news-ink transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>

        <!-- Delete Form -->
        <form id="delete-form" action="{{ route('admin.categories.destroy', $category) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
</div>

@push('scripts')
<script>
// Sync color picker with text input
document.getElementById('color').addEventListener('input', function() {
    document.getElementById('color-text').value = this.value;
});

// Sync text input with color picker
document.getElementById('color-text').addEventListener('input', function() {
    const colorValue = this.value;
    if (/^#[0-9A-F]{6}$/i.test(colorValue)) {
        document.getElementById('color').value = colorValue;
    }
});
</script>
@endpush
@endsection
