@extends('layouts.admin-simple')

@section('title', 'Banding Ban')
@section('page-title', 'Banding Ban')
@section('page-subtitle', 'Tinjau banding pengguna yang sedang dibanned')

@section('content')
<div class="space-y-6">
    <x-admin.table :paginator="$appeals">
        <x-slot:head>
            <x-admin.th :sortable="false" label="#" align="center" class="w-14" />
            <x-admin.th column="name" label="Pengguna" />
            <x-admin.th column="content_warning_count" label="Sanksi ke-" />
            <x-admin.th column="banned_until" label="Ban sampai" />
            <x-admin.th :sortable="false" label="Alasan banding" />
            <x-admin.th column="ban_appeal_at" label="Diajukan" />
            <x-admin.th :sortable="false" label="Aksi" align="right" />
        </x-slot:head>

        @forelse($appeals as $user)
            <tr class="hover:bg-news-paper transition-colors">
                <x-admin.td-number :index="$appeals->firstItem() + $loop->index" />
                <td class="px-4 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-news-ink">{{ $user->name }}</div>
                    <div class="text-sm text-news-muted">{{ $user->email }}</div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-news-ink">
                    #{{ $user->content_warning_count }}
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-news-ink">
                    {{ $user->banned_until?->format('d M Y, H:i') }}
                </td>
                <td class="px-4 py-4 text-sm text-news-ink max-w-md">
                    {{ $user->ban_appeal_message }}
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-news-muted">
                    {{ $user->ban_appeal_at?->format('d M Y, H:i') }}
                </td>
                <x-admin.actions>
                    <x-admin.action-icon
                        :href="route('admin.ban-appeals.approve', $user)"
                        method="POST"
                        icon="fas fa-check"
                        color="green"
                        title="Setujui banding (buka ban)"
                        confirm="Setujui banding dan buka ban {{ $user->name }}?"
                    />
                    <x-admin.action-icon
                        :href="route('admin.ban-appeals.reject', $user)"
                        method="POST"
                        icon="fas fa-times"
                        color="red"
                        title="Tolak banding"
                        confirm="Tolak banding {{ $user->name }}? Masa ban tetap berjalan."
                    />
                    <x-admin.action-icon
                        :href="route('admin.users.lift-ban', $user)"
                        method="POST"
                        icon="fas fa-unlock"
                        color="indigo"
                        title="Buka ban langsung"
                        confirm="Buka ban {{ $user->name }} sekarang?"
                    />
                </x-admin.actions>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-4 py-12 text-center text-news-muted">
                    <i class="fas fa-gavel text-4xl mb-4 text-news-muted"></i>
                    <p class="text-lg font-medium text-news-ink">Tidak ada banding pending</p>
                </td>
            </tr>
        @endforelse
    </x-admin.table>
</div>
@endsection
