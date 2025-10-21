<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Articles Card -->
    <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 p-6 transform hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl group-hover:from-blue-100 group-hover:to-blue-200 transition-all duration-300">
                    <i class="fas fa-newspaper text-blue-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Total Artikel</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['articles'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $stats['published_articles'] }} dipublikasi, {{ $stats['draft_articles'] }} draft</p>
                </div>
            </div>
            <div class="text-right">
                <div class="bg-blue-50 rounded-full px-3 py-1">
                    <span class="text-xs font-medium text-blue-600">Bulan ini</span>
                </div>
                <p class="text-lg font-bold text-blue-600 mt-2">+{{ $monthlyStats['articles_this_month'] }}</p>
            </div>
        </div>
    </div>

    <!-- Categories Card -->
    <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 p-6 transform hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-2xl group-hover:from-green-100 group-hover:to-green-200 transition-all duration-300">
                    <i class="fas fa-folder text-green-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Kategori</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['categories'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $stats['active_categories'] }} aktif</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Comments Card -->
    <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 p-6 transform hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="p-4 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-2xl group-hover:from-yellow-100 group-hover:to-yellow-200 transition-all duration-300">
                    <i class="fas fa-comments text-yellow-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Komentar</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['comments'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $stats['pending_comments'] }} menunggu moderasi</p>
                </div>
            </div>
            <div class="text-right">
                <div class="bg-yellow-50 rounded-full px-3 py-1">
                    <span class="text-xs font-medium text-yellow-600">Bulan ini</span>
                </div>
                <p class="text-lg font-bold text-yellow-600 mt-2">+{{ $monthlyStats['comments_this_month'] }}</p>
            </div>
        </div>
    </div>

    <!-- Subscribers Card -->
    <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 p-6 transform hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl group-hover:from-purple-100 group-hover:to-purple-200 transition-all duration-300">
                    <i class="fas fa-envelope text-purple-600 text-2xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Newsletter</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['subscribers'] }}</p>
                </div>
            </div>
            <div class="text-right">
                <div class="bg-purple-50 rounded-full px-3 py-1">
                    <span class="text-xs font-medium text-purple-600">Bulan ini</span>
                </div>
                <p class="text-lg font-bold text-purple-600 mt-2">+{{ $monthlyStats['subscribers_this_month'] }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Additional Stats Row -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Users Card -->
    <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 p-6 transform hover:-translate-y-1">
        <div class="flex items-center">
            <div class="p-4 bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-2xl group-hover:from-indigo-100 group-hover:to-indigo-200 transition-all duration-300">
                <i class="fas fa-users text-indigo-600 text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Total User</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stats['users'] }}</p>
            </div>
        </div>
    </div>

    <!-- Popular Article Card -->
    <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 p-6 transform hover:-translate-y-1">
        <div class="flex items-center">
            <div class="p-4 bg-gradient-to-br from-red-50 to-red-100 rounded-2xl group-hover:from-red-100 group-hover:to-red-200 transition-all duration-300">
                <i class="fas fa-fire text-red-600 text-2xl"></i>
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
                <p class="text-xs text-gray-500 mt-1">views</p>
            </div>
        </div>
    </div>

    <!-- System Status Card -->
    <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100 p-6 transform hover:-translate-y-1">
        <div class="flex items-center">
            <div class="p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-2xl group-hover:from-green-100 group-hover:to-green-200 transition-all duration-300">
                <i class="fas fa-check-circle text-green-600 text-2xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Status Sistem</p>
                <p class="text-2xl font-bold text-green-600 mt-1">Online</p>
                <p class="text-xs text-gray-500 mt-1">Semua sistem berjalan normal</p>
            </div>
        </div>
    </div>
</div>

<!-- Chart and Activity Row -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Activity Chart -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900">Aktivitas 7 Hari Terakhir</h3>
            <div class="flex space-x-2">
                <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                <div class="w-3 h-3 bg-yellow-500 rounded-full"></div>
                <div class="w-3 h-3 bg-green-500 rounded-full"></div>
            </div>
        </div>
        <div class="h-64">
            <canvas id="activityChart"></canvas>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-gray-900">Aktivitas Terbaru</h3>
            <div class="bg-gray-100 rounded-full px-3 py-1">
                <span class="text-xs font-medium text-gray-600">Live</span>
            </div>
        </div>
        <div class="space-y-4 max-h-64 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
            @forelse($recentActivity as $activity)
                <div class="flex items-center space-x-4 p-4 bg-gradient-to-r from-gray-50 to-white rounded-xl hover:from-gray-100 hover:to-gray-50 transition-all duration-200 border border-gray-100">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-gradient-to-br from-{{ $activity['color'] }}-100 to-{{ $activity['color'] }}-200 rounded-xl flex items-center justify-center shadow-sm">
                            <i class="{{ $activity['icon'] }} text-{{ $activity['color'] }}-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">
                            {{ $activity['title'] }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $activity['user'] }} • {{ $activity['time']->diffForHumans() }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-history text-gray-400 text-2xl"></i>
                    </div>
                    <p class="text-gray-600 font-medium">Belum ada aktivitas</p>
                    <p class="text-sm text-gray-500 mt-1">Aktivitas akan muncul di sini</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8 hover:shadow-xl transition-all duration-300">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-xl font-bold text-gray-900">Aksi Cepat</h3>
        <div class="bg-blue-50 rounded-full px-3 py-1">
            <span class="text-xs font-medium text-blue-600">Shortcuts</span>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <a href="#" class="group flex items-center p-5 bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl hover:from-blue-100 hover:to-blue-200 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg border border-blue-100">
            <div class="p-3 bg-blue-500 rounded-xl group-hover:bg-blue-600 transition-colors duration-300">
                <i class="fas fa-plus text-white text-lg"></i>
            </div>
            <div class="ml-4">
                <p class="font-semibold text-blue-900">Tambah Artikel</p>
                <p class="text-sm text-blue-700">Buat artikel baru</p>
            </div>
        </a>
        <a href="#" class="group flex items-center p-5 bg-gradient-to-br from-green-50 to-green-100 rounded-2xl hover:from-green-100 hover:to-green-200 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg border border-green-100">
            <div class="p-3 bg-green-500 rounded-xl group-hover:bg-green-600 transition-colors duration-300">
                <i class="fas fa-folder-plus text-white text-lg"></i>
            </div>
            <div class="ml-4">
                <p class="font-semibold text-green-900">Tambah Kategori</p>
                <p class="text-sm text-green-700">Buat kategori baru</p>
            </div>
        </a>
        <a href="#" class="group flex items-center p-5 bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-2xl hover:from-yellow-100 hover:to-yellow-200 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg border border-yellow-100">
            <div class="p-3 bg-yellow-500 rounded-xl group-hover:bg-yellow-600 transition-colors duration-300">
                <i class="fas fa-comments text-white text-lg"></i>
            </div>
            <div class="ml-4">
                <p class="font-semibold text-yellow-900">Moderasi</p>
                <p class="text-sm text-yellow-700">Kelola komentar</p>
            </div>
        </a>
        <a href="#" class="group flex items-center p-5 bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl hover:from-purple-100 hover:to-purple-200 transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg border border-purple-100">
            <div class="p-3 bg-purple-500 rounded-xl group-hover:bg-purple-600 transition-colors duration-300">
                <i class="fas fa-cog text-white text-lg"></i>
            </div>
            <div class="ml-4">
                <p class="font-semibold text-purple-900">Pengaturan</p>
                <p class="text-sm text-purple-700">Kelola pengaturan</p>
            </div>
        </a>
    </div>
</div>

<!-- Popular Categories -->
<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-all duration-300">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-xl font-bold text-gray-900">Kategori Populer</h3>
        <div class="bg-green-50 rounded-full px-3 py-1">
            <span class="text-xs font-medium text-green-600">Top 5</span>
        </div>
    </div>
    <div class="space-y-4">
        @forelse($categoryStats as $index => $category)
            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-white rounded-xl hover:from-gray-100 hover:to-gray-50 transition-all duration-200 border border-gray-100">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center text-white font-bold text-sm mr-4">
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
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full transition-all duration-500" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12">
                <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-folder text-gray-400 text-2xl"></i>
                </div>
                <p class="text-gray-600 font-medium">Belum ada kategori</p>
                <p class="text-sm text-gray-500 mt-1">Kategori akan muncul di sini</p>
            </div>
        @endforelse
    </div>
</div>

@push('styles')
<style>
/* Custom Scrollbar */
.scrollbar-thin {
    scrollbar-width: thin;
}

.scrollbar-thumb-gray-300::-webkit-scrollbar-thumb {
    background-color: #d1d5db;
    border-radius: 0.5rem;
}

.scrollbar-track-gray-100::-webkit-scrollbar-track {
    background-color: #f3f4f6;
}

.scrollbar-thin::-webkit-scrollbar {
    width: 6px;
}

/* Smooth animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-up {
    animation: fadeInUp 0.6s ease-out;
}

/* Hover effects */
.group:hover .group-hover\:scale-105 {
    transform: scale(1.05);
}

/* Gradient text */
.gradient-text {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('activityChart').getContext('2d');
    const chartData = @json($chartData);
    
    // Create gradient
    const gradient1 = ctx.createLinearGradient(0, 0, 0, 400);
    gradient1.addColorStop(0, 'rgba(59, 130, 246, 0.3)');
    gradient1.addColorStop(1, 'rgba(59, 130, 246, 0.05)');
    
    const gradient2 = ctx.createLinearGradient(0, 0, 0, 400);
    gradient2.addColorStop(0, 'rgba(245, 158, 11, 0.3)');
    gradient2.addColorStop(1, 'rgba(245, 158, 11, 0.05)');
    
    const gradient3 = ctx.createLinearGradient(0, 0, 0, 400);
    gradient3.addColorStop(0, 'rgba(16, 185, 129, 0.3)');
    gradient3.addColorStop(1, 'rgba(16, 185, 129, 0.05)');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: chartData.map(item => item.date),
            datasets: [{
                label: 'Artikel',
                data: chartData.map(item => item.articles),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: gradient1,
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: 'rgb(59, 130, 246)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }, {
                label: 'Komentar',
                data: chartData.map(item => item.comments),
                borderColor: 'rgb(245, 158, 11)',
                backgroundColor: gradient2,
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: 'rgb(245, 158, 11)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }, {
                label: 'Views',
                data: chartData.map(item => item.views),
                borderColor: 'rgb(16, 185, 129)',
                backgroundColor: gradient3,
                borderWidth: 3,
                tension: 0.4,
                fill: true,
                pointBackgroundColor: 'rgb(16, 185, 129)',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
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
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
});
</script>
@endpush
