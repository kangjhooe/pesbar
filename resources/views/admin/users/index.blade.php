@extends('layouts.admin-simple')

@section('page-title', 'Manajemen Pengguna')
@section('page-subtitle', 'Kelola pengguna dan peran akses')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Pengguna</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $users->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-user-check text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Penulis</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $users->where('role', 'penulis')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-user-edit text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Editor</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $users->where('role', 'editor')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-user-shield text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Admin</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $users->where('role', 'admin')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <div>
        <h3 class="text-lg font-medium text-gray-900 mb-4">Daftar Pengguna</h3>

        <x-admin.table :paginator="$users" :bulk="true" bulk-id="users-bulk">
            <x-slot:bulkBar>
                <x-admin.bulk-bar
                    :action="route('admin.users.bulk')"
                    bulk-id="users-bulk"
                    :options="['verify' => 'Verifikasi', 'unverify' => 'Batal Verifikasi', 'upgrade' => 'Upgrade ke Penulis']"
                />
            </x-slot:bulkBar>

            <x-slot:head>
                <x-admin.checkbox all bulk-id="users-bulk" />
                <x-admin.th :sortable="false" label="#" align="center" class="w-14" />
                <x-admin.th column="name" label="Pengguna" />
                <x-admin.th column="role" label="Role" />
                <x-admin.th column="verified" label="Status" />
                <x-admin.th column="created_at" label="Bergabung" />
                <x-admin.th :sortable="false" label="Aksi" align="right" />
            </x-slot:head>

            @forelse($users as $user)
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <x-admin.checkbox :value="$user->id" bulk-id="users-bulk" name="ids[]" />
                    <x-admin.td-number :index="$users->firstItem() + $loop->index" />
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
                                <div class="flex items-center gap-2 flex-wrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    <x-user-role-badge :user="$user" size="xs" />
                                </div>
                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        @php
                            $roleColors = [
                                'admin' => 'bg-red-100 text-red-800',
                                'editor' => 'bg-yellow-100 text-yellow-800',
                                'penulis' => 'bg-green-100 text-green-800',
                                'user' => 'bg-gray-100 text-gray-800'
                            ];
                        @endphp
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $roleColors[$user->role] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($user->role) }}
                        </span>
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
                        @if($user->role === 'user')
                            <x-admin.action-icon
                                :href="route('admin.users.upgrade', $user)"
                                method="POST"
                                icon="fas fa-arrow-up"
                                color="green"
                                title="Upgrade ke Penulis"
                                confirm="Upgrade pengguna ini menjadi penulis?"
                            />
                        @endif
                        <x-admin.action-icon
                            :href="route('admin.users.toggle-verified', $user)"
                            method="POST"
                            :icon="$user->verified ? 'fas fa-times' : 'fas fa-check'"
                            :color="$user->verified ? 'amber' : 'blue'"
                            :title="$user->verified ? 'Batal Verifikasi' : 'Verifikasi'"
                            :confirm="($user->verified ? 'Batalkan' : 'Verifikasi') . ' pengguna ini?'"
                        />
                    </x-admin.actions>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-12 text-center text-gray-500">
                        <i class="fas fa-users text-4xl mb-4 text-gray-300"></i>
                        <p class="text-lg font-medium text-gray-700">Belum Ada Pengguna</p>
                        <p class="text-sm">Belum ada pengguna yang terdaftar dalam sistem.</p>
                    </td>
                </tr>
            @endforelse
        </x-admin.table>
    </div>
</div>
@endsection
