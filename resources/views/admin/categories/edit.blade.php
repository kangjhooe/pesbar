@extends('layouts.admin-simple')

@section('title', 'Edit Kategori - Admin Panel')
@section('page-title', 'Edit Kategori')
@section('page-subtitle', 'Ubah informasi kategori')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Edit Kategori</h3>
                    <p class="text-sm text-gray-600">Ubah informasi kategori "{{ $category->name }}"</p>
                </div>
                <a href="{{ route('admin.categories.index') }}" 
                   class="text-gray-400 hover:text-gray-600 transition-colors">
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
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Kategori <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $category->name) }}"
                           required
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                           placeholder="Masukkan nama kategori">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                        Deskripsi
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="4"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                              placeholder="Masukkan deskripsi kategori (opsional)">{{ old('description', $category->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category Color -->
                <div>
                    <label for="color" class="block text-sm font-medium text-gray-700 mb-2">
                        Warna Kategori
                    </label>
                    <div class="flex items-center space-x-3">
                        <input type="color" 
                               id="color" 
                               name="color" 
                               value="{{ old('color', $category->color ?? '#3b82f6') }}"
                               class="w-12 h-10 border border-gray-300 rounded-lg cursor-pointer @error('color') border-red-500 @enderror">
                        <div class="flex-1">
                            <input type="text" 
                                   id="color-text" 
                                   value="{{ old('color', $category->color ?? '#3b82f6') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="#3b82f6"
                                   readonly>
                        </div>
                    </div>
                    @error('color')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Pilih warna untuk kategori ini</p>
                </div>

                <!-- Category Stats -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">Informasi Kategori</h4>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Jumlah Artikel:</span>
                            <span class="font-medium text-gray-900">{{ $category->articles_count ?? 0 }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Dibuat:</span>
                            <span class="font-medium text-gray-900">{{ $category->created_at->format('d M Y') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Terakhir Diupdate:</span>
                            <span class="font-medium text-gray-900">{{ $category->updated_at->format('d M Y') }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">ID:</span>
                            <span class="font-medium text-gray-900">#{{ $category->id }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-gray-200 mt-6">
                <a href="{{ route('admin.categories.index') }}" 
                   class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Kembali
                </a>
                <div class="flex items-center space-x-3">
                    <button type="button" 
                            onclick="if(confirm('Apakah Anda yakin ingin menghapus kategori ini?')) { document.getElementById('delete-form').submit(); }"
                            class="px-4 py-2 text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
                        <i class="fas fa-trash mr-2"></i>
                        Hapus
                    </button>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
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
