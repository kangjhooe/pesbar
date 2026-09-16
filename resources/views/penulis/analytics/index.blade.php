@extends('layouts.penulis')

@section('title', 'Analytics Dashboard')
@section('page-title', 'Analytics Dashboard')
@section('page-subtitle', 'Pantau performa artikel Anda')

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div>
 <!-- Header -->
 <div class="mb-6">
 <div class="flex items-center justify-between flex-wrap gap-4">
 <div>
 <h1 class="text-3xl font-bold text-news-ink">Analytics Dashboard</h1>
 <p class="text-news-muted mt-1">Pantau performa dan engagement artikel Anda</p>
 </div>
 <div class="flex gap-2">
 <a href="{{ route('penulis.dashboard') }}" class="px-4 py-2 border border-news-line text-news-muted hover:text-news-ink hover:border-news-ink transition-colors">
 <i class="fas fa-arrow-left mr-2"></i>Kembali
 </a>
 </div>
 </div>
 </div>

 <!-- Period Filter -->
 <div class="mb-6">
 <form method="GET" action="{{ route('penulis.analytics') }}" class="flex gap-2">
 <select name="days" class="px-4 py-2 border border-news-line rounded-lg focus:outline-none focus:ring-2 focus:ring-news-accent/30 focus:border-news-accent">
 <option value="7" {{ $days == 7 ? 'selected' : '' }}>7 Hari Terakhir</option>
 <option value="30" {{ $days == 30 ? 'selected' : '' }}>30 Hari Terakhir</option>
 <option value="90" {{ $days == 90 ? 'selected' : '' }}>90 Hari Terakhir</option>
 <option value="365" {{ $days == 365 ? 'selected' : '' }}>1 Tahun Terakhir</option>
 </select>
 <button type="submit" class="px-4 py-2 bg-news-ink hover:bg-news-accent text-white rounded-lg transition">
 <i class="fas fa-filter mr-2"></i>Filter
 </button>
 </form>
 </div>

 <!-- Stats Cards -->
 <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
 <div class="bg-white border border-news-line p-6 border-l-4 border-news-accent">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm text-news-muted mb-1">Total Artikel</p>
 <p class="text-3xl font-bold text-news-ink">{{ number_format($stats['total_articles']) }}</p>
 </div>
 <div class="w-12 h-12 bg-news-paper rounded-full flex items-center justify-center">
 <i class="fas fa-newspaper text-news-accent text-xl"></i>
 </div>
 </div>
 </div>

 <div class="bg-white border border-news-line p-6 border-l-4 border-news-ink">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm text-news-muted mb-1">Total Views</p>
 <p class="text-3xl font-bold text-news-ink">{{ number_format($stats['total_views']) }}</p>
 </div>
 <div class="w-12 h-12 bg-news-paper rounded-full flex items-center justify-center">
 <i class="fas fa-eye text-news-accent text-xl"></i>
 </div>
 </div>
 </div>

 <div class="bg-white border border-news-line p-6 border-l-4 border-news-ink">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm text-news-muted mb-1">Total Komentar</p>
 <p class="text-3xl font-bold text-news-ink">{{ number_format($stats['total_comments']) }}</p>
 </div>
 <div class="w-12 h-12 bg-news-paper rounded-full flex items-center justify-center">
 <i class="fas fa-comments text-news-accent text-xl"></i>
 </div>
 </div>
 </div>

 <div class="bg-white border border-news-line p-6 border-l-4 border-news-accent">
 <div class="flex items-center justify-between">
 <div>
 <p class="text-sm text-news-muted mb-1">Engagement Rate</p>
 <p class="text-3xl font-bold text-news-ink">{{ number_format($stats['engagement_rate'], 2) }}%</p>
 </div>
 <div class="w-12 h-12 bg-news-paper rounded-full flex items-center justify-center">
 <i class="fas fa-chart-line text-news-accent text-xl"></i>
 </div>
 </div>
 </div>
 </div>

 <!-- Charts Row -->
 <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
 <!-- Articles Published Over Time Chart -->
 <div class="bg-white border border-news-line p-6">
 <h3 class="text-lg font-semibold text-news-ink mb-4">Artikel Diterbitkan & Total Views</h3>
 <div style="height: 300px; position: relative;">
 <canvas id="viewsChart"></canvas>
 </div>
 </div>

 <!-- Category Performance Chart -->
 <div class="bg-white border border-news-line p-6">
 <h3 class="text-lg font-semibold text-news-ink mb-4">Performance by Category</h3>
 <div style="height: 300px; position: relative;">
 <canvas id="categoryChart"></canvas>
 </div>
 </div>
 </div>

 <!-- Top Performing Articles & Category Performance in Grid -->
 <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
 <!-- Top Performing Articles -->
 <div class="bg-white border border-news-line p-6">
 <h3 class="text-lg font-semibold text-news-ink mb-4">Top Performing Articles</h3>
 <div class="overflow-x-auto">
 <table class="min-w-full divide-y divide-news-line">
 <thead class="bg-news-paper">
 <tr>
 <th class="px-4 py-2 text-left text-xs font-medium text-news-muted uppercase tracking-wider">Artikel</th>
 <th class="px-4 py-2 text-left text-xs font-medium text-news-muted uppercase tracking-wider">Views</th>
 <th class="px-4 py-2 text-left text-xs font-medium text-news-muted uppercase tracking-wider">Komentar</th>
 <th class="px-4 py-2 text-left text-xs font-medium text-news-muted uppercase tracking-wider">Aksi</th>
 </tr>
 </thead>
 <tbody class="bg-white divide-y divide-news-line">
 @forelse($topArticles as $article)
 <tr class="hover:bg-news-paper">
 <td class="px-4 py-3">
 <div class="text-sm font-medium text-news-ink line-clamp-2" title="{{ $article['title'] }}">{{ $article['title'] }}</div>
 <div class="text-xs text-news-muted mt-1">{{ $article['category'] }}</div>
 </td>
 <td class="px-4 py-3 text-sm text-news-ink">{{ number_format($article['views']) }}</td>
 <td class="px-4 py-3 text-sm text-news-ink">{{ number_format($article['comments']) }}</td>
 <td class="px-4 py-3 text-sm font-medium">
 <a href="{{ route('penulis.articles.show', $article['id']) }}" class="text-news-accent hover:text-news-ink" title="Lihat Detail">
 <i class="fas fa-eye"></i>
 </a>
 </td>
 </tr>
 @empty
 <tr>
 <td colspan="4" class="px-4 py-4 text-center text-news-muted text-sm">Tidak ada data</td>
 </tr>
 @endforelse
 </tbody>
 </table>
 </div>
 </div>

 <!-- Category Performance Table -->
 @if(count($categoryPerformance) > 0)
 <div class="bg-white border border-news-line p-6">
 <h3 class="text-lg font-semibold text-news-ink mb-4">Performance by Category</h3>
 <div class="overflow-x-auto">
 <table class="min-w-full divide-y divide-news-line">
 <thead class="bg-news-paper">
 <tr>
 <th class="px-4 py-2 text-left text-xs font-medium text-news-muted uppercase tracking-wider">Kategori</th>
 <th class="px-4 py-2 text-left text-xs font-medium text-news-muted uppercase tracking-wider">Artikel</th>
 <th class="px-4 py-2 text-left text-xs font-medium text-news-muted uppercase tracking-wider">Views</th>
 <th class="px-4 py-2 text-left text-xs font-medium text-news-muted uppercase tracking-wider">Engagement</th>
 </tr>
 </thead>
 <tbody class="bg-white divide-y divide-news-line">
 @foreach($categoryPerformance as $cat)
 <tr class="hover:bg-news-paper">
 <td class="px-4 py-3 text-sm font-medium text-news-ink">{{ $cat['category_name'] }}</td>
 <td class="px-4 py-3 text-sm text-news-ink">{{ $cat['total_articles'] }}</td>
 <td class="px-4 py-3 text-sm text-news-ink">{{ number_format($cat['total_views']) }}</td>
 <td class="px-4 py-3 text-sm text-news-ink">{{ number_format($cat['engagement_rate'], 1) }}%</td>
 </tr>
 @endforeach
 </tbody>
 </table>
 </div>
 </div>
 @endif
 </div>
</div>

@push('scripts')
<script>
 // Articles Published & Views Over Time Chart
 const viewsCtx = document.getElementById('viewsChart').getContext('2d');
 const viewsChart = new Chart(viewsCtx, {
 type: 'line',
 data: {
 labels: {!! json_encode(array_column($viewsOverTime, 'date_formatted')) !!},
 datasets: [
 {
 label: 'Total Views',
 data: {!! json_encode(array_column($viewsOverTime, 'views')) !!},
 borderColor: 'rgb(185, 28, 28)',
 backgroundColor: 'rgba(185, 28, 28, 0.1)',
 tension: 0.4,
 fill: true,
 yAxisID: 'y'
 },
 {
 label: 'Artikel Diterbitkan',
 data: {!! json_encode(array_column($viewsOverTime, 'articles')) !!},
 borderColor: 'rgb(10, 10, 10)',
 backgroundColor: 'rgba(10, 10, 10, 0.08)',
 tension: 0.4,
 fill: false,
 yAxisID: 'y1',
 type: 'bar'
 }
 ]
 },
 options: {
 responsive: true,
 maintainAspectRatio: false,
 interaction: {
 mode: 'index',
 intersect: false,
 },
 plugins: {
 legend: {
 display: true,
 position: 'top'
 },
 tooltip: {
 enabled: true
 }
 },
 scales: {
 y: {
 type: 'linear',
 display: true,
 position: 'left',
 beginAtZero: true,
 title: {
 display: true,
 text: 'Total Views'
 }
 },
 y1: {
 type: 'linear',
 display: true,
 position: 'right',
 beginAtZero: true,
 title: {
 display: true,
 text: 'Jumlah Artikel'
 },
 grid: {
 drawOnChartArea: false,
 },
 }
 }
 }
 });

 // Category Performance Chart
 const categoryCtx = document.getElementById('categoryChart');
 @if(count($categoryPerformance) > 0)
 const categoryChart = new Chart(categoryCtx.getContext('2d'), {
 type: 'bar',
 data: {
 labels: {!! json_encode(array_column($categoryPerformance, 'category_name')) !!},
 datasets: [{
 label: 'Total Views',
 data: {!! json_encode(array_column($categoryPerformance, 'total_views')) !!},
 backgroundColor: 'rgba(185, 28, 28, 0.85)',
 borderColor: 'rgb(185, 28, 28)',
 borderWidth: 1
 }]
 },
 options: {
 responsive: true,
 maintainAspectRatio: false,
 plugins: {
 legend: {
 display: true,
 position: 'top'
 }
 },
 scales: {
 y: {
 beginAtZero: true
 }
 }
 }
 });
 @else
 const ctx = categoryCtx.getContext('2d');
 ctx.font = '16px Arial';
 ctx.fillStyle = '#5c5c5c';
 ctx.textAlign = 'center';
 ctx.fillText('Tidak ada data kategori', categoryCtx.width / 2, categoryCtx.height / 2);
 @endif
</script>
@endpush
@endsection

