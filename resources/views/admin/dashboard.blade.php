@extends('layouts.admin-simple')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Halo, ' . Auth::user()->name . ' · ' . now()->translatedFormat('l, d F Y'))

@section('content')
<div class="space-y-6">
    {{-- Action queue --}}
    <section>
        <div class="flex items-end justify-between mb-3">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Perlu tindakan</h3>
                <p class="text-xs text-gray-500 mt-0.5">Antrian yang menunggu review Anda</p>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <a href="{{ route('admin.articles.pending') }}"
               class="group flex items-center justify-between rounded-xl border border-gray-200 bg-white px-4 py-4 hover:border-amber-300 hover:bg-amber-50/40 transition-colors">
                <div>
                    <p class="text-xs font-medium text-gray-500">Artikel review</p>
                    <p class="mt-1 text-2xl font-semibold tabular-nums text-gray-900">{{ $stats['pending_articles'] }}</p>
                </div>
                <span class="inline-flex items-center text-xs font-medium text-amber-700 opacity-0 group-hover:opacity-100 transition-opacity">
                    Review <i class="fas fa-arrow-right ml-1.5 text-[10px]"></i>
                </span>
            </a>

            <a href="{{ route('admin.comments.index') }}"
               class="group flex items-center justify-between rounded-xl border border-gray-200 bg-white px-4 py-4 hover:border-rose-300 hover:bg-rose-50/40 transition-colors">
                <div>
                    <p class="text-xs font-medium text-gray-500">Komentar pending</p>
                    <p class="mt-1 text-2xl font-semibold tabular-nums text-gray-900">{{ $stats['pending_comments'] }}</p>
                </div>
                <span class="inline-flex items-center text-xs font-medium text-rose-700 opacity-0 group-hover:opacity-100 transition-opacity">
                    Moderasi <i class="fas fa-arrow-right ml-1.5 text-[10px]"></i>
                </span>
            </a>

            <a href="{{ route('admin.verification-requests') }}"
               class="group flex items-center justify-between rounded-xl border border-gray-200 bg-white px-4 py-4 hover:border-sky-300 hover:bg-sky-50/40 transition-colors">
                <div>
                    <p class="text-xs font-medium text-gray-500">Upgrade / verifikasi</p>
                    <p class="mt-1 text-2xl font-semibold tabular-nums text-gray-900">{{ $stats['pending_verification_requests'] }}</p>
                </div>
                <span class="inline-flex items-center text-xs font-medium text-sky-700 opacity-0 group-hover:opacity-100 transition-opacity">
                    Tinjau <i class="fas fa-arrow-right ml-1.5 text-[10px]"></i>
                </span>
            </a>
        </div>
    </section>

    {{-- Today + quick actions --}}
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 rounded-xl border border-gray-200 bg-white p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Hari ini</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div>
                    <p class="text-xs text-gray-500">Artikel baru</p>
                    <p class="mt-1 text-xl font-semibold tabular-nums text-gray-900">{{ $stats['articles_today'] }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Komentar baru</p>
                    <p class="mt-1 text-xl font-semibold tabular-nums text-gray-900">{{ $stats['comments_today'] }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Pembacaan user</p>
                    <p class="mt-1 text-xl font-semibold tabular-nums text-gray-900">{{ number_format($stats['reads_today']) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Total views</p>
                    <p class="mt-1 text-xl font-semibold tabular-nums text-gray-900">{{ number_format($stats['total_views']) }}</p>
                </div>
            </div>
            <div class="mt-5 pt-4 border-t border-gray-100 grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
                <div>
                    <p class="text-xs text-gray-500">Users</p>
                    <p class="font-medium text-gray-800 tabular-nums">{{ number_format($stats['total_users']) }} <span class="text-xs font-normal text-emerald-600">+{{ $monthlyStats['users_this_month'] ?? 0 }}</span></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Penulis</p>
                    <p class="font-medium text-gray-800 tabular-nums">{{ number_format($stats['total_penulis']) }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Artikel</p>
                    <p class="font-medium text-gray-800 tabular-nums">{{ number_format($stats['total_articles']) }} <span class="text-xs font-normal text-emerald-600">+{{ $monthlyStats['articles_this_month'] ?? 0 }}</span></p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Subscriber</p>
                    <p class="font-medium text-gray-800 tabular-nums">{{ number_format($stats['newsletter_subscribers']) }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Aksi cepat</h3>
            <div class="space-y-2">
                <a href="{{ route('admin.articles.create') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-gray-100 text-gray-600">
                        <i class="fas fa-plus text-xs"></i>
                    </span>
                    Buat artikel
                </a>
                <a href="{{ route('admin.articles.pending') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 text-amber-700">
                        <i class="fas fa-clock text-xs"></i>
                    </span>
                    Review artikel
                </a>
                <a href="{{ route('admin.comments.index') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-rose-50 text-rose-700">
                        <i class="fas fa-comments text-xs"></i>
                    </span>
                    Moderasi komentar
                </a>
                <a href="{{ route('admin.verification-requests') }}"
                   class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-sky-50 text-sky-700">
                        <i class="fas fa-user-check text-xs"></i>
                    </span>
                    Verifikasi & upgrade
                </a>
            </div>
        </div>
    </section>

    {{-- Chart + activity --}}
    <section class="grid grid-cols-1 lg:grid-cols-5 gap-4">
        <div class="lg:col-span-3 rounded-xl border border-gray-200 bg-white p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-semibold text-gray-900">Aktivitas 7 hari</h3>
                <a href="{{ route('admin.analytics.index') }}" class="text-xs font-medium text-blue-600 hover:text-news-accent">Analitik</a>
            </div>
            <div class="h-56">
                <canvas id="adminActivityChart"></canvas>
            </div>
        </div>

        <div class="lg:col-span-2 rounded-xl border border-gray-200 bg-white p-5">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Aktivitas terbaru</h3>
            <div class="space-y-1 max-h-56 overflow-y-auto">
                @forelse($recent_activity->take(8) as $activity)
                    @php
                        $dot = match($activity['color'] ?? 'gray') {
                            'green' => 'bg-emerald-500',
                            'yellow' => 'bg-amber-500',
                            'blue' => 'bg-sky-500',
                            'red' => 'bg-rose-500',
                            default => 'bg-gray-400',
                        };
                    @endphp
                    <div class="flex gap-3 rounded-lg px-2 py-2.5 hover:bg-gray-50">
                        <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full {{ $dot }}"></span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm text-gray-900 truncate">
                                <span class="font-medium">{{ $activity['user'] }}</span>
                                <span class="text-gray-500"> {{ $activity['action'] }}</span>
                            </p>
                            @if(!empty($activity['title']))
                                <p class="text-xs text-gray-500 truncate mt-0.5">{{ $activity['title'] }}</p>
                            @endif
                            <p class="text-[11px] text-gray-400 mt-0.5">
                                {{ $activity['time'] ? \Carbon\Carbon::parse($activity['time'])->diffForHumans() : 'Baru saja' }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="py-10 text-center">
                        <p class="text-sm text-gray-500">Belum ada aktivitas</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Recent articles --}}
    <section class="rounded-xl border border-gray-200 bg-white overflow-hidden">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between px-4 sm:px-5 py-4 border-b border-gray-100">
            <h3 class="text-sm font-semibold text-gray-900">Artikel terbaru</h3>
            <a href="{{ route('admin.articles.index') }}" class="text-xs font-medium text-blue-600 hover:text-news-accent">Lihat semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead>
                    <tr class="bg-gray-50/80">
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Judul</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Penulis</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Status</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-500">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recent_articles as $article)
                        <tr class="hover:bg-gray-50/80 transition-colors">
                            <td class="px-5 py-3.5">
                                <a href="{{ route('admin.articles.show', $article) }}" class="block group">
                                    <div class="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition-colors line-clamp-2" title="{{ $article->title }}">
                                        {{ $article->title }}
                                    </div>
                                    @if($article->excerpt)
                                        <div class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ $article->excerpt }}</div>
                                    @endif
                                </a>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span class="flex h-7 w-7 items-center justify-center rounded-full bg-gray-100 text-xs font-semibold text-gray-600">
                                        {{ strtoupper(substr($article->author->name ?? 'A', 0, 1)) }}
                                    </span>
                                    <span class="text-sm text-gray-800">{{ $article->author->name ?? 'Unknown' }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                @php
                                    $statusMap = [
                                        'published' => ['Terbit', 'bg-emerald-50 text-emerald-700 ring-emerald-600/20'],
                                        'pending_review' => ['Review', 'bg-amber-50 text-amber-700 ring-amber-600/20'],
                                        'draft' => ['Draft', 'bg-gray-100 text-gray-600 ring-gray-500/20'],
                                        'rejected' => ['Ditolak', 'bg-rose-50 text-rose-700 ring-rose-600/20'],
                                    ];
                                    [$label, $classes] = $statusMap[$article->status] ?? ['Lainnya', 'bg-gray-100 text-gray-600 ring-gray-500/20'];
                                @endphp
                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $classes }}">
                                    {{ $label }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <div class="text-sm text-gray-800">{{ $article->created_at?->format('d M Y') ?? '-' }}</div>
                                <div class="text-xs text-gray-400">{{ $article->created_at?->format('H:i') ?? '' }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-12 text-center text-sm text-gray-500">
                                Belum ada artikel
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('adminActivityChart');
    if (!canvas) return;

    const chartData = @json($chartData);
    const ctx = canvas.getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.map(item => item.date),
            datasets: [
                {
                    label: 'Artikel',
                    data: chartData.map(item => item.articles),
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.08)',
                    borderWidth: 2,
                    tension: 0.35,
                    fill: true,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                },
                {
                    label: 'Komentar',
                    data: chartData.map(item => item.comments),
                    borderColor: '#d97706',
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    tension: 0.35,
                    fill: false,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    align: 'end',
                    labels: {
                        boxWidth: 8,
                        boxHeight: 8,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: { size: 11 },
                        color: '#6b7280',
                    },
                },
                tooltip: {
                    backgroundColor: '#111827',
                    titleFont: { size: 12 },
                    bodyFont: { size: 12 },
                    padding: 10,
                    cornerRadius: 8,
                },
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#9ca3af', font: { size: 11 } },
                    border: { display: false },
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        color: '#9ca3af',
                        font: { size: 11 },
                    },
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    border: { display: false },
                },
            },
            interaction: { intersect: false, mode: 'index' },
        },
    });
});
</script>
@endpush
