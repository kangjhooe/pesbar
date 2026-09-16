@extends('layouts.admin-simple')

@section('title', 'Manajemen Kategori - Admin Panel')
@section('page-title', 'Manajemen Kategori')
@section('page-subtitle', 'Kelola kategori artikel')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-lg font-semibold text-news-ink">Daftar Kategori</h3>
            <p class="text-sm text-news-muted">Total {{ $categories->total() }} kategori</p>
        </div>
        <button onclick="openModal('add-category-modal')"
                class="bg-news-accent text-white px-4 py-2 rounded-lg hover:bg-news-ink transition-colors flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Tambah Kategori
        </button>
    </div>

    <x-admin.table :paginator="$categories" :bulk="true" bulk-id="categories-bulk">
        <x-slot:bulkBar>
            <x-admin.bulk-bar
                :action="route('admin.categories.bulk')"
                bulk-id="categories-bulk"
                :options="['activate' => 'Aktifkan', 'deactivate' => 'Nonaktifkan', 'delete' => 'Hapus (artikel dialihkan)']"
            />
        </x-slot:bulkBar>

        <x-slot:head>
            <x-admin.checkbox all bulk-id="categories-bulk" />
            <x-admin.th :sortable="false" label="#" align="center" class="w-14" />
            <x-admin.th column="name" label="Kategori" />
            <x-admin.th :sortable="false" label="Deskripsi" />
            <x-admin.th column="articles_count" label="Jumlah Artikel" />
            <x-admin.th :sortable="false" label="Status" />
            <x-admin.th column="created_at" label="Dibuat" />
            <x-admin.th :sortable="false" label="Aksi" align="right" />
        </x-slot:head>

        @forelse($categories as $category)
            <tr class="hover:bg-news-paper transition-colors">
                <x-admin.checkbox :value="$category->id" bulk-id="categories-bulk" name="ids[]" />
                <x-admin.td-number :index="$categories->firstItem() + $loop->index" />
                <td class="px-4 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="w-3.5 h-3.5 rounded-full mr-3 ring-2 ring-white shadow" style="background-color: {{ $category->color ?? '#b91c1c' }}"></div>
                        <div class="text-sm font-medium text-news-ink">{{ $category->name }}</div>
                    </div>
                </td>
                <td class="px-4 py-4">
                    <div class="text-sm text-news-muted">{{ Str::limit($category->description, 50) ?: '—' }}</div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-news-paper text-news-accent border border-news-line">
                        {{ $category->articles_count }} artikel
                    </span>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    @if($category->is_active)
                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold bg-emerald-50 text-emerald-800">Aktif</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold bg-news-paper text-news-muted">Nonaktif</span>
                    @endif
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-news-muted">
                    {{ $category->created_at->format('d-m-Y') }}
                </td>
                <x-admin.actions>
                    <x-admin.action-icon :href="route('admin.categories.edit', $category)" icon="fas fa-edit" color="amber" title="Edit" />
                    <x-admin.action-icon
                        type="button"
                        icon="fas fa-pen"
                        color="blue"
                        title="Quick Edit"
                        onclick="editCategory({{ json_encode($category->slug) }}, {{ json_encode($category->name) }}, {{ json_encode($category->description) }}, {{ json_encode($category->color) }})"
                    />
                    <x-admin.action-icon
                        :href="route('admin.categories.destroy', $category)"
                        method="DELETE"
                        icon="fas fa-trash"
                        color="red"
                        title="Hapus"
                        confirm="Hapus kategori ini? Artikel terkait akan dialihkan ke kategori lain."
                    />
                </x-admin.actions>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="px-4 py-12 text-center text-news-muted">
                    <i class="fas fa-tags text-4xl mb-4 text-news-muted"></i>
                    <p class="text-lg font-medium text-news-ink">Belum ada kategori</p>
                    <p class="text-sm">Mulai buat kategori pertama Anda</p>
                </td>
            </tr>
        @endforelse
    </x-admin.table>
</div>

<!-- Add Category Modal -->
<div id="add-category-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white border-2 border-news-ink max-w-md w-full">
            <div class="px-6 py-4 border-b border-news-line">
                <h3 class="text-lg font-semibold text-news-ink">Tambah Kategori</h3>
            </div>
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-news-ink mb-1">Nama Kategori</label>
                        <input type="text" id="name" name="name" required value="{{ old('name') }}"
                               class="w-full border border-news-line rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent @error('name') border-red-500 @enderror">
                        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-medium text-news-ink mb-1">Deskripsi</label>
                        <textarea id="description" name="description" rows="3"
                                  class="w-full border border-news-line rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent">{{ old('description') }}</textarea>
                    </div>
                    <div>
                        <label for="color" class="block text-sm font-medium text-news-ink mb-1">Warna</label>
                        <input type="color" id="color" name="color" value="{{ old('color', '#b91c1c') }}"
                               class="w-full border border-news-line rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent">
                    </div>
                    <label class="flex items-center gap-2 text-sm text-news-ink">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                               class="rounded border-news-line text-news-accent focus:ring-news-accent/30">
                        Kategori aktif (tampil di publik)
                    </label>
                </div>
                <div class="px-6 py-4 border-t border-news-line flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('add-category-modal')"
                            class="px-4 py-2 border border-news-line text-news-ink hover:border-news-ink hover:bg-white transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-news-accent text-white rounded-lg hover:bg-news-ink transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Category Modal -->
<div id="edit-category-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white border-2 border-news-ink max-w-md w-full">
            <div class="px-6 py-4 border-b border-news-line">
                <h3 class="text-lg font-semibold text-news-ink">Edit Kategori</h3>
            </div>
            <form id="edit-category-form" method="POST">
                @csrf
                @method('PUT')
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label for="edit_name" class="block text-sm font-medium text-news-ink mb-1">Nama Kategori</label>
                        <input type="text" id="edit_name" name="name" required
                               class="w-full border border-news-line rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent">
                    </div>
                    <div>
                        <label for="edit_description" class="block text-sm font-medium text-news-ink mb-1">Deskripsi</label>
                        <textarea id="edit_description" name="description" rows="3"
                                  class="w-full border border-news-line rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent"></textarea>
                    </div>
                    <div>
                        <label for="edit_color" class="block text-sm font-medium text-news-ink mb-1">Warna</label>
                        <input type="color" id="edit_color" name="color"
                               class="w-full border border-news-line rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent">
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-news-line flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('edit-category-modal')"
                            class="px-4 py-2 border border-news-line text-news-ink hover:border-news-ink hover:bg-white transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-news-accent text-white rounded-lg hover:bg-news-ink transition-colors">
                        Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
}
function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}
function editCategory(slug, name, description, color) {
    document.getElementById('edit-category-form').action = `/admin/categories/${slug}`;
    document.getElementById('edit_name').value = name || '';
    document.getElementById('edit_description').value = description || '';
    document.getElementById('edit_color').value = color || '#b91c1c';
    openModal('edit-category-modal');
}
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('bg-black')) {
        e.target.classList.add('hidden');
    }
});
@if($errors->any() && old('name') !== null && !old('_method'))
openModal('add-category-modal');
@endif
</script>
@endpush
@endsection
