@extends('layouts.admin-simple')

@section('title', 'Manajemen Komentar - Admin Panel')
@section('page-title', 'Manajemen Komentar')
@section('page-subtitle', 'Kelola komentar artikel')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-comments text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Komentar</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $comments->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Disetujui</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $comments->where('is_approved', true)->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Menunggu</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $comments->where('is_approved', false)->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @if(request('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif
            @if(request('direction'))
                <input type="hidden" name="direction" value="{{ request('direction') }}">
            @endif
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent">
                    <option value="">Semua Status</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Disetujui</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari komentar..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
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
            <tr class="hover:bg-slate-50/80 transition-colors">
                <x-admin.checkbox :value="$comment->id" bulk-id="comments-bulk" name="ids[]" />
                <x-admin.td-number :index="$comments->firstItem() + $loop->index" />
                <td class="px-4 py-4">
                    <div class="text-sm text-gray-900 max-w-xs">
                        {{ Str::limit($comment->content, 100) }}
                    </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900 max-w-xs line-clamp-2" title="{{ $comment->article->title ?? '—' }}">
                        {{ $comment->article->title ?? '—' }}
                    </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ $comment->name }}</div>
                    <div class="text-sm text-gray-500">{{ $comment->email }}</div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
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
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
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
                <td colspan="8" class="px-4 py-12 text-center text-gray-500">
                    <i class="fas fa-comments text-4xl mb-4 text-gray-300"></i>
                    <p class="text-lg font-medium text-gray-700">Belum ada komentar</p>
                    <p class="text-sm">Komentar akan muncul di sini</p>
                </td>
            </tr>
        @endforelse
    </x-admin.table>
</div>
@endsection
