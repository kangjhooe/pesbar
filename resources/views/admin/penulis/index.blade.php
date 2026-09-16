@extends('layouts.admin-simple')

@section('title', 'Manajemen Penulis - Admin Panel')
@section('page-title', 'Manajemen Penulis')
@section('page-subtitle', 'Kelola penulis dan kontributor')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white border border-news-line p-6">
            <div class="flex items-center">
                <div class="p-3 bg-news-paper text-news-ink">
                    <i class="fas fa-user-edit text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-news-muted">Total Penulis</p>
                    <p class="text-2xl font-semibold text-news-ink">{{ $penulis->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-news-line p-6">
            <div class="flex items-center">
                <div class="p-3 bg-news-paper text-news-ink">
                    <i class="fas fa-newspaper text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-news-muted">Total Artikel</p>
                    <p class="text-2xl font-semibold text-news-ink">{{ $penulis->sum('articles_count') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-news-line p-6">
            <div class="flex items-center">
                <div class="p-3 bg-news-paper text-news-ink">
                    <i class="fas fa-eye text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-news-muted">Total Views</p>
                    <p class="text-2xl font-semibold text-news-ink">{{ number_format($penulis->sum('articles_sum_views')) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-news-line p-6">
            <div class="flex items-center">
                <div class="p-3 bg-news-paper text-news-ink">
                    <i class="fas fa-star text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-news-muted">Penulis Aktif</p>
                    <p class="text-2xl font-semibold text-news-ink">{{ $penulis->where('created_at', '>=', now()->subDays(30))->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <x-admin.table :paginator="$penulis" :bulk="true" bulk-id="penulis-bulk">
        <x-slot:bulkBar>
            <x-admin.bulk-bar
                :action="route('admin.penulis.bulk')"
                bulk-id="penulis-bulk"
                :options="['unverify' => 'Cabut Verified → User', 'demote' => 'Turunkan ke User']"
            />
        </x-slot:bulkBar>

        <x-slot:head>
            <x-admin.checkbox all bulk-id="penulis-bulk" />
            <x-admin.th :sortable="false" label="#" align="center" class="w-14 admin-col-optional" />
            <x-admin.th column="name" label="Penulis" />
            <x-admin.th :sortable="false" label="Kontak" class="admin-col-optional" />
            <x-admin.th column="articles_count" label="Statistik" class="admin-col-optional" />
            <x-admin.th column="verified" label="Status" />
            <x-admin.th column="created_at" label="Bergabung" class="admin-col-optional" />
            <x-admin.th :sortable="false" label="Aksi" align="right" />
        </x-slot:head>

        @forelse($penulis as $user)
            <tr class="hover:bg-news-paper transition-colors">
                <x-admin.checkbox :value="$user->id" bulk-id="penulis-bulk" name="ids[]" />
                <x-admin.td-number :index="$penulis->firstItem() + $loop->index" class="admin-col-optional" />
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
                                @if($user->isRedaksi())
                                    <span class="inline-flex px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide bg-news-ink text-white">
                                        Redaksi
                                    </span>
                                @endif
                            </div>
                            <div class="text-sm text-news-muted">@{{ $user->username }}</div>
                            <div class="mt-0.5 text-xs text-news-muted md:hidden truncate max-w-[11rem]">{{ $user->email }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap admin-col-optional">
                    <div class="text-sm text-news-ink">{{ $user->email }}</div>
                    @if($user->profile && $user->profile->phone)
                        <div class="text-sm text-news-muted">{{ $user->profile->phone }}</div>
                    @endif
                </td>
                <td class="px-4 py-4 whitespace-nowrap admin-col-optional">
                    <div class="flex items-center space-x-2">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-news-paper text-news-ink">
                            <i class="fas fa-newspaper mr-1"></i>
                            {{ $user->published_articles_count }} terbit
                            @if(($user->articles_count ?? 0) > ($user->published_articles_count ?? 0))
                                <span class="ml-1 text-news-muted">/ {{ $user->articles_count }} total</span>
                            @endif
                        </span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-800">
                            <i class="fas fa-eye mr-1"></i>
                            {{ number_format($user->articles_sum_views ?? 0) }} views
                        </span>
                    </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    <div class="flex flex-col gap-1.5">
                        <div class="flex items-center space-x-2 flex-wrap gap-y-1">
                        @if($user->isRedaksi())
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-news-ink text-white">
                                Redaksi
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
                        @if((int) $user->content_warning_count > 0)
                            <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded bg-amber-100 text-amber-900 w-fit">
                                {{ $user->content_warning_count }}x sanksi (riwayat)
                            </span>
                        @endif
                        @if($user->isPublishRestricted())
                            <span class="inline-flex px-2 py-0.5 text-xs font-medium rounded bg-red-50 text-news-accent w-fit">
                                Batas publish s/d {{ $user->publish_restricted_until->format('d/m/Y') }}
                            </span>
                        @endif
                    </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-news-muted admin-col-optional">
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
                        :href="route('admin.penulis.impersonate', $user)"
                        method="POST"
                        icon="fas fa-user-secret"
                        color="indigo"
                        title="Lihat sebagai penulis"
                        confirm="Masuk ke dashboard sebagai penulis ini? Tindakan yang Anda lakukan akan berlaku atas nama mereka."
                    />
                    <x-admin.action-icon
                        :href="route('admin.penulis.warn', $user)"
                        method="POST"
                        icon="fas fa-exclamation-triangle"
                        color="amber"
                        title="Beri sanksi (ban 7/30/365 hari)"
                        confirm="Beri sanksi konten? Akun diturunkan ke user dan dibanned: ke-1 = 7 hari, ke-2 = 30 hari, ke-3+ = 1 tahun."
                    />
                    @if($user->isPublishRestricted())
                        <x-admin.action-icon
                            :href="route('admin.penulis.clear-publish-restriction', $user)"
                            method="POST"
                            icon="fas fa-unlock"
                            color="green"
                            title="Cabut batas publish"
                            confirm="Cabut pembatasan publish untuk penulis ini?"
                        />
                    @else
                        <x-admin.action-icon
                            :href="route('admin.penulis.restrict-publish', $user)"
                            method="POST"
                            icon="fas fa-ban"
                            color="amber"
                            title="Batasi publish 7 hari"
                            confirm="Batasi publish langsung penulis ini selama 7 hari? Artikel baru wajib lewat review."
                        />
                    @endif
                    @if($user->isRedaksi())
                        <x-admin.action-icon
                            :href="route('admin.penulis.unmark-redaksi', $user)"
                            method="POST"
                            icon="fas fa-user-edit"
                            color="amber"
                            title="Cabut status Redaksi"
                            confirm="Cabut status Redaksi? Akun tetap penulis terverifikasi."
                        />
                    @else
                        <x-admin.action-icon
                            :href="route('admin.penulis.mark-redaksi', $user)"
                            method="POST"
                            icon="fas fa-id-badge"
                            color="indigo"
                            title="Jadikan Redaksi"
                            confirm="Tandai {{ $user->name }} sebagai penulis Redaksi?"
                        />
                    @endif
                    @if($user->verified && !$user->isRedaksi())
                        <x-admin.action-icon
                            :href="route('admin.penulis.revoke-verified', $user)"
                            method="POST"
                            icon="fas fa-user-slash"
                            color="amber"
                            title="Cabut verified → User"
                            confirm="Cabut verified dan turunkan ke user biasa? (tanpa ban; bisa ajukan upgrade lagi)"
                        />
                    @endif
                    @if(!$user->isRedaksi())
                        <x-admin.action-icon
                            :href="route('admin.penulis.demote', $user)"
                            method="POST"
                            icon="fas fa-arrow-down"
                            color="red"
                            title="Turunkan ke User"
                            confirm="Turunkan penulis ini menjadi user? Artikel yang sudah ada tetap milik penulis ini."
                        />
                    @endif
                </x-admin.actions>
            </tr>
        @empty
            <tr>
                <td colspan="8" class="px-4 py-12 text-center text-news-muted">
                    <i class="fas fa-user-edit text-4xl mb-4 text-news-muted"></i>
                    <p class="text-lg font-medium text-news-ink">Belum Ada Penulis</p>
                    <p class="text-sm">Penulis muncul setelah approve pengajuan, atau ditandai Redaksi dari daftar pengguna</p>
                </td>
            </tr>
        @endforelse
    </x-admin.table>
</div>
@endsection
