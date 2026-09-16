@extends('layouts.admin-simple')

@section('title', 'Event Popup Management')
@section('page-title', 'Event Popup Management')
@section('page-subtitle', 'Popup di semua halaman publik saat tanggal aktif')

@section('content')
<div class="space-y-6">
    @include('admin.partials.widget-placement-note', [
        'items' => [
            'Semua halaman publik (layout public) — muncul otomatis jika aktif & dalam rentang tanggal',
            'Pengunjung bisa menutup (dismiss); tidak hanya di beranda',
        ],
    ])

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-lg font-semibold text-news-ink">Daftar Event Popup</h3>
            <p class="text-sm text-news-muted">Total {{ $eventPopups->total() }} popup</p>
        </div>
        <a href="{{ route('admin.event-popups.create') }}"
           class="inline-flex items-center justify-center bg-news-accent text-white px-4 py-2 rounded-lg hover:bg-news-ink transition-colors">
            <i class="fas fa-plus mr-2"></i>Tambah Event Popup
        </a>
    </div>

    <x-admin.table :paginator="$eventPopups" :bulk="true" bulk-id="event-popups-bulk">
        <x-slot:bulkBar>
            <x-admin.bulk-bar
                :action="route('admin.event-popups.bulk')"
                bulk-id="event-popups-bulk"
                :options="['activate' => 'Aktifkan', 'deactivate' => 'Nonaktifkan', 'delete' => 'Hapus']"
            />
        </x-slot:bulkBar>

        <x-slot:head>
            <x-admin.checkbox all bulk-id="event-popups-bulk" />
            <x-admin.th :sortable="false" label="#" align="center" class="w-14" />
            <x-admin.th column="title" label="Judul" />
            <x-admin.th column="start_date" label="Tanggal Mulai" />
            <x-admin.th column="end_date" label="Tanggal Selesai" />
            <x-admin.th column="status" label="Status" />
            <x-admin.th :sortable="false" label="Aksi" align="right" />
        </x-slot:head>

        @forelse($eventPopups as $popup)
            <tr class="hover:bg-news-paper transition-colors">
                <x-admin.checkbox :value="$popup->id" bulk-id="event-popups-bulk" name="ids[]" />
                <x-admin.td-number :index="$eventPopups->firstItem() + $loop->index" />
                <td class="px-4 py-4">
                    <div class="text-sm font-medium text-news-ink line-clamp-2" title="{{ $popup->title }}">{{ $popup->title }}</div>
                    <div class="text-sm text-news-muted line-clamp-1 max-w-xs">{{ $popup->message }}</div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-news-ink">
                    {{ $popup->start_date->format('d-m-Y') }}
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-news-ink">
                    {{ $popup->end_date->format('d-m-Y') }}
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    @if($popup->status)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-800">
                            Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            Non-Aktif
                        </span>
                    @endif
                </td>
                <x-admin.actions>
                    <x-admin.action-icon
                        :href="route('admin.event-popups.edit', $popup)"
                        icon="fas fa-edit"
                        color="indigo"
                        title="Edit"
                    />
                    <x-admin.action-icon
                        :href="route('admin.event-popups.toggle-status', $popup)"
                        method="PATCH"
                        :icon="$popup->status ? 'fas fa-toggle-on' : 'fas fa-toggle-off'"
                        color="amber"
                        :title="$popup->status ? 'Nonaktifkan' : 'Aktifkan'"
                    />
                    <x-admin.action-icon
                        :href="route('admin.event-popups.destroy', $popup)"
                        method="DELETE"
                        icon="fas fa-trash"
                        color="red"
                        title="Hapus"
                        confirm="Apakah Anda yakin ingin menghapus event popup ini?"
                    />
                </x-admin.actions>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-4 py-12 text-center text-news-muted">
                    <i class="fas fa-bell text-4xl mb-4 text-news-muted"></i>
                    <p class="text-lg font-medium text-news-ink">Belum ada Event Popup</p>
                    <p class="text-sm mb-4">Mulai dengan membuat event popup pertama Anda.</p>
                    <a href="{{ route('admin.event-popups.create') }}"
                       class="inline-flex items-center bg-news-accent text-white px-4 py-2 rounded-lg hover:bg-news-ink transition-colors">
                        <i class="fas fa-plus mr-2"></i>Tambah Event Popup
                    </a>
                </td>
            </tr>
        @endforelse
    </x-admin.table>
</div>
@endsection
