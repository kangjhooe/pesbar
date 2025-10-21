<!-- Comments Overview -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <!-- Total Comments -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 bg-blue-100 rounded-lg">
                <i class="fas fa-comments text-blue-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Komentar</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['comments'] }}</p>
            </div>
        </div>
    </div>

    <!-- Approved Comments -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 bg-green-100 rounded-lg">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Disetujui</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['comments'] - $stats['pending_comments'] }}</p>
            </div>
        </div>
    </div>

    <!-- Pending Comments -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 bg-yellow-100 rounded-lg">
                <i class="fas fa-clock text-yellow-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Menunggu Moderasi</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['pending_comments'] }}</p>
            </div>
        </div>
    </div>

    <!-- Comments This Month -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 bg-purple-100 rounded-lg">
                <i class="fas fa-calendar text-purple-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Bulan Ini</p>
                <p class="text-2xl font-bold text-gray-900">{{ $monthlyStats['comments_this_month'] }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Comment Management Actions -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Manajemen Komentar</h3>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <a href="#" class="flex items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
            <i class="fas fa-clock text-yellow-600 text-xl mr-3"></i>
            <div>
                <p class="font-medium text-yellow-900">Moderasi</p>
                <p class="text-sm text-yellow-700">Review komentar baru</p>
            </div>
        </a>
        <a href="#" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
            <i class="fas fa-check text-green-600 text-xl mr-3"></i>
            <div>
                <p class="font-medium text-green-900">Disetujui</p>
                <p class="text-sm text-green-700">Komentar yang disetujui</p>
            </div>
        </a>
        <a href="#" class="flex items-center p-4 bg-red-50 rounded-lg hover:bg-red-100 transition-colors">
            <i class="fas fa-ban text-red-600 text-xl mr-3"></i>
            <div>
                <p class="font-medium text-red-900">Ditolak</p>
                <p class="text-sm text-red-700">Komentar yang ditolak</p>
            </div>
        </a>
        <a href="#" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
            <i class="fas fa-list text-blue-600 text-xl mr-3"></i>
            <div>
                <p class="font-medium text-blue-900">Semua Komentar</p>
                <p class="text-sm text-blue-700">Lihat semua komentar</p>
            </div>
        </a>
    </div>
</div>

<!-- Recent Comments -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900">Komentar Terbaru</h3>
        <div class="flex space-x-2">
            <button class="px-3 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                Semua ({{ $recentComments->count() }})
            </button>
            <button class="px-3 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                Pending ({{ $recentComments->where('is_approved', false)->count() }})
            </button>
            <button class="px-3 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                Disetujui ({{ $recentComments->where('is_approved', true)->count() }})
            </button>
        </div>
    </div>
    <div class="p-6">
        @if($recentComments->count() > 0)
            <div class="space-y-4">
                @foreach($recentComments as $comment)
                    <div class="flex items-start space-x-4 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-gray-600"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-2 mb-2">
                                <h4 class="text-sm font-medium text-gray-900">{{ $comment->name }}</h4>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $comment->is_approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $comment->is_approved ? 'Disetujui' : 'Pending' }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-700 mb-2">{{ Str::limit($comment->content, 150) }}</p>
                            <div class="flex items-center space-x-4 text-xs text-gray-500">
                                <span>
                                    <i class="fas fa-newspaper text-gray-400 mr-1"></i>
                                    {{ $comment->article->title ?? 'Artikel tidak ditemukan' }}
                                </span>
                                <span>
                                    <i class="fas fa-clock text-gray-400 mr-1"></i>
                                    {{ $comment->created_at->format('d-m-Y H:i') }}
                                </span>
                                @if($comment->email)
                                    <span>
                                        <i class="fas fa-envelope text-gray-400 mr-1"></i>
                                        {{ $comment->email }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="flex-shrink-0 flex items-center space-x-1">
                            @if(!$comment->is_approved)
                                <button class="p-2 text-green-600 hover:bg-green-100 rounded-lg transition-colors" title="Setujui">
                                    <i class="fas fa-check text-sm"></i>
                                </button>
                            @endif
                            <button class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors" title="Hapus">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                            <button class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors" title="Lihat Detail">
                                <i class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Load More Button -->
            <div class="mt-6 text-center">
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Lihat Semua Komentar
                </button>
            </div>
        @else
            <div class="text-center py-8">
                <i class="fas fa-comments text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-600 mb-4">Belum ada komentar</p>
                <p class="text-sm text-gray-500">Komentar dari pembaca akan muncul di sini</p>
            </div>
        @endif
    </div>
</div>

<!-- Comment Statistics -->
<div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistik Komentar</h3>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Most Commented Articles -->
        <div>
            <h4 class="text-sm font-medium text-gray-600 mb-3">Artikel dengan Komentar Terbanyak</h4>
            <div class="space-y-2">
                @php
                    $mostCommented = \App\Models\Article::withCount('comments')
                        ->orderBy('comments_count', 'desc')
                        ->limit(5)
                        ->get();
                @endphp
                @forelse($mostCommented as $article)
                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                        <span class="text-sm text-gray-900 truncate">{{ $article->title }}</span>
                        <span class="text-xs text-gray-500">{{ $article->comments_count }} komentar</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Belum ada data</p>
                @endforelse
            </div>
        </div>

        <!-- Comment Activity -->
        <div>
            <h4 class="text-sm font-medium text-gray-600 mb-3">Aktivitas Komentar</h4>
            <div class="space-y-2">
                <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                    <span class="text-sm text-gray-900">Hari ini</span>
                    <span class="text-xs text-gray-500">
                        {{ \App\Models\Comment::whereDate('created_at', today())->count() }} komentar
                    </span>
                </div>
                <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                    <span class="text-sm text-gray-900">Minggu ini</span>
                    <span class="text-xs text-gray-500">
                        {{ \App\Models\Comment::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count() }} komentar
                    </span>
                </div>
                <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                    <span class="text-sm text-gray-900">Bulan ini</span>
                    <span class="text-xs text-gray-500">{{ $monthlyStats['comments_this_month'] }} komentar</span>
                </div>
            </div>
        </div>

        <!-- Comment Moderation -->
        <div>
            <h4 class="text-sm font-medium text-gray-600 mb-3">Moderasi</h4>
            <div class="space-y-2">
                <div class="flex items-center justify-between p-2 bg-yellow-50 rounded">
                    <span class="text-sm text-gray-900">Menunggu Review</span>
                    <span class="text-xs text-yellow-600 font-medium">{{ $stats['pending_comments'] }}</span>
                </div>
                <div class="flex items-center justify-between p-2 bg-green-50 rounded">
                    <span class="text-sm text-gray-900">Disetujui</span>
                    <span class="text-xs text-green-600 font-medium">{{ $stats['comments'] - $stats['pending_comments'] }}</span>
                </div>
                <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                    <span class="text-sm text-gray-900">Tingkat Persetujuan</span>
                    <span class="text-xs text-gray-500">
                        @php
                            $approvalRate = $stats['comments'] > 0 ? round((($stats['comments'] - $stats['pending_comments']) / $stats['comments']) * 100, 1) : 0;
                        @endphp
                        {{ $approvalRate }}%
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
