@extends('layouts.admin-simple')

@section('title', 'Manajemen Komentar - Admin Panel')
@section('page-title', 'Manajemen Komentar')
@section('page-subtitle', 'Kelola komentar artikel')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white border border-news-line p-6">
            <div class="flex items-center">
                <div class="p-3 bg-news-paper text-news-ink">
                    <i class="fas fa-comments text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-news-muted">Total Komentar</p>
                    <p class="text-2xl font-semibold text-news-ink">{{ $commentStats['total'] ?? $comments->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-news-line p-6">
            <div class="flex items-center">
                <div class="p-3 bg-news-paper text-news-ink">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-news-muted">Disetujui</p>
                    <p class="text-2xl font-semibold text-news-ink">{{ $commentStats['approved'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-news-line p-6">
            <div class="flex items-center">
                <div class="p-3 bg-news-paper text-news-ink">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-news-muted">Menunggu</p>
                    <p class="text-2xl font-semibold text-news-ink">{{ $commentStats['pending'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white border border-news-line p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @if(request('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif
            @if(request('direction'))
                <input type="hidden" name="direction" value="{{ request('direction') }}">
            @endif
            <div>
                <label class="block text-sm font-medium text-news-ink mb-1">Status</label>
                <select name="status" class="w-full border border-news-line rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent">
                    <option value="">Semua Status</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-news-ink mb-1">Pencarian</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari komentar..."
                       class="w-full border border-news-line rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-news-ink text-white px-4 py-2 rounded-lg hover:bg-news-accent transition-colors">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
            </div>
        </form>
    </div>

    <x-admin.table :paginator="$comments" :bulk="true" bulk-id="comments-bulk">
        <x-slot:bulkBar>
            <x-admin.bulk-bar
                :action="route('admin.comments.bulk')"
                bulk-id="comments-bulk"
                :options="['approve' => 'Setujui', 'reject' => 'Tolak', 'delete' => 'Hapus']"
            />
        </x-slot:bulkBar>

        <x-slot:head>
            <x-admin.checkbox all bulk-id="comments-bulk" />
            <x-admin.th :sortable="false" label="#" align="center" class="w-14" />
            <x-admin.th :sortable="false" label="Komentar" />
            <x-admin.th :sortable="false" label="Artikel" />
            <x-admin.th column="name" label="Penulis" />
            <x-admin.th column="is_approved" label="Status" />
            <x-admin.th column="created_at" label="Tanggal" />
            <x-admin.th :sortable="false" label="Aksi" align="right" />
        </x-slot:head>

        @forelse($comments as $comment)
            <tr class="hover:bg-news-paper transition-colors">
                <x-admin.checkbox :value="$comment->id" bulk-id="comments-bulk" name="ids[]" />
                <x-admin.td-number :index="$comments->firstItem() + $loop->index" />
                <td class="px-4 py-4">
                    <div class="text-sm text-news-ink max-w-xs">
                        {{ Str::limit($comment->comment, 100) }}
                    </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    <div class="text-sm text-news-ink max-w-xs line-clamp-2" title="{{ $comment->article->title ?? '—' }}">
                        {{ $comment->article->title ?? '—' }}
                    </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    <div class="text-sm text-news-ink">{{ $comment->name }}</div>
                    <div class="text-sm text-news-muted">{{ $comment->email }}</div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    @if($comment->is_approved)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-800">
                            <i class="fas fa-check-circle mr-1"></i>
                            Disetujui
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-800">
                            <i class="fas fa-clock mr-1"></i>
                            Menunggu
                        </span>
                    @endif
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-news-muted">
                    {{ $comment->created_at->format('d-m-Y H:i') }}
                </td>
                <x-admin.actions>
                    @if(!$comment->is_approved)
                        <x-admin.action-icon
                            :href="route('admin.comments.approve', $comment)"
                            method="POST"
                            icon="fas fa-check"
                            color="green"
                            title="Setujui"
                        />
                    @endif
                    @if($comment->is_approved)
                        <x-admin.action-icon
                            :href="route('admin.comments.reject', $comment)"
                            method="POST"
                            icon="fas fa-times"
                            color="amber"
                            title="Tolak"
                        />
                    @endif
                    <x-admin.action-icon
                        :href="route('admin.comments.destroy', $comment)"
                        method="DELETE"
                        icon="fas fa-trash"
                        color="red"
                        title="Hapus"
                        confirm="Apakah Anda yakin ingin menghapus komentar ini?"
                    />
                </x-admin.actions>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="px-4 py-12 text-center text-news-muted">
                    <i class="fas fa-comments text-4xl mb-4 text-news-muted"></i>
                    <p class="text-lg font-medium text-news-ink">Belum ada komentar</p>
                    <p class="text-sm">Komentar akan muncul di sini</p>
                </td>
            </tr>
        @endforelse
    </x-admin.table>
</div>
@endsection
