@extends('layouts.admin-simple')

@section('title', 'Manajemen Polling')
@section('page-title', 'Manajemen Polling')
@section('page-subtitle', 'Polling di sidebar beranda (dan artikel)')

@section('content')
<div class="space-y-6">
    @include('admin.partials.widget-placement-note', [
        'items' => [
            'Sidebar beranda — setelah widget agenda',
            'Sidebar halaman artikel (versi ringkas)',
        ],
    ])

    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h3 class="text-lg font-semibold text-news-ink">Daftar Polling</h3>
            <p class="text-sm text-news-muted">Total {{ $stats['total'] }} polling</p>
        </div>
        <a href="{{ route('admin.polls.create') }}"
           class="inline-flex items-center px-4 py-2 bg-news-accent text-white font-medium rounded-lg hover:bg-news-ink transition-colors">
            <i class="fas fa-plus mr-2"></i>
            Tambah Polling
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-news-line p-4">
            <div class="flex items-center">
                <div class="p-2 bg-news-paper">
                    <i class="fas fa-poll text-news-accent"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-news-muted">Total Polling</p>
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
                    <i class="fas fa-play text-news-ink"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-news-muted">Berlangsung</p>
                    <p class="text-2xl font-bold text-news-ink">{{ $stats['running'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-news-line p-4">
            <div class="flex items-center">
                <div class="p-2 bg-news-paper">
                    <i class="fas fa-stop text-news-ink"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-news-muted">Selesai</p>
                    <p class="text-2xl font-bold text-news-ink">{{ $stats['finished'] }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Bulk uses poll_ids[] — matches PollController@bulkAction --}}
    <x-admin.table :paginator="$polls" :bulk="true" bulk-id="polls-bulk">
        <x-slot:bulkBar>
            <x-admin.bulk-bar
                :action="route('admin.polls.bulk-action')"
                bulk-id="polls-bulk"
                :options="['activate' => 'Aktifkan', 'deactivate' => 'Nonaktifkan', 'reset_votes' => 'Reset Suara', 'delete' => 'Hapus']"
            />
        </x-slot:bulkBar>

        <x-slot:head>
            <x-admin.checkbox all bulk-id="polls-bulk" />
            <x-admin.th :sortable="false" label="#" align="center" class="w-14" />
            <x-admin.th column="title" label="Polling" />
            <x-admin.th column="poll_type" label="Tipe" />
            <x-admin.th :sortable="false" label="Suara" />
            <x-admin.th column="start_date" label="Periode" />
            <x-admin.th column="is_active" label="Status" />
            <x-admin.th :sortable="false" label="Aksi" align="right" />
        </x-slot:head>

        @forelse($polls as $poll)
            <tr class="hover:bg-news-paper transition-colors">
                <x-admin.checkbox :value="$poll->id" bulk-id="polls-bulk" name="poll_ids[]" />
                <x-admin.td-number :index="$polls->firstItem() + $loop->index" />
                <td class="px-4 py-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-10 w-10">
                            <div class="h-10 w-10 rounded-lg bg-news-paper flex items-center justify-center">
                                <i class="fas fa-poll text-news-accent"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-news-ink line-clamp-2" title="{{ $poll->title }}">{{ $poll->title }}</div>
                            <div class="text-sm text-news-muted">{{ $poll->options->count() }} pilihan</div>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-news-paper text-news-ink">
                        {{ $poll->poll_type_label }}
                    </span>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-news-ink">{{ number_format($poll->total_votes) }}</div>
                    <div class="text-xs text-news-muted">suara</div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    <div class="text-sm text-news-ink">
                        @if($poll->start_date)
                            {{ $poll->formatted_start_date }}
                        @else
                            <span class="text-news-muted">—</span>
                        @endif
                    </div>
                    <div class="text-xs text-news-muted">
                        @if($poll->end_date)
                            s/d {{ $poll->formatted_end_date }}
                        @else
                            Tidak terbatas
                        @endif
                    </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    <div class="flex flex-col space-y-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $poll->is_active ? 'bg-emerald-50 text-emerald-800' : 'bg-news-paper text-news-ink' }}">
                            {{ $poll->is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                        @php
                            $statusColors = [
                                'inactive' => 'bg-news-paper text-news-muted',
                                'upcoming' => 'bg-amber-50 text-amber-800',
                                'running' => 'bg-emerald-50 text-emerald-800',
                                'finished' => 'bg-red-50 text-news-accent'
                            ];
                            $statusColor = $statusColors[$poll->status] ?? 'bg-news-paper text-news-ink';
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                            {{ $poll->status_label }}
                        </span>
                    </div>
                </td>
                <x-admin.actions>
                    <x-admin.action-icon :href="route('admin.polls.show', $poll)" icon="fas fa-eye" color="blue" title="Lihat" />
                    <x-admin.action-icon :href="route('admin.polls.edit', $poll)" icon="fas fa-edit" color="indigo" title="Edit" />
                    <x-admin.action-icon
                        :href="route('admin.polls.toggle-status', $poll)"
                        method="POST"
                        :icon="$poll->is_active ? 'fas fa-pause' : 'fas fa-play'"
                        :color="$poll->is_active ? 'amber' : 'green'"
                        :title="$poll->is_active ? 'Nonaktifkan' : 'Aktifkan'"
                    />
                    <x-admin.action-icon
                        :href="route('admin.polls.reset-votes', $poll)"
                        method="POST"
                        icon="fas fa-undo"
                        color="orange"
                        title="Reset Suara"
                        confirm="Apakah Anda yakin ingin mereset suara polling ini?"
                    />
                    <x-admin.action-icon
                        :href="route('admin.polls.destroy', $poll)"
                        method="DELETE"
                        icon="fas fa-trash"
                        color="red"
                        title="Hapus"
                        confirm="Apakah Anda yakin ingin menghapus polling ini?"
                    />
                </x-admin.actions>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="px-4 py-12 text-center text-news-muted">
                    <i class="fas fa-poll text-4xl mb-4 text-news-muted"></i>
                    <p class="text-lg font-medium text-news-ink">Belum ada polling</p>
                    <p class="text-sm mb-4">Mulai dengan membuat polling pertama Anda</p>
                    <a href="{{ route('admin.polls.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-news-accent text-white font-medium rounded-lg hover:bg-news-ink transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        Buat Polling Baru
                    </a>
                </td>
            </tr>
        @endforelse
    </x-admin.table>
</div>
@endsection
