@extends('layouts.admin-simple')

@section('title', 'Manajemen Event')
@section('page-title', 'Manajemen Event')
@section('page-subtitle', 'Agenda di widget beranda dan halaman /agenda')

@section('content')
<div class="space-y-6">
    @include('admin.partials.widget-placement-note', [
        'items' => [
            'Sidebar beranda — widget Agenda Kegiatan',
            'Halaman publik /agenda (daftar lengkap)',
        ],
    ])

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h3 class="text-lg font-semibold text-news-ink">Daftar Event</h3>
            <p class="text-sm text-news-muted">Total {{ $stats['total'] }} event</p>
        </div>
        <a href="{{ route('admin.events.create') }}"
           class="inline-flex items-center bg-news-accent text-white px-4 py-2 rounded-lg hover:bg-news-ink transition-colors">
            <i class="fas fa-plus mr-2"></i>
            Tambah Event
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-news-line p-4">
            <div class="flex items-center">
                <div class="p-2 bg-news-paper">
                    <i class="fas fa-calendar-alt text-news-accent"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-news-muted">Total Event</p>
                    <p class="text-2xl font-bold text-news-ink">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-news-line p-4">
            <div class="flex items-center">
                <div class="p-2 bg-news-paper">
                    <i class="fas fa-check-circle text-news-ink"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-news-muted">Aktif</p>
                    <p class="text-2xl font-bold text-news-ink">{{ $stats['active'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-news-line p-4">
            <div class="flex items-center">
                <div class="p-2 bg-news-paper">
                    <i class="fas fa-clock text-news-ink"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-news-muted">Akan Datang</p>
                    <p class="text-2xl font-bold text-news-ink">{{ $stats['upcoming'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-news-line p-4">
            <div class="flex items-center">
                <div class="p-2 bg-news-paper">
                    <i class="fas fa-calendar-day text-news-ink"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-news-muted">Hari Ini</p>
                    <p class="text-2xl font-bold text-news-ink">{{ $stats['today'] }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Bulk uses event_ids[] — matches EventController@bulkAction --}}
    <x-admin.table :paginator="$events" :bulk="true" bulk-id="events-bulk">
        <x-slot:bulkBar>
            <x-admin.bulk-bar
                :action="route('admin.events.bulk-action')"
                bulk-id="events-bulk"
                :options="['activate' => 'Aktifkan', 'deactivate' => 'Nonaktifkan', 'delete' => 'Hapus']"
            />
        </x-slot:bulkBar>

        <x-slot:head>
            <x-admin.checkbox all bulk-id="events-bulk" />
            <x-admin.th :sortable="false" label="#" align="center" class="w-14" />
            <x-admin.th column="title" label="Event" />
            <x-admin.th column="event_date" label="Tanggal" />
            <x-admin.th column="location" label="Lokasi" />
            <x-admin.th column="event_type" label="Tipe" />
            <x-admin.th column="is_active" label="Status" />
            <x-admin.th :sortable="false" label="Aksi" align="right" />
        </x-slot:head>

        @forelse($events as $event)
            <tr class="hover:bg-news-paper transition-colors">
                <x-admin.checkbox :value="$event->id" bulk-id="events-bulk" name="event_ids[]" />
                <x-admin.td-number :index="$events->firstItem() + $loop->index" />
                <td class="px-4 py-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                            @if($event->image)
                                <img class="h-10 w-10 rounded-lg object-cover" src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}">
                            @else
                                <div class="h-10 w-10 bg-news-ink flex items-center justify-center">
                                    <i class="fas fa-calendar-alt text-white text-sm"></i>
                                </div>
                            @endif
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-news-ink line-clamp-2" title="{{ $event->title }}">{{ $event->title }}</div>
                            <div class="text-sm text-news-muted line-clamp-1">{{ $event->organizer }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    <div class="text-sm text-news-ink">{{ $event->formatted_date }}</div>
                    @if($event->start_time)
                        <div class="text-sm text-news-muted">{{ $event->formatted_start_time }}</div>
                    @endif
                </td>
                <td class="px-4 py-4">
                    <div class="text-sm text-news-ink">{{ Str::limit($event->location, 25) }}</div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-news-paper text-news-ink">
                        {{ $event->event_type_label }}
                    </span>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    <div class="flex flex-col space-y-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $event->is_active ? 'bg-emerald-50 text-emerald-800' : 'bg-red-50 text-news-accent' }}">
                            {{ $event->is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $event->priority_color }}-100 text-{{ $event->priority_color }}-800">
                            {{ $event->priority_label }}
                        </span>
                    </div>
                </td>
                <x-admin.actions>
                    <x-admin.action-icon :href="route('admin.events.show', $event)" icon="fas fa-eye" color="blue" title="Lihat" />
                    <x-admin.action-icon :href="route('admin.events.edit', $event)" icon="fas fa-edit" color="indigo" title="Edit" />
                    <x-admin.action-icon
                        :href="route('admin.events.toggle-status', $event)"
                        method="POST"
                        :icon="$event->is_active ? 'fas fa-pause' : 'fas fa-play'"
                        :color="$event->is_active ? 'amber' : 'green'"
                        :title="$event->is_active ? 'Nonaktifkan' : 'Aktifkan'"
                    />
                    <x-admin.action-icon
                        :href="route('admin.events.destroy', $event)"
                        method="DELETE"
                        icon="fas fa-trash"
                        color="red"
                        title="Hapus"
                        confirm="Apakah Anda yakin ingin menghapus event ini?"
                    />
                </x-admin.actions>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="px-4 py-12 text-center text-news-muted">
                    <i class="fas fa-calendar-alt text-4xl mb-4 text-news-muted"></i>
                    <p class="text-lg font-medium text-news-ink">Belum ada event</p>
                    <p class="text-sm">Mulai dengan membuat event pertama Anda</p>
                </td>
            </tr>
        @endforelse
    </x-admin.table>
</div>
@endsection
