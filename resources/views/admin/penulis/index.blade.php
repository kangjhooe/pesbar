@extends('layouts.admin-simple')

@section('title', 'Manajemen Penulis - Admin Panel')
@section('page-title', 'Manajemen Penulis')
@section('page-subtitle', 'Kelola penulis dan kontributor')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-user-edit text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Penulis</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $penulis->total() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-newspaper text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Artikel</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $penulis->sum(function($user) { return $user->articles()->count(); }) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-eye text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Views</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($penulis->sum(function($user) { return $user->articles()->sum('views'); })) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                    <i class="fas fa-star text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Penulis Aktif</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $penulis->where('created_at', '>=', now()->subDays(30))->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Penulis Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Penulis
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Kontak
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Statistik
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Bergabung
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($penulis as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    @if($user->profile && $user->profile->avatar)
                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $user->profile->avatar) }}" alt="{{ $user->name }}">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-700">{{ substr($user->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-500">@{{ $user->username }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $user->email }}</div>
                            @if($user->profile && $user->profile->phone)
                                <div class="text-sm text-gray-500">{{ $user->profile->phone }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                <div class="flex items-center space-x-4">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-newspaper mr-1"></i>
                                        {{ $user->articles()->count() }} artikel
                                    </span>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-eye mr-1"></i>
                                        {{ number_format($user->articles()->sum('views')) }} views
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center space-x-2">
                                @if($user->verified)
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Terverifikasi
                                    </span>
                                @else
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i>
                                        Belum Terverifikasi
                                    </span>
                                @endif
                                
                                @if($user->provider === 'google')
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        <i class="fab fa-google mr-1"></i>
                                        Google
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $user->created_at->format('d-m-Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('penulis.public-profile', $user->username) }}" 
                                   class="text-blue-600 hover:text-blue-900" title="Lihat Profil">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                <form method="POST" action="{{ route('admin.users.toggle-verified', $user) }}" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="text-{{ $user->verified ? 'yellow' : 'green' }}-600 hover:text-{{ $user->verified ? 'yellow' : 'green' }}-900" 
                                            title="{{ $user->verified ? 'Batal Verifikasi' : 'Verifikasi' }}">
                                        <i class="fas fa-{{ $user->verified ? 'times' : 'check' }}"></i>
                                    </button>
                                </form>
                                
                                <form method="POST" action="{{ route('admin.penulis.demote', $user) }}" class="inline">
                                    @csrf
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-900" 
                                            title="Turunkan ke User"
                                            onclick="return confirm('Apakah Anda yakin ingin menurunkan penulis ini menjadi user?')">
                                        <i class="fas fa-arrow-down"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <i class="fas fa-user-edit text-4xl mb-4"></i>
                                <p class="text-lg font-medium">Belum Ada Penulis</p>
                                <p class="text-sm">Penulis akan muncul di sini setelah mendaftar</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($penulis->hasPages())
    <div class="flex justify-center">
        {{ $penulis->links() }}
    </div>
    @endif
</div>
@endsection
