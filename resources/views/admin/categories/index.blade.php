@extends('layouts.admin-simple')

@section('title', 'Manajemen Kategori - Admin Panel')
@section('page-title', 'Manajemen Kategori')
@section('page-subtitle', 'Kelola kategori artikel')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Daftar Kategori</h3>
            <p class="text-sm text-gray-600">Total {{ $categories->total() }} kategori</p>
        </div>
        <button onclick="openModal('add-category-modal')"
                class="bg-news-accent text-white px-4 py-2 rounded-lg hover:bg-red-800 transition-colors flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Tambah Kategori
        </button>
    </div>

    <x-admin.table :paginator="$categories" :bulk="true" bulk-id="categories-bulk">
        <x-slot:bulkBar>
            <x-admin.bulk-bar
                :action="route('admin.categories.bulk')"
                bulk-id="categories-bulk"
                :options="['delete' => 'Hapus (artikel dialihkan)']"
            />
        </x-slot:bulkBar>

        <x-slot:head>
            <x-admin.checkbox all bulk-id="categories-bulk" />
            <x-admin.th :sortable="false" label="#" align="center" class="w-14" />
            <x-admin.th column="name" label="Kategori" />
            <x-admin.th :sortable="false" label="Deskripsi" />
            <x-admin.th column="articles_count" label="Jumlah Artikel" />
            <x-admin.th column="created_at" label="Dibuat" />
            <x-admin.th :sortable="false" label="Aksi" align="right" />
        </x-slot:head>

        @forelse($categories as $category)
            <tr class="hover:bg-slate-50/80 transition-colors">
                <x-admin.checkbox :value="$category->id" bulk-id="categories-bulk" name="ids[]" />
                <x-admin.td-number :index="$categories->firstItem() + $loop->index" />
                <td class="px-4 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="w-3.5 h-3.5 rounded-full mr-3 ring-2 ring-white shadow" style="background-color: {{ $category->color ?? '#3b82f6' }}"></div>
                        <div class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
                    </div>
                </td>
                <td class="px-4 py-4">
                    <div class="text-sm text-gray-600">{{ Str::limit($category->description, 50) ?: '—' }}</div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-news-accent border border-blue-100">
                        {{ $category->articles_count }} artikel
                    </span>
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
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
                <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                    <i class="fas fa-tags text-4xl mb-4 text-gray-300"></i>
                    <p class="text-lg font-medium text-gray-700">Belum ada kategori</p>
                    <p class="text-sm">Mulai buat kategori pertama Anda</p>
                </td>
            </tr>
        @endforelse
    </x-admin.table>
</div>

<!-- Add Category Modal -->
<div id="add-category-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Tambah Kategori</h3>
            </div>
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
                        <input type="text" id="name" name="name" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent">
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea id="description" name="description" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent"></textarea>
                    </div>
                    <div>
                        <label for="color" class="block text-sm font-medium text-gray-700 mb-1">Warna</label>
                        <input type="color" id="color" name="color" value="#3b82f6"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent">
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('add-category-modal')"
                            class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-news-accent text-white rounded-lg hover:bg-red-800 transition-colors">
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
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Edit Kategori</h3>
            </div>
            <form id="edit-category-form" method="POST">
                @csrf
                @method('PUT')
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label for="edit_name" class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
                        <input type="text" id="edit_name" name="name" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent">
                    </div>
                    <div>
                        <label for="edit_description" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                        <textarea id="edit_description" name="description" rows="3"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent"></textarea>
                    </div>
                    <div>
                        <label for="edit_color" class="block text-sm font-medium text-gray-700 mb-1">Warna</label>
                        <input type="color" id="edit_color" name="color"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent">
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('edit-category-modal')"
                            class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-news-accent text-white rounded-lg hover:bg-red-800 transition-colors">
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
    document.getElementById('edit_color').value = color || '#3b82f6';
    openModal('edit-category-modal');
}
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('bg-black')) {
        e.target.classList.add('hidden');
    }
});
</script>
@endpush
@endsection
