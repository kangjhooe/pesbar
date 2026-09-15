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
            <h3 class="text-lg font-semibold text-gray-900">Daftar Event</h3>
            <p class="text-sm text-gray-600">Total {{ $stats['total'] }} event</p>
        </div>
        <a href="{{ route('admin.events.create') }}"
           class="inline-flex items-center bg-news-accent text-white px-4 py-2 rounded-lg hover:bg-red-800 transition-colors">
            <i class="fas fa-plus mr-2"></i>
            Tambah Event
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i class="fas fa-calendar-alt text-blue-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Total Event</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Aktif</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['active'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Akan Datang</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['upcoming'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <i class="fas fa-calendar-day text-red-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['today'] }}</p>
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
            <tr class="hover:bg-slate-50/80 transition-colors">
                <x-admin.checkbox :value="$event->id" bulk-id="events-bulk" name="event_ids[]" />
                <x-admin.td-number :index="$events->firstItem() + $loop->index" />
                <td class="px-4 py-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                            @if($event->image)
                                <img class="h-10 w-10 rounded-lg object-cover" src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}">
                            @else
                                <div class="h-10 w-10 rounded-lg bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                    <i class="fas fa-calendar-alt text-white text-sm"></i>
                                </div>
                            @endif
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900 line-clamp-2" title="{{ $event->title }}">{{ $event->title }}</div>
                            <div class="text-sm text-gray-500 line-clamp-1">{{ $event->organizer }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ $event->formatted_date }}</div>
                    @if($event->start_time)
                        <div class="text-sm text-gray-500">{{ $event->formatted_start_time }}</div>
                    @endif
                </td>
                <td class="px-4 py-4">
                    <div class="text-sm text-gray-900">{{ Str::limit($event->location, 25) }}</div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ $event->event_type_label }}
                    </span>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    <div class="flex flex-col space-y-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $event->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
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
                <td colspan="8" class="px-4 py-12 text-center text-gray-500">
                    <i class="fas fa-calendar-alt text-4xl mb-4 text-gray-300"></i>
                    <p class="text-lg font-medium text-gray-700">Belum ada event</p>
                    <p class="text-sm">Mulai dengan membuat event pertama Anda</p>
                </td>
            </tr>
        @endforelse
    </x-admin.table>
</div>
@endsection
