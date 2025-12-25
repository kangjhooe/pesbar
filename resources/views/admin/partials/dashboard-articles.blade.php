<!-- Article Management Overview -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Published Articles -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 bg-green-100 rounded-lg">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Artikel Dipublikasi</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['published_articles'] }}</p>
            </div>
        </div>
    </div>

    <!-- Draft Articles -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 bg-yellow-100 rounded-lg">
                <i class="fas fa-edit text-yellow-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Artikel Draft</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['draft_articles'] }}</p>
            </div>
        </div>
    </div>

    <!-- Total Views -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 bg-blue-100 rounded-lg">
                <i class="fas fa-eye text-blue-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total Views</p>
                <p class="text-2xl font-bold text-gray-900">{{ $popularArticles->sum('views') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Article Management Actions -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Manajemen Artikel</h3>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <a href="#" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
            <i class="fas fa-plus text-blue-600 text-xl mr-3"></i>
            <div>
                <p class="font-medium text-blue-900">Tambah Artikel</p>
                <p class="text-sm text-blue-700">Buat artikel baru</p>
            </div>
        </a>
        <a href="#" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
            <i class="fas fa-list text-green-600 text-xl mr-3"></i>
            <div>
                <p class="font-medium text-green-900">Semua Artikel</p>
                <p class="text-sm text-green-700">Lihat semua artikel</p>
            </div>
        </a>
        <a href="#" class="flex items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
            <i class="fas fa-edit text-yellow-600 text-xl mr-3"></i>
            <div>
                <p class="font-medium text-yellow-900">Draft Artikel</p>
                <p class="text-sm text-yellow-700">Kelola draft</p>
            </div>
        </a>
        <a href="#" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
            <i class="fas fa-folder text-purple-600 text-xl mr-3"></i>
            <div>
                <p class="font-medium text-purple-900">Kategori</p>
                <p class="text-sm text-purple-700">Kelola kategori</p>
            </div>
        </a>
    </div>
</div>

<!-- Recent Articles -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
        <h3 class="text-lg font-semibold text-gray-900">Artikel Terbaru</h3>
        <div class="flex space-x-2">
            <button class="px-3 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                Semua ({{ $recentArticles->count() }})
            </button>
            <button class="px-3 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">
                Dipublikasi ({{ $recentArticles->where('status', 'published')->count() }})
            </button>
            <button class="px-3 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                Draft ({{ $recentArticles->where('status', 'draft')->count() }})
            </button>
        </div>
    </div>
    <div class="p-6">
        @if($recentArticles->count() > 0)
            <div class="space-y-4">
                @foreach($recentArticles->take(10) as $article)
                    <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex-shrink-0">
                            @if($article->featured_image)
                                <img src="{{ asset('storage/' . $article->featured_image) }}" alt="{{ $article->title }}" class="w-16 h-16 object-cover rounded-lg">
                            @else
                                <div class="w-16 h-16 bg-gray-300 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-image text-gray-500"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium text-gray-900 truncate">
                                {{ $article->title }}
                            </h4>
                            <div class="flex items-center space-x-4 mt-1">
                                <p class="text-sm text-gray-600">
                                    <i class="fas fa-folder text-gray-400 mr-1"></i>
                                    {{ $article->category->name ?? 'Tanpa Kategori' }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    <i class="fas fa-user text-gray-400 mr-1"></i>
                                    <span class="text-gray-500">Penulis:</span>
                                    <span class="font-medium">{{ $article->author->name ?? 'Sistem' }}</span>
                                    @if($article->author)
                                        <x-user-role-badge :user="$article->author" size="xs" class="ml-1" />
                                    @endif
                                </p>
                                <p class="text-sm text-gray-600">
                                    <i class="fas fa-eye text-gray-400 mr-1"></i>
                                    {{ $article->views ?? 0 }} views
                                </p>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="fas fa-clock text-gray-400 mr-1"></i>
                                {{ $article->created_at->format('d-m-Y H:i') }}
                            </p>
                        </div>
                        <div class="flex-shrink-0 flex items-center space-x-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $article->status === 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $article->status === 'published' ? 'Dipublikasi' : 'Draft' }}
                            </span>
                            <div class="flex space-x-1">
                                <button class="p-1 text-gray-400 hover:text-blue-600 transition-colors" title="Edit">
                                    <i class="fas fa-edit text-sm"></i>
                                </button>
                                <button class="p-1 text-gray-400 hover:text-red-600 transition-colors" title="Hapus">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Load More Button -->
            <div class="mt-6 text-center">
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Lihat Semua Artikel
                </button>
            </div>
        @else
            <div class="text-center py-8">
                <i class="fas fa-newspaper text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-600 mb-4">Belum ada artikel</p>
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Buat Artikel Pertama
                </button>
            </div>
        @endif
    </div>
</div>
