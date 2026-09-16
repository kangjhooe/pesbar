@extends('layouts.admin-simple')

@section('title', 'Analitik - Admin Panel')
@section('page-title', 'Analitik Website')
@section('page-subtitle', 'Statistik dan performa website')

@section('content')
<div class="space-y-6">
    <div class="bg-white border border-news-line p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <p class="text-sm text-news-muted">Periode tren: <span class="font-medium text-news-ink">{{ $days }} hari</span></p>
        <form method="GET" action="{{ route('admin.analytics.index') }}" class="flex items-center gap-2">
            <label for="days" class="text-sm text-news-ink">Filter</label>
            <select name="days" id="days" class="form-select" onchange="this.form.submit()">
                <option value="7" {{ (int)$days === 7 ? 'selected' : '' }}>7 hari</option>
                <option value="30" {{ (int)$days === 30 ? 'selected' : '' }}>30 hari</option>
                <option value="90" {{ (int)$days === 90 ? 'selected' : '' }}>90 hari</option>
            </select>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <div class="bg-white border border-news-line p-6">
            <div class="flex items-center">
                <div class="p-3 bg-news-paper text-news-ink">
                    <i class="fas fa-newspaper text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-news-muted">Total Artikel</p>
                    <p class="text-2xl font-semibold text-news-ink">{{ $stats['total_articles'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-news-line p-6">
            <div class="flex items-center">
                <div class="p-3 bg-news-paper text-news-ink">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-news-muted">Artikel Terbit</p>
                    <p class="text-2xl font-semibold text-news-ink">{{ $stats['published_articles'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-news-line p-6">
            <div class="flex items-center">
                <div class="p-3 bg-news-paper text-news-ink">
                    <i class="fas fa-eye text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-news-muted">Total Views</p>
                    <p class="text-2xl font-semibold text-news-ink">{{ number_format($stats['total_views']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-news-line p-6">
            <div class="flex items-center">
                <div class="p-3 bg-news-paper text-news-ink">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-news-muted">Total Users</p>
                    <p class="text-2xl font-semibold text-news-ink">{{ $stats['total_users'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-news-line p-6">
            <div class="flex items-center">
                <div class="p-3 bg-news-paper text-news-ink">
                    <i class="fas fa-comments text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-news-muted">Total Komentar</p>
                    <p class="text-2xl font-semibold text-news-ink">{{ $stats['total_comments'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white border border-news-line p-4">
            <p class="text-sm text-news-muted">Artikel terbit (periode)</p>
            <p class="text-xl font-semibold text-news-ink">{{ number_format($stats['period_articles']) }}</p>
        </div>
        <div class="bg-white border border-news-line p-4">
            <p class="text-sm text-news-muted">Views artikel baru (periode)</p>
            <p class="text-xl font-semibold text-news-ink">{{ number_format($stats['period_views']) }}</p>
        </div>
        <div class="bg-white border border-news-line p-4">
            <p class="text-sm text-news-muted">Komentar (periode)</p>
            <p class="text-xl font-semibold text-news-ink">{{ number_format($stats['period_comments']) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white border border-news-line p-6">
            <h3 class="text-lg font-semibold text-news-ink mb-4">Artikel ({{ $days }} Hari Terakhir)</h3>
            <div class="h-64">
                <canvas id="articlesChart"></canvas>
            </div>
        </div>

        <div class="bg-white border border-news-line p-6">
            <h3 class="text-lg font-semibold text-news-ink mb-4">Views ({{ $days }} Hari Terakhir)</h3>
            <div class="h-64">
                <canvas id="viewsChart"></canvas>
            </div>
        </div>
    </div>

    <div class="bg-white border border-news-line">
        <div class="px-6 py-4 border-b border-news-line">
            <h3 class="text-lg font-semibold text-news-ink">Artikel Terpopuler</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @forelse($popularArticles as $index => $article)
                <div class="flex items-center space-x-4 p-4 bg-news-paper">
                    <div class="flex-shrink-0 w-8 h-8 bg-white text-news-ink rounded-full flex items-center justify-center font-semibold border border-news-line">
                        {{ $index + 1 }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-medium text-news-ink line-clamp-2" title="{{ $article['title'] }}">{{ $article['title'] }}</h4>
                        <p class="text-sm text-news-muted">{{ $article['category'] }} · {{ $article['author'] }}</p>
                    </div>
                    <div class="flex-shrink-0 text-right">
                        <p class="text-sm font-semibold text-news-ink">{{ number_format($article['views']) }}</p>
                        <p class="text-xs text-news-muted">views</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <i class="fas fa-chart-bar text-4xl text-news-muted mb-4"></i>
                    <p class="text-news-muted">Belum ada data artikel</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="bg-white border border-news-line">
        <div class="px-6 py-4 border-b border-news-line">
            <h3 class="text-lg font-semibold text-news-ink">Performa Kategori</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @forelse(collect($categoryStats)->take(10) as $category)
                <div class="flex items-center justify-between p-4 bg-news-paper">
                    <div>
                        <h4 class="text-sm font-medium text-news-ink">{{ $category['category_name'] }}</h4>
                        <p class="text-sm text-news-muted">{{ $category['total_articles'] }} artikel</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-news-ink">{{ number_format($category['total_views']) }}</p>
                        <p class="text-xs text-news-muted">total views</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <i class="fas fa-tags text-4xl text-news-muted mb-4"></i>
                    <p class="text-news-muted">Belum ada data kategori</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const chartLabels = @json(array_column($chartData, 'date_formatted'));
const articlesData = @json(array_column($chartData, 'articles'));
const viewsData = @json(array_column($chartData, 'views'));

new Chart(document.getElementById('articlesChart').getContext('2d'), {
    type: 'line',
    data: {
        labels: chartLabels,
        datasets: [{
            label: 'Artikel',
            data: articlesData,
            borderColor: '#0a0a0a',
            backgroundColor: 'rgba(10, 10, 10, 0.06)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
        plugins: { legend: { display: false } }
    }
});

new Chart(document.getElementById('viewsChart').getContext('2d'), {
    type: 'bar',
    data: {
        labels: chartLabels,
        datasets: [{
            label: 'Views',
            data: viewsData,
            backgroundColor: 'rgba(185, 28, 28, 0.75)',
            borderColor: '#b91c1c',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: { y: { beginAtZero: true } },
        plugins: { legend: { display: false } }
    }
});
</script>
@endpush
@endsection
