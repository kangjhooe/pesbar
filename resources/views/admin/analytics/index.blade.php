@extends('layouts.admin-simple')

@section('title', 'Analitik - Admin Panel')
@section('page-title', 'Analitik Website')
@section('page-subtitle', 'Statistik dan performa website')

@section('content')
<div class="space-y-6">
    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-newspaper text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Artikel</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_articles'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Artikel Terbit</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['published_articles'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-eye text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Views</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($stats['total_views']) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Users</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_users'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-comments text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Komentar</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_comments'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Articles Chart -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Artikel (30 Hari Terakhir)</h3>
            <div class="h-64">
                <canvas id="articlesChart"></canvas>
            </div>
        </div>
        
        <!-- Views Chart -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Views (30 Hari Terakhir)</h3>
            <div class="h-64">
                <canvas id="viewsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Articles -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Artikel Terpopuler</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @php
                    $popularArticles = \App\Models\Article::where('status', 'published')
                        ->orderBy('views', 'desc')
                        ->limit(10)
                        ->get();
                @endphp
                
                @forelse($popularArticles as $index => $article)
                <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg">
                    <div class="flex-shrink-0 w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-semibold">
                        {{ $index + 1 }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-medium text-gray-900 truncate">{{ $article->title }}</h4>
                        <p class="text-sm text-gray-500">{{ $article->category->name ?? 'Tidak ada kategori' }}</p>
                    </div>
                    <div class="flex-shrink-0 text-right">
                        <p class="text-sm font-semibold text-gray-900">{{ number_format($article->views) }}</p>
                        <p class="text-xs text-gray-500">views</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <i class="fas fa-chart-bar text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">Belum ada data artikel</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Category Performance -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Performa Kategori</h3>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                @php
                    $categoryStats = \App\Models\Category::withCount('articles')
                        ->with(['articles' => function($query) {
                            $query->where('status', 'published');
                        }])
                        ->get()
                        ->map(function($category) {
                            $category->total_views = $category->articles->sum('views');
                            return $category;
                        })
                        ->sortByDesc('total_views')
                        ->take(10);
                @endphp
                
                @forelse($categoryStats as $category)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <div class="w-4 h-4 rounded-full" style="background-color: {{ $category->color ?? '#3b82f6' }}"></div>
                        <div>
                            <h4 class="text-sm font-medium text-gray-900">{{ $category->name }}</h4>
                            <p class="text-sm text-gray-500">{{ $category->articles_count }} artikel</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-900">{{ number_format($category->total_views) }}</p>
                        <p class="text-xs text-gray-500">total views</p>
                    </div>
                </div>
                @empty
                <div class="text-center py-8">
                    <i class="fas fa-tags text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">Belum ada data kategori</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Articles Chart
const articlesCtx = document.getElementById('articlesChart').getContext('2d');
const articlesChart = new Chart(articlesCtx, {
    type: 'line',
    data: {
        labels: @json(array_column($chartData, 'date')),
        datasets: [{
            label: 'Artikel',
            data: @json(array_column($chartData, 'articles')),
            borderColor: 'rgb(59, 130, 246)',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// Views Chart
const viewsCtx = document.getElementById('viewsChart').getContext('2d');
const viewsChart = new Chart(viewsCtx, {
    type: 'bar',
    data: {
        labels: @json(array_column($chartData, 'date')),
        datasets: [{
            label: 'Views',
            data: @json(array_column($chartData, 'views')),
            backgroundColor: 'rgba(16, 185, 129, 0.8)',
            borderColor: 'rgb(16, 185, 129)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
</script>
@endpush
@endsection
