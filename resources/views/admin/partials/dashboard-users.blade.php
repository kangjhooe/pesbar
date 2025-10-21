<!-- Users Overview -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <!-- Total Users -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 bg-blue-100 rounded-lg">
                <i class="fas fa-users text-blue-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Total User</p>
                <p class="text-2xl font-bold text-gray-900">{{ $stats['users'] }}</p>
            </div>
        </div>
    </div>

    <!-- Active Users -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 bg-green-100 rounded-lg">
                <i class="fas fa-user-check text-green-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">User Aktif</p>
                <p class="text-2xl font-bold text-gray-900">
                    @php
                        $activeUsers = \App\Models\User::where('created_at', '>=', now()->subDays(30))->count();
                    @endphp
                    {{ $activeUsers }}
                </p>
            </div>
        </div>
    </div>

    <!-- Admin Users -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 bg-purple-100 rounded-lg">
                <i class="fas fa-user-shield text-purple-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">Admin</p>
                <p class="text-2xl font-bold text-gray-900">
                    @php
                        $adminUsers = \App\Models\User::where('role', 'admin')->count();
                    @endphp
                    {{ $adminUsers }}
                </p>
            </div>
        </div>
    </div>

    <!-- New Users This Month -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center">
            <div class="p-3 bg-yellow-100 rounded-lg">
                <i class="fas fa-user-plus text-yellow-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <p class="text-sm font-medium text-gray-600">User Baru</p>
                <p class="text-2xl font-bold text-gray-900">
                    @php
                        $newUsers = \App\Models\User::whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)
                            ->count();
                    @endphp
                    {{ $newUsers }}
                </p>
            </div>
        </div>
    </div>
</div>

<!-- User Management Actions -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Manajemen User</h3>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <a href="#" class="flex items-center p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors">
            <i class="fas fa-user-plus text-blue-600 text-xl mr-3"></i>
            <div>
                <p class="font-medium text-blue-900">Tambah User</p>
                <p class="text-sm text-blue-700">Buat user baru</p>
            </div>
        </a>
        <a href="#" class="flex items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition-colors">
            <i class="fas fa-list text-green-600 text-xl mr-3"></i>
            <div>
                <p class="font-medium text-green-900">Semua User</p>
                <p class="text-sm text-green-700">Lihat semua user</p>
            </div>
        </a>
        <a href="#" class="flex items-center p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
            <i class="fas fa-user-shield text-purple-600 text-xl mr-3"></i>
            <div>
                <p class="font-medium text-purple-900">Admin</p>
                <p class="text-sm text-purple-700">Kelola admin</p>
            </div>
        </a>
        <a href="#" class="flex items-center p-4 bg-yellow-50 rounded-lg hover:bg-yellow-100 transition-colors">
            <i class="fas fa-key text-yellow-600 text-xl mr-3"></i>
            <div>
                <p class="font-medium text-yellow-900">Reset Password</p>
                <p class="text-sm text-yellow-700">Reset password user</p>
            </div>
        </a>
    </div>
</div>

<!-- Recent Users -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-8">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">User Terbaru</h3>
    </div>
    <div class="p-6">
        @php
            $recentUsers = \App\Models\User::latest()->limit(10)->get();
        @endphp
        @if($recentUsers->count() > 0)
            <div class="space-y-4">
                @foreach($recentUsers as $user)
                    <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-gray-600"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-medium text-gray-900">{{ $user->name }}</h4>
                            <p class="text-sm text-gray-600">{{ $user->email }}</p>
                            <p class="text-xs text-gray-500">
                                <i class="fas fa-clock text-gray-400 mr-1"></i>
                                Bergabung {{ $user->created_at->format('d-m-Y H:i') }}
                            </p>
                        </div>
                        <div class="flex-shrink-0 flex items-center space-x-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($user->role ?? 'user') }}
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
        @else
            <div class="text-center py-8">
                <i class="fas fa-users text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-600">Belum ada user</p>
            </div>
        @endif
    </div>
</div>

<!-- System Settings -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Pengaturan Sistem</h3>
    </div>
    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Site Settings -->
            <div>
                <h4 class="text-sm font-medium text-gray-600 mb-3">Pengaturan Website</h4>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Judul Website</p>
                            <p class="text-xs text-gray-500">Portal Berita Kabupaten Pesisir Barat</p>
                        </div>
                        <button class="p-1 text-gray-400 hover:text-blue-600 transition-colors">
                            <i class="fas fa-edit text-sm"></i>
                        </button>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Deskripsi</p>
                            <p class="text-xs text-gray-500">Portal berita resmi Kabupaten Pesisir Barat</p>
                        </div>
                        <button class="p-1 text-gray-400 hover:text-blue-600 transition-colors">
                            <i class="fas fa-edit text-sm"></i>
                        </button>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Email Admin</p>
                            <p class="text-xs text-gray-500">admin@pesisirbarat.go.id</p>
                        </div>
                        <button class="p-1 text-gray-400 hover:text-blue-600 transition-colors">
                            <i class="fas fa-edit text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Content Settings -->
            <div>
                <h4 class="text-sm font-medium text-gray-600 mb-3">Pengaturan Konten</h4>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Moderasi Komentar</p>
                            <p class="text-xs text-gray-500">Otomatis</p>
                        </div>
                        <button class="p-1 text-gray-400 hover:text-blue-600 transition-colors">
                            <i class="fas fa-edit text-sm"></i>
                        </button>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Artikel per Halaman</p>
                            <p class="text-xs text-gray-500">12 artikel</p>
                        </div>
                        <button class="p-1 text-gray-400 hover:text-blue-600 transition-colors">
                            <i class="fas fa-edit text-sm"></i>
                        </button>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Newsletter</p>
                            <p class="text-xs text-gray-500">Aktif</p>
                        </div>
                        <button class="p-1 text-gray-400 hover:text-blue-600 transition-colors">
                            <i class="fas fa-edit text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Information -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <h4 class="text-sm font-medium text-gray-600 mb-3">Informasi Sistem</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500">Versi Laravel</p>
                    <p class="text-sm font-medium text-gray-900">{{ app()->version() }}</p>
                </div>
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500">PHP Version</p>
                    <p class="text-sm font-medium text-gray-900">{{ PHP_VERSION }}</p>
                </div>
                <div class="p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs text-gray-500">Environment</p>
                    <p class="text-sm font-medium text-gray-900">{{ app()->environment() }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
