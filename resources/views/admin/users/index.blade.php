@extends('layouts.admin-simple')

@section('page-title', 'Manajemen Pengguna')
@section('page-subtitle', 'Kelola pengguna dan peran akses')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white border border-news-line p-6">
            <div class="flex items-center">
                <div class="p-3 bg-news-paper text-news-ink">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-news-muted">Total Pengguna</p>
                    <p class="text-2xl font-semibold text-news-ink">{{ $users->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-news-line p-6">
            <div class="flex items-center">
                <div class="p-3 bg-news-paper text-news-ink">
                    <i class="fas fa-user-check text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-news-muted">Penulis</p>
                    <p class="text-2xl font-semibold text-news-ink">{{ $users->where('role', 'penulis')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-news-line p-6">
            <div class="flex items-center">
                <div class="p-3 bg-news-paper text-news-ink">
                    <i class="fas fa-user-edit text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-news-muted">Editor</p>
                    <p class="text-2xl font-semibold text-news-ink">{{ $users->where('role', 'editor')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-news-line p-6">
            <div class="flex items-center">
                <div class="p-3 bg-news-paper text-news-ink">
                    <i class="fas fa-user-shield text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-news-muted">Admin</p>
                    <p class="text-2xl font-semibold text-news-ink">{{ $users->where('role', 'admin')->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <div>
        <h3 class="text-lg font-medium text-news-ink mb-4">Daftar Pengguna</h3>

        <x-admin.table :paginator="$users" :bulk="true" bulk-id="users-bulk">
            <x-slot:bulkBar>
                <x-admin.bulk-bar
                    :action="route('admin.users.bulk')"
                    bulk-id="users-bulk"
                :options="['unverify' => 'Cabut Verified → User']"
                />
            </x-slot:bulkBar>

            <x-slot:head>
                <x-admin.checkbox all bulk-id="users-bulk" />
                <x-admin.th :sortable="false" label="#" align="center" class="w-14 admin-col-optional" />
                <x-admin.th column="name" label="Pengguna" />
                <x-admin.th column="role" label="Role" class="admin-col-optional" />
                <x-admin.th column="verified" label="Status" />
                <x-admin.th column="created_at" label="Bergabung" class="admin-col-optional" />
                <x-admin.th :sortable="false" label="Aksi" align="right" />
            </x-slot:head>

            @forelse($users as $user)
                <tr class="hover:bg-news-paper transition-colors">
                    <x-admin.checkbox :value="$user->id" bulk-id="users-bulk" name="ids[]" />
                    <x-admin.td-number :index="$users->firstItem() + $loop->index" class="admin-col-optional" />
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                @if($user->profile && $user->profile->avatar)
                                    <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $user->profile->avatar) }}" alt="{{ $user->name }}">
                                @else
                                    <div class="h-10 w-10 rounded-full bg-news-paper flex items-center justify-center">
                                        <span class="text-sm font-medium text-news-ink">{{ substr($user->name, 0, 1) }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-4 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <div class="text-sm font-medium text-news-ink">{{ $user->name }}</div>
                                    <x-user-role-badge :user="$user" size="xs" />
                                    @if($user->isRedaksi())
                                        <span class="inline-flex px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide bg-news-ink text-white">
                                            Redaksi
                                        </span>
                                    @endif
                                </div>
                                <div class="text-sm text-news-muted truncate max-w-[12rem] sm:max-w-none">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap admin-col-optional">
                        @php
                            $roleColors = [
                                'admin' => 'bg-red-50 text-news-accent',
                                'editor' => 'bg-amber-50 text-amber-800',
                                'penulis' => 'bg-emerald-50 text-emerald-800',
                                'user' => 'bg-news-paper text-news-muted'
                            ];
                        @endphp
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $roleColors[$user->role] ?? 'bg-news-paper text-news-ink' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="flex items-center space-x-2">
                            @if($user->isRedaksi())
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-news-ink text-white">
                                    Redaksi
                                </span>
                            @elseif($user->isBanned())
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-50 text-news-accent">
                                    <i class="fas fa-ban mr-1"></i>
                                    Banned s/d {{ $user->banned_until->format('d/m/Y') }}
                                </span>
                            @elseif($user->verified)
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-emerald-50 text-emerald-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Terverifikasi
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-amber-50 text-amber-800">
                                    <i class="fas fa-clock mr-1"></i>
                                    Belum Terverifikasi
                                </span>
                            @endif

                            @if($user->provider === 'google')
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-news-paper text-news-ink">
                                    <i class="fab fa-google mr-1"></i>
                                    Google
                                </span>
                            @endif
                        </div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-news-muted admin-col-optional">
                        {{ $user->created_at->format('d-m-Y') }}
                    </td>
                    <x-admin.actions>
                        @if($user->role === 'user')
                            @if($user->isBanned())
                                <x-admin.action-icon
                                    :href="route('admin.users.lift-ban', $user)"
                                    method="POST"
                                    icon="fas fa-unlock"
                                    color="indigo"
                                    title="Buka ban"
                                    confirm="Buka ban {{ $user->name }} sekarang?"
                                />
                            @elseif($user->verification_request_status === 'pending')
                                <x-admin.action-icon
                                    :href="route('admin.verification-requests')"
                                    icon="fas fa-clipboard-check"
                                    color="blue"
                                    title="Ada pengajuan upgrade — buka daftar verifikasi"
                                />
                            @endif
                            @if(!$user->isBanned())
                                <x-admin.action-icon
                                    :href="route('admin.users.mark-redaksi', $user)"
                                    method="POST"
                                    icon="fas fa-id-badge"
                                    color="indigo"
                                    title="Jadikan Redaksi"
                                    confirm="Jadikan {{ $user->name }} sebagai penulis Redaksi (langsung terverifikasi)?"
                                />
                            @endif
                        @endif
                        @if($user->role === 'penulis' && $user->is_internal)
                            <x-admin.action-icon
                                :href="route('admin.penulis.unmark-redaksi', $user)"
                                method="POST"
                                icon="fas fa-user-edit"
                                color="amber"
                                title="Cabut status Redaksi"
                                confirm="Cabut status Redaksi? Akun tetap penulis terverifikasi."
                            />
                        @elseif($user->role === 'penulis' && $user->verified)
                            <x-admin.action-icon
                                :href="route('admin.penulis.mark-redaksi', $user)"
                                method="POST"
                                icon="fas fa-id-badge"
                                color="indigo"
                                title="Jadikan Redaksi"
                                confirm="Tandai {{ $user->name }} sebagai penulis Redaksi?"
                            />
                            <x-admin.action-icon
                                :href="route('admin.users.toggle-verified', $user)"
                                method="POST"
                                icon="fas fa-user-slash"
                                color="amber"
                                title="Cabut Verified → User"
                                confirm="Cabut verified dan turunkan ke user biasa?"
                            />
                        @endif
                    </x-admin.actions>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-12 text-center text-news-muted">
                        <i class="fas fa-users text-4xl mb-4 text-news-muted"></i>
                        <p class="text-lg font-medium text-news-ink">Belum Ada Pengguna</p>
                        <p class="text-sm">Belum ada pengguna yang terdaftar dalam sistem.</p>
                    </td>
                </tr>
            @endforelse
        </x-admin.table>
    </div>
</div>
@endsection
