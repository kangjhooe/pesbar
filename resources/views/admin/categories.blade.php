@extends('layouts.admin-simple')

@section('title', 'Kategori - Admin Panel')
@section('page-title', 'Manajemen Kategori')
@section('page-subtitle', 'Kelola kategori artikel dan konten')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-lg font-semibold text-news-ink">Daftar Kategori</h3>
            <p class="text-sm text-news-muted">Total {{ $categories->total() }} kategori</p>
        </div>
        <div class="flex gap-2">
            <button class="bg-news-accent text-white px-4 py-2 rounded-lg hover:bg-news-ink transition-colors flex items-center">
                <i class="fas fa-plus mr-2"></i>
                Tambah Kategori
            </button>
        </div>
    </div>

    <!-- Categories Table -->
    <div class="bg-white rounded-xl shadow-sm border border-news-line overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-news-line">
                <thead class="bg-news-paper">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">
                            Nama Kategori
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">
                            Deskripsi
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">
                            Jumlah Artikel
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">
                            Dibuat
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-news-line">
                    @forelse($categories as $category)
                    <tr class="hover:bg-news-paper">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-lg bg-news-ink flex items-center justify-center">
                                        <i class="fas fa-folder text-white"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-news-ink">
                                        {{ $category->name }}
                                    </div>
                                    <div class="text-sm text-news-muted">
                                        Slug: {{ $category->slug }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-news-ink">
                                {{ Str::limit($category->description, 50) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-news-paper text-news-ink">
                                {{ $category->articles_count }} artikel
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($category->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i>
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-news-muted">
                            {{ $category->created_at->format('d-m-Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('categories.show', $category) }}" 
                                   class="text-news-accent hover:text-news-ink" 
                                   title="Lihat">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="text-red-600 hover:text-red-900" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="text-news-muted">
                                <i class="fas fa-folder text-4xl mb-4"></i>
                                <p class="text-lg font-medium">Belum ada kategori</p>
                                <p class="text-sm">Mulai buat kategori pertama Anda</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($categories->hasPages())
    <div class="flex justify-center">
        {{ $categories->links() }}
    </div>
    @endif
</div>
@endsection
