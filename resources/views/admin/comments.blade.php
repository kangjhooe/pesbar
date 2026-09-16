@extends('layouts.admin-simple')

@section('title', 'Komentar - Admin Panel')
@section('page-title', 'Manajemen Komentar')
@section('page-subtitle', 'Kelola komentar dari pengunjung website')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-lg font-semibold text-news-ink">Daftar Komentar</h3>
            <p class="text-sm text-news-muted">Total {{ $comments->total() }} komentar</p>
        </div>
        <div class="flex gap-2">
            <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center">
                <i class="fas fa-check mr-2"></i>
                Setujui Semua
            </button>
            <button class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors flex items-center">
                <i class="fas fa-trash mr-2"></i>
                Hapus Spam
            </button>
        </div>
    </div>

    <!-- Comments Table -->
    <div class="bg-white rounded-xl shadow-sm border border-news-line overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-news-line">
                <thead class="bg-news-paper">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">
                            Komentar
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">
                            Artikel
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">
                            Penulis
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">
                            Tanggal
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-news-line">
                    @forelse($comments as $comment)
                    <tr class="hover:bg-news-paper">
                        <td class="px-6 py-4">
                            <div class="max-w-xs">
                                <div class="text-sm text-news-ink line-clamp-3">
                                    {{ $comment->content }}
                                </div>
                                @if($comment->parent_id)
                                    <div class="text-xs text-news-muted mt-1">
                                        <i class="fas fa-reply mr-1"></i>
                                        Balasan komentar
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-news-ink line-clamp-2" title="{{ $comment->article->title }}">
                                {{ $comment->article->title }}
                            </div>
                            <div class="text-xs text-news-muted">
                                <a href="{{ $comment->article->publicUrl() }}" 
                                   class="text-news-accent hover:text-news-ink">
                                    Lihat artikel
                                </a>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8">
                                    <div class="h-8 w-8 rounded-full bg-news-ink flex items-center justify-center">
                                        <span class="text-white text-xs font-medium">
                                            {{ strtoupper(substr($comment->name, 0, 1)) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-news-ink">
                                        {{ $comment->name }}
                                    </div>
                                    <div class="text-xs text-news-muted">
                                        {{ $comment->email }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($comment->is_approved)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Disetujui
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-clock mr-1"></i>
                                    Menunggu
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-news-muted">
                            {{ $comment->created_at->format('d-m-Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                @if(!$comment->is_approved)
                                    <button class="text-green-600 hover:text-green-900" title="Setujui">
                                        <i class="fas fa-check"></i>
                                    </button>
                                @endif
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
                                <i class="fas fa-comments text-4xl mb-4"></i>
                                <p class="text-lg font-medium">Belum ada komentar</p>
                                <p class="text-sm">Komentar dari pengunjung akan muncul di sini</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($comments->hasPages())
    <div class="flex justify-center">
        {{ $comments->links() }}
    </div>
    @endif
</div>
@endsection
