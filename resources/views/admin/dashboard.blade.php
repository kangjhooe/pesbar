@extends('layouts.admin-simple')

@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Halo, ' . Auth::user()->name . ' · ' . now()->translatedFormat('l, d F Y'))

@section('content')
@php $isFullAdmin = Auth::user()->isAdmin(); @endphp
<div class="space-y-6">
    {{-- Action queue --}}
    <section>
        <div class="mb-3">
            <h3 class="text-[11px] font-bold uppercase tracking-wider text-news-muted">Perlu tindakan</h3>
            <p class="text-xs text-news-muted mt-0.5">Moderasi penayangan & antrean lain</p>
        </div>
        <div class="grid grid-cols-1 {{ $isFullAdmin ? 'sm:grid-cols-3' : 'sm:grid-cols-1' }} gap-3">
            <a href="{{ route('admin.articles.suspended') }}"
               class="group flex items-center justify-between border border-news-line border-t-2 border-t-orange-500 bg-white px-4 py-4 hover:border-news-ink transition-colors">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-news-muted">Ditangguhkan</p>
                    <p class="mt-1 text-2xl font-bold tabular-nums text-news-ink">{{ $stats['suspended_articles'] }}</p>
                </div>
                <span class="inline-flex items-center text-xs font-semibold text-news-accent opacity-0 group-hover:opacity-100 transition-opacity">
                    Lihat <i class="fas fa-arrow-right ml-1.5 text-[10px]"></i>
                </span>
            </a>

            @if($isFullAdmin)
            <a href="{{ route('admin.comments.index', ['status' => 'pending']) }}"
               class="group flex items-center justify-between border border-news-line border-t-2 border-t-news-accent bg-white px-4 py-4 hover:border-news-ink transition-colors">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-news-muted">Komentar pending</p>
                    <p class="mt-1 text-2xl font-bold tabular-nums text-news-ink">{{ $stats['pending_comments'] }}</p>
                </div>
                <span class="inline-flex items-center text-xs font-semibold text-news-accent opacity-0 group-hover:opacity-100 transition-opacity">
                    Moderasi <i class="fas fa-arrow-right ml-1.5 text-[10px]"></i>
                </span>
            </a>

            <a href="{{ route('admin.verification-requests') }}"
               class="group flex items-center justify-between border border-news-line border-t-2 border-t-news-ink bg-white px-4 py-4 hover:border-news-ink transition-colors">
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-wider text-news-muted">Upgrade / verifikasi</p>
                    <p class="mt-1 text-2xl font-bold tabular-nums text-news-ink">{{ $stats['pending_verification_requests'] }}</p>
                </div>
                <span class="inline-flex items-center text-xs font-semibold text-news-accent opacity-0 group-hover:opacity-100 transition-opacity">
                    Tinjau <i class="fas fa-arrow-right ml-1.5 text-[10px]"></i>
                </span>
            </a>
            @endif
        </div>
    </section>

    {{-- Today + quick actions --}}
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 border border-news-line border-t-2 border-t-news-ink bg-white p-5">
            <h3 class="text-[11px] font-bold uppercase tracking-wider text-news-muted mb-4">Hari ini</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div>
                    <p class="text-xs text-news-muted">Artikel baru</p>
                    <p class="mt-1 text-xl font-bold tabular-nums text-news-ink">{{ $stats['articles_today'] }}</p>
                </div>
                <div>
                    <p class="text-xs text-news-muted">Terbit hari ini</p>
                    <p class="mt-1 text-xl font-bold tabular-nums text-news-ink">{{ $stats['published_today'] }}</p>
                </div>
                <div>
                    <p class="text-xs text-news-muted">Komentar baru</p>
                    <p class="mt-1 text-xl font-bold tabular-nums text-news-ink">{{ $stats['comments_today'] }}</p>
                </div>
                <div>
                    <p class="text-xs text-news-muted">Pembacaan user</p>
                    <p class="mt-1 text-xl font-bold tabular-nums text-news-ink">{{ number_format($stats['reads_today']) }}</p>
                </div>
            </div>
            <div class="mt-5 pt-4 border-t border-news-line grid grid-cols-2 sm:grid-cols-5 gap-3 text-sm">
                <div>
                    <p class="text-xs text-news-muted">Users</p>
                    <p class="font-semibold text-news-ink tabular-nums">{{ number_format($stats['total_users']) }} <span class="text-xs font-normal text-emerald-700">+{{ $monthlyStats['users_this_month'] ?? 0 }}</span></p>
                </div>
                <div>
                    <p class="text-xs text-news-muted">Penulis</p>
                    <p class="font-semibold text-news-ink tabular-nums">{{ number_format($stats['total_penulis']) }}</p>
                </div>
                <div>
                    <p class="text-xs text-news-muted">Artikel terbit</p>
                    <p class="font-semibold text-news-ink tabular-nums">{{ number_format($stats['published_articles']) }} <span class="text-xs font-normal text-news-muted">/ {{ number_format($stats['total_articles']) }} total</span></p>
                </div>
                <div>
                    <p class="text-xs text-news-muted">Total views</p>
                    <p class="font-semibold text-news-ink tabular-nums">{{ number_format($stats['total_views']) }}</p>
                </div>
                <div>
                    <p class="text-xs text-news-muted">Subscriber aktif</p>
                    <p class="font-semibold text-news-ink tabular-nums">{{ number_format($stats['newsletter_subscribers']) }}</p>
                </div>
            </div>
        </div>

        <div class="border border-news-line bg-white p-5">
            <h3 class="text-[11px] font-bold uppercase tracking-wider text-news-muted mb-4">Aksi cepat</h3>
            <div class="space-y-1">
                @if($isFullAdmin)
                <a href="{{ route('admin.articles.create') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-news-ink hover:bg-news-paper transition-colors">
                    <span class="flex h-8 w-8 items-center justify-center bg-news-ink text-white">
                        <i class="fas fa-plus text-xs"></i>
                    </span>
                    Buat artikel
                </a>
                @endif
                <a href="{{ route('admin.articles.moderate') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-news-ink hover:bg-news-paper transition-colors">
                    <span class="flex h-8 w-8 items-center justify-center bg-news-paper text-orange-700">
                        <i class="fas fa-hand-paper text-xs"></i>
                    </span>
                    Tangguhkan artikel
                </a>
                <a href="{{ route('admin.articles.suspended') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-news-ink hover:bg-news-paper transition-colors">
                    <span class="flex h-8 w-8 items-center justify-center bg-news-paper text-orange-700">
                        <i class="fas fa-pause-circle text-xs"></i>
                    </span>
                    Artikel ditangguhkan
                </a>
                @if($isFullAdmin)
                <a href="{{ route('admin.comments.index', ['status' => 'pending']) }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-news-ink hover:bg-news-paper transition-colors">
                    <span class="flex h-8 w-8 items-center justify-center bg-news-paper text-news-accent">
                        <i class="fas fa-comments text-xs"></i>
                    </span>
                    Moderasi komentar
                </a>
                <a href="{{ route('admin.verification-requests') }}"
                   class="flex items-center gap-3 px-3 py-2.5 text-sm font-medium text-news-ink hover:bg-news-paper transition-colors">
                    <span class="flex h-8 w-8 items-center justify-center bg-news-paper text-news-ink">
                        <i class="fas fa-user-check text-xs"></i>
                    </span>
                    Verifikasi & upgrade
                </a>
                @endif
            </div>
        </div>
    </section>

    {{-- Chart + activity --}}
    <section class="grid grid-cols-1 lg:grid-cols-5 gap-4">
        <div class="lg:col-span-3 border border-news-line bg-white p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-[11px] font-bold uppercase tracking-wider text-news-muted">Aktivitas 7 hari</h3>
                @if($isFullAdmin)
                <a href="{{ route('admin.analytics.index') }}" class="text-xs font-semibold text-news-accent hover:text-news-ink">Analitik</a>
                @endif
            </div>
            <div class="h-56">
                <canvas id="adminActivityChart"></canvas>
            </div>
        </div>

        <div class="lg:col-span-2 border border-news-line bg-white p-5">
            <h3 class="text-[11px] font-bold uppercase tracking-wider text-news-muted mb-4">Aktivitas terbaru</h3>
            <div class="space-y-1 max-h-56 overflow-y-auto">
                @forelse($recent_activity->take(8) as $activity)
                    @php
                        $dot = match($activity['color'] ?? 'gray') {
                            'green' => 'bg-emerald-600',
                            'yellow' => 'bg-amber-500',
                            'blue' => 'bg-news-accent',
                            'red' => 'bg-news-accent',
                            default => 'bg-news-muted',
                        };
                    @endphp
                    <div class="flex gap-3 px-2 py-2.5 hover:bg-news-paper">
                        <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full {{ $dot }}"></span>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm text-news-ink truncate">
                                <span class="font-medium">{{ $activity['user'] }}</span>
                                <span class="text-news-muted"> {{ $activity['action'] }}</span>
                            </p>
                            @if(!empty($activity['title']))
                                <p class="text-xs text-news-muted truncate mt-0.5">{{ $activity['title'] }}</p>
                            @endif
                            <p class="text-[11px] text-news-muted/80 mt-0.5">
                                {{ $activity['time'] ? \Carbon\Carbon::parse($activity['time'])->diffForHumans() : 'Baru saja' }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="py-10 text-center">
                        <p class="text-sm text-news-muted">Belum ada aktivitas</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Recent articles --}}
    <section class="border border-news-line bg-white overflow-hidden">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between px-4 sm:px-5 py-4 border-b border-news-line">
            <h3 class="text-[11px] font-bold uppercase tracking-wider text-news-muted">Artikel terbaru</h3>
            @if($isFullAdmin)
            <a href="{{ route('admin.articles.index') }}" class="text-xs font-semibold text-news-accent hover:text-news-ink">Lihat semua</a>
            @else
            <a href="{{ route('admin.articles.suspended') }}" class="text-xs font-semibold text-news-accent hover:text-news-ink">Ditangguhkan</a>
            @endif
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-news-line">
                <thead>
                    <tr class="bg-news-paper">
                        <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-news-muted">Judul</th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-news-muted">Penulis</th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-news-muted">Status</th>
                        <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-news-muted">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-news-line">
                    @forelse($recent_articles as $article)
                        <tr class="hover:bg-news-paper transition-colors">
                            <td class="px-5 py-3.5">
                                @php
                                    $articleUrl = $isFullAdmin
                                        ? route('admin.articles.show', $article)
                                        : (in_array($article->status, ['published', 'suspended', 'archived'], true)
                                            ? route('admin.articles.detail', $article)
                                            : null);
                                @endphp
                                @if($articleUrl)
                                <a href="{{ $articleUrl }}" class="block group">
                                    <div class="text-sm font-medium text-news-ink group-hover:text-news-accent transition-colors line-clamp-2" title="{{ $article->title }}">
                                        {{ $article->title }}
                                    </div>
                                    @if($article->excerpt)
                                        <div class="text-xs text-news-muted mt-0.5 line-clamp-1">{{ $article->excerpt }}</div>
                                    @endif
                                </a>
                                @else
                                <div>
                                    <div class="text-sm font-medium text-news-ink line-clamp-2" title="{{ $article->title }}">
                                        {{ $article->title }}
                                    </div>
                                    @if($article->excerpt)
                                        <div class="text-xs text-news-muted mt-0.5 line-clamp-1">{{ $article->excerpt }}</div>
                                    @endif
                                </div>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <span class="flex h-7 w-7 items-center justify-center bg-news-ink text-xs font-semibold text-white">
                                        {{ strtoupper(substr($article->author->name ?? 'A', 0, 1)) }}
                                    </span>
                                    <span class="text-sm text-news-ink">{{ $article->author->name ?? 'Unknown' }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                @php
                                    $statusMap = [
                                        'published' => ['Terbit', 'bg-emerald-50 text-emerald-800'],
                                        'suspended' => ['Ditangguhkan', 'bg-orange-50 text-orange-800'],
                                        'draft' => ['Draft', 'bg-news-paper text-news-muted'],
                                        'archived' => ['Arsip', 'bg-news-paper text-news-muted'],
                                    ];
                                    [$label, $classes] = $statusMap[$article->status] ?? ['Lainnya', 'bg-news-paper text-news-muted'];
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 text-xs font-semibold {{ $classes }}">
                                    {{ $label }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <div class="text-sm text-news-ink">{{ $article->created_at?->format('d M Y') ?? '-' }}</div>
                                <div class="text-xs text-news-muted">{{ $article->created_at?->format('H:i') ?? '' }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-12 text-center text-sm text-news-muted">
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
                    borderColor: '#0a0a0a',
                    backgroundColor: 'rgba(10, 10, 10, 0.06)',
                    borderWidth: 2,
                    tension: 0.35,
                    fill: true,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    pointBackgroundColor: '#0a0a0a',
                },
                {
                    label: 'Komentar',
                    data: chartData.map(item => item.comments),
                    borderColor: '#b91c1c',
                    backgroundColor: 'transparent',
                    borderWidth: 2,
                    tension: 0.35,
                    fill: false,
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    pointBackgroundColor: '#b91c1c',
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
                        color: '#5c5c5c',
                    },
                },
                tooltip: {
                    backgroundColor: '#0a0a0a',
                    titleFont: { size: 12 },
                    bodyFont: { size: 12 },
                    padding: 10,
                    cornerRadius: 0,
                },
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#5c5c5c', font: { size: 11 } },
                    border: { display: false },
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        color: '#5c5c5c',
                        font: { size: 11 },
                    },
                    grid: { color: 'rgba(10, 10, 10, 0.06)' },
                    border: { display: false },
                },
            },
            interaction: { intersect: false, mode: 'index' },
        },
    });
});
</script>
@endpush
