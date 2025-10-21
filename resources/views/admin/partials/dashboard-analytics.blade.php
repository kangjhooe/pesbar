<!-- Analytics Overview -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <!-- Total Views -->
    <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 p-6 transform hover:-translate-y-1">
        <div class="flex items-center">
            <div class="p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl group-hover:from-blue-100 group-hover:to-blue-200 transition-all duration-300">
                <i class="fas fa-eye text-blue-600 text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Total Views</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $popularArticles->sum('views') }}</p>
            </div>
        </div>
    </div>

    <!-- Average Views per Article -->
    <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 p-6 transform hover:-translate-y-1">
        <div class="flex items-center">
            <div class="p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-2xl group-hover:from-green-100 group-hover:to-green-200 transition-all duration-300">
                <i class="fas fa-chart-line text-green-600 text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Rata-rata Views</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">
                    {{ $stats['articles'] > 0 ? round($popularArticles->sum('views') / $stats['articles']) : 0 }}
                </p>
            </div>
        </div>
    </div>

    <!-- Most Popular Article -->
    <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 p-6 transform hover:-translate-y-1">
        <div class="flex items-center">
            <div class="p-4 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-2xl group-hover:from-yellow-100 group-hover:to-yellow-200 transition-all duration-300">
                <i class="fas fa-fire text-yellow-600 text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Artikel Terpopuler</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">
                    @if($popularArticles->count() > 0)
                        {{ $popularArticles->first()->views ?? 0 }}
                    @else
                        0
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Engagement Rate -->
    <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 p-6 transform hover:-translate-y-1">
        <div class="flex items-center">
            <div class="p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl group-hover:from-purple-100 group-hover:to-purple-200 transition-all duration-300">
                <i class="fas fa-heart text-purple-600 text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Engagement</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">
                    @php
                        $totalViews = $popularArticles->sum('views');
                        $totalComments = $stats['comments'];
                        $engagementRate = $totalViews > 0 ? round(($totalComments / $totalViews) * 100, 1) : 0;
                    @endphp
                    {{ $engagementRate }}%
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Views Chart -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900">Trend Views 7 Hari Terakhir</h3>
            <div class="flex space-x-2">
                <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
            </div>
        </div>
        <div class="h-64">
            <canvas id="viewsChart"></canvas>
        </div>
    </div>

    <!-- Content Distribution -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900">Distribusi Konten</h3>
            <div class="flex space-x-1">
                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                <div class="w-2 h-2 bg-yellow-500 rounded-full"></div>
                <div class="w-2 h-2 bg-red-500 rounded-full"></div>
                <div class="w-2 h-2 bg-purple-500 rounded-full"></div>
            </div>
        </div>
        <div class="h-64">
            <canvas id="contentChart"></canvas>
        </div>
    </div>
</div>

<!-- Popular Articles and Categories -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Top 5 Popular Articles -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900">5 Artikel Terpopuler</h3>
            <div class="bg-red-50 rounded-full px-3 py-1">
                <span class="text-xs font-medium text-red-600">Trending</span>
            </div>
        </div>
        <div class="space-y-4">
            @forelse($popularArticles->take(5) as $index => $article)
                <div class="flex items-center space-x-4 p-4 bg-gradient-to-r from-gray-50 to-white rounded-xl hover:from-gray-100 hover:to-gray-50 transition-all duration-200 border border-gray-100">
                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-sm">
                        <span class="text-sm font-bold text-white">{{ $index + 1 }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold text-gray-900 truncate">
                            {{ $article->title }}
                        </h4>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $article->category->name ?? 'Tanpa Kategori' }} • 
                            {{ $article->created_at->format('d-m-Y') }}
                        </p>
                    </div>
                    <div class="flex-shrink-0 text-right">
                        <p class="text-lg font-bold text-gray-900">{{ $article->views ?? 0 }}</p>
                        <p class="text-xs text-gray-500">views</p>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-newspaper text-gray-400 text-2xl"></i>
                    </div>
                    <p class="text-gray-600 font-medium">Belum ada artikel</p>
                    <p class="text-sm text-gray-500 mt-1">Artikel populer akan muncul di sini</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Category Performance -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900">Performa Kategori</h3>
            <div class="bg-green-50 rounded-full px-3 py-1">
                <span class="text-xs font-medium text-green-600">Analytics</span>
            </div>
        </div>
        <div class="space-y-4">
            @forelse($categoryStats as $index => $category)
                <div class="flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-white rounded-xl hover:from-gray-100 hover:to-gray-50 transition-all duration-200 border border-gray-100">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center text-white font-bold text-sm mr-4">
                            {{ $index + 1 }}
                        </div>
                        <span class="text-sm font-semibold text-gray-900">{{ $category->name }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="text-sm text-gray-600 mr-3 font-medium">{{ $category->articles_count }} artikel</span>
                        <div class="w-24 bg-gray-200 rounded-full h-2">
                            @php
                                $maxArticles = $categoryStats->max('articles_count');
                                $percentage = $maxArticles > 0 ? ($category->articles_count / $maxArticles) * 100 : 0;
                            @endphp
                            <div class="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-folder text-gray-400 text-2xl"></i>
                    </div>
                    <p class="text-gray-600 font-medium">Belum ada kategori</p>
                    <p class="text-sm text-gray-500 mt-1">Performa kategori akan muncul di sini</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartData = @json($chartData);
    
    // Views Chart
    const viewsCtx = document.getElementById('viewsChart').getContext('2d');
    
    // Create gradient for views chart
    const viewsGradient = viewsCtx.createLinearGradient(0, 0, 0, 400);
    viewsGradient.addColorStop(0, 'rgba(59, 130, 246, 0.8)');
    viewsGradient.addColorStop(1, 'rgba(59, 130, 246, 0.1)');
    
    new Chart(viewsCtx, {
        type: 'bar',
        data: {
            labels: chartData.map(item => item.date),
            datasets: [{
                label: 'Views',
                data: chartData.map(item => item.views),
                backgroundColor: viewsGradient,
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11,
                            weight: '500'
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        font: {
                            size: 11,
                            weight: '500'
                        }
                    }
                }
            }
        }
    });

    // Content Distribution Chart
    const contentCtx = document.getElementById('contentChart').getContext('2d');
    const categoryData = @json($categoryStats);
    
    new Chart(contentCtx, {
        type: 'doughnut',
        data: {
            labels: categoryData.map(item => item.name),
            datasets: [{
                data: categoryData.map(item => item.articles_count),
                backgroundColor: [
                    'rgba(59, 130, 246, 0.9)',
                    'rgba(16, 185, 129, 0.9)',
                    'rgba(245, 158, 11, 0.9)',
                    'rgba(239, 68, 68, 0.9)',
                    'rgba(139, 92, 246, 0.9)'
                ],
                borderWidth: 3,
                borderColor: '#fff',
                hoverBorderWidth: 4,
                hoverBorderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: {
                            size: 12,
                            weight: '600'
                        }
                    }
                }
            },
            animation: {
                animateRotate: true,
                animateScale: true,
                duration: 2000
            }
        }
    });
});
</script>
@endpush
