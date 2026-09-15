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
                    <p class="text-2xl font-semibold text-gray-900">{{ $penulis->sum('articles_count') }}</p>
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
                    <p class="text-2xl font-semibold text-gray-900">{{ number_format($penulis->sum('articles_sum_views')) }}</p>
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

    <x-admin.table :paginator="$penulis" :bulk="true" bulk-id="penulis-bulk">
        <x-slot:bulkBar>
            <x-admin.bulk-bar
                :action="route('admin.penulis.bulk')"
                bulk-id="penulis-bulk"
                :options="['verify' => 'Verifikasi', 'unverify' => 'Batal Verifikasi', 'demote' => 'Turunkan ke User']"
            />
        </x-slot:bulkBar>

        <x-slot:head>
            <x-admin.checkbox all bulk-id="penulis-bulk" />
            <x-admin.th :sortable="false" label="#" align="center" class="w-14" />
            <x-admin.th column="name" label="Penulis" />
            <x-admin.th :sortable="false" label="Kontak" />
            <x-admin.th column="articles_count" label="Statistik" />
            <x-admin.th column="verified" label="Status" />
            <x-admin.th column="created_at" label="Bergabung" />
            <x-admin.th :sortable="false" label="Aksi" align="right" />
        </x-slot:head>

        @forelse($penulis as $user)
            <tr class="hover:bg-slate-50/80 transition-colors">
                <x-admin.checkbox :value="$user->id" bulk-id="penulis-bulk" name="ids[]" />
                <x-admin.td-number :index="$penulis->firstItem() + $loop->index" />
                <td class="px-4 py-4 whitespace-nowrap">
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
                <td class="px-4 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ $user->email }}</div>
                    @if($user->profile && $user->profile->phone)
                        <div class="text-sm text-gray-500">{{ $user->profile->phone }}</div>
                    @endif
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-newspaper mr-1"></i>
                            {{ $user->articles_count }} artikel
                        </span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-eye mr-1"></i>
                            {{ number_format($user->articles_sum_views ?? 0) }} views
                        </span>
                    </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
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
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $user->created_at->format('d-m-Y') }}
                </td>
                <x-admin.actions>
                    <x-admin.action-icon
                        :href="route('penulis.public-profile', $user->username)"
                        icon="fas fa-eye"
                        color="blue"
                        title="Lihat Profil"
                    />
                    <x-admin.action-icon
                        :href="route('admin.users.toggle-verified', $user)"
                        method="POST"
                        :icon="$user->verified ? 'fas fa-times' : 'fas fa-check'"
                        :color="$user->verified ? 'amber' : 'green'"
                        :title="$user->verified ? 'Batal Verifikasi' : 'Verifikasi'"
                    />
                    <x-admin.action-icon
                        :href="route('admin.penulis.demote', $user)"
                        method="POST"
                        icon="fas fa-arrow-down"
                        color="red"
                        title="Turunkan ke User"
                        confirm="Apakah Anda yakin ingin menurunkan penulis ini menjadi user?"
                    />
                </x-admin.actions>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="px-4 py-12 text-center text-gray-500">
                    <i class="fas fa-user-edit text-4xl mb-4 text-gray-300"></i>
                    <p class="text-lg font-medium text-gray-700">Belum Ada Penulis</p>
                    <p class="text-sm">Penulis akan muncul di sini setelah mendaftar</p>
                </td>
            </tr>
        @endforelse
    </x-admin.table>
</div>
@endsection
