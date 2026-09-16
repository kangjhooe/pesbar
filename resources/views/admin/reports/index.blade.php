@extends('layouts.admin-simple')

@section('title', 'Laporan - Admin Panel')
@section('page-title', 'Laporan Website')
@section('page-subtitle', 'Laporan dan statistik detail')

@section('content')
<div class="space-y-6">
    <!-- Export Actions -->
    <div class="bg-white border border-news-line p-6">
        <h3 class="text-lg font-semibold text-news-ink mb-4">Export Laporan</h3>
        <div class="flex flex-wrap gap-4">
            <form action="{{ route('admin.reports.export') }}" method="GET" class="inline">
                <input type="hidden" name="type" value="articles">
                <button type="submit" class="bg-news-accent text-white px-4 py-2 rounded-lg hover:bg-news-ink transition-colors flex items-center">
                    <i class="fas fa-file-excel mr-2"></i>
                    Export Artikel
                </button>
            </form>
            
            <form action="{{ route('admin.reports.export') }}" method="GET" class="inline">
                <input type="hidden" name="type" value="users">
                <button type="submit" class="btn-secondary px-4 py-2 flex items-center">
                    <i class="fas fa-users mr-2"></i>
                    Export Users
                </button>
            </form>
            
            <form action="{{ route('admin.reports.export') }}" method="GET" class="inline">
                <input type="hidden" name="type" value="comments">
                <button type="submit" class="btn-secondary px-4 py-2 flex items-center">
                    <i class="fas fa-comments mr-2"></i>
                    Export Komentar
                </button>
            </form>
            
            <form action="{{ route('admin.reports.export') }}" method="GET" class="inline">
                <input type="hidden" name="type" value="newsletter">
                <button type="submit" class="bg-news-ink text-white px-4 py-2 rounded-lg hover:bg-news-accent transition-colors flex items-center">
                    <i class="fas fa-envelope mr-2"></i>
                    Export Newsletter
                </button>
            </form>
        </div>
    </div>

    <!-- Articles by Category -->
    <div class="bg-white border border-news-line">
        <div class="px-6 py-4 border-b border-news-line">
            <h3 class="text-lg font-semibold text-news-ink">Artikel per Kategori</h3>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-news-line">
                    <thead class="bg-news-paper">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">Jumlah Artikel</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">Persentase</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-news-line">
                        @php
                            $totalArticles = $reports['articles_by_category']->sum('articles_count');
                        @endphp
                        @forelse($reports['articles_by_category'] as $category)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-4 h-4 rounded-full mr-3" style="background-color: {{ $category->color ?? '#b91c1c' }}"></div>
                                    <div class="text-sm font-medium text-news-ink">{{ $category->name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-news-ink">{{ $category->articles_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-full bg-news-paper rounded-full h-2 mr-2">
                                        <div class="bg-news-ink h-2 rounded-full" style="width: {{ $totalArticles > 0 ? ($category->articles_count / $totalArticles) * 100 : 0 }}%"></div>
                                    </div>
                                    <span class="text-sm text-news-muted">{{ $totalArticles > 0 ? number_format(($category->articles_count / $totalArticles) * 100, 1) : 0 }}%</span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-news-muted">
                                Belum ada data kategori
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Articles by Author -->
    <div class="bg-white border border-news-line">
        <div class="px-6 py-4 border-b border-news-line">
            <h3 class="text-lg font-semibold text-news-ink">Artikel per Penulis</h3>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-news-line">
                    <thead class="bg-news-paper">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">Penulis</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">Jumlah Artikel</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">Bergabung</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-news-line">
                        @forelse($reports['articles_by_author'] as $author)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-news-ink">{{ $author->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-news-muted">{{ $author->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-news-paper text-news-ink">
                                    {{ $author->articles_count }} artikel
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-news-muted">
                                {{ $author->created_at->format('d-m-Y') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-news-muted">
                                Belum ada data penulis
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Monthly Statistics -->
    <div class="bg-white border border-news-line">
        <div class="px-6 py-4 border-b border-news-line">
            <h3 class="text-lg font-semibold text-news-ink">Statistik Bulanan (12 Bulan Terakhir)</h3>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-news-line">
                    <thead class="bg-news-paper">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">Bulan</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">Artikel</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">Users</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">Growth</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-news-line">
                        @forelse($reports['monthly_stats'] as $stat)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-news-ink">{{ $stat['month'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-news-ink">{{ $stat['articles'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-news-ink">{{ $stat['users'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $prevMonth = $loop->index > 0 ? $reports['monthly_stats'][$loop->index - 1] : null;
                                    $growth = $prevMonth ? (($stat['articles'] - $prevMonth['articles']) / max($prevMonth['articles'], 1)) * 100 : 0;
                                @endphp
                                @if($growth > 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-800">
                                        <i class="fas fa-arrow-up mr-1"></i>
                                        +{{ number_format($growth, 1) }}%
                                    </span>
                                @elseif($growth < 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-50 text-news-accent">
                                        <i class="fas fa-arrow-down mr-1"></i>
                                        {{ number_format($growth, 1) }}%
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-news-paper text-news-ink">
                                        <i class="fas fa-minus mr-1"></i>
                                        0%
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-news-muted">
                                Belum ada data statistik
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- System Information -->
    <div class="bg-white border border-news-line">
        <div class="px-6 py-4 border-b border-news-line">
            <h3 class="text-lg font-semibold text-news-ink">Informasi Sistem</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-news-paper rounded-lg p-4">
                    <h4 class="text-sm font-medium text-news-ink mb-2">Laravel Version</h4>
                    <p class="text-sm text-news-muted">{{ app()->version() }}</p>
                </div>
                
                <div class="bg-news-paper rounded-lg p-4">
                    <h4 class="text-sm font-medium text-news-ink mb-2">PHP Version</h4>
                    <p class="text-sm text-news-muted">{{ PHP_VERSION }}</p>
                </div>
                
                <div class="bg-news-paper rounded-lg p-4">
                    <h4 class="text-sm font-medium text-news-ink mb-2">Environment</h4>
                    <p class="text-sm text-news-muted">{{ app()->environment() }}</p>
                </div>
                
                <div class="bg-news-paper rounded-lg p-4">
                    <h4 class="text-sm font-medium text-news-ink mb-2">Database</h4>
                    <p class="text-sm text-news-muted">{{ config('database.default') }}</p>
                </div>
                
                <div class="bg-news-paper rounded-lg p-4">
                    <h4 class="text-sm font-medium text-news-ink mb-2">Cache Driver</h4>
                    <p class="text-sm text-news-muted">{{ config('cache.default') }}</p>
                </div>
                
                <div class="bg-news-paper rounded-lg p-4">
                    <h4 class="text-sm font-medium text-news-ink mb-2">Queue Driver</h4>
                    <p class="text-sm text-news-muted">{{ config('queue.default') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
