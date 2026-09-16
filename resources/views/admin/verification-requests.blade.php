@extends('layouts.admin-simple')

@section('title', 'Permintaan Upgrade & Verifikasi')
@section('page-title', 'Permintaan Upgrade & Verifikasi')
@section('page-subtitle', 'Tinjau upgrade user ke penulis dan verifikasi penulis')

@section('content')
<div class="space-y-6">
    <x-admin.table :paginator="$requests" :bulk="true" bulk-id="verification-bulk">
        <x-slot:bulkBar>
            <x-admin.bulk-bar
                :action="route('admin.verification-requests.bulk')"
                bulk-id="verification-bulk"
                :options="['approve' => 'Setujui', 'reject' => 'Tolak']"
            />
        </x-slot:bulkBar>

        <x-slot:head>
            <x-admin.checkbox all bulk-id="verification-bulk" />
            <x-admin.th :sortable="false" label="#" align="center" class="w-14" />
            <x-admin.th column="name" label="Pemohon" />
            <x-admin.th column="role" label="Jenis" />
            <x-admin.th :sortable="false" label="Email" />
            <x-admin.th column="verification_type" label="Tipe" />
            <x-admin.th :sortable="false" label="Dokumen" />
            <x-admin.th column="articles_count" label="Total Artikel" />
            <x-admin.th column="verification_requested_at" label="Tanggal Permintaan" />
            <x-admin.th :sortable="false" label="Aksi" align="right" />
        </x-slot:head>

        @forelse($requests as $user)
            <tr class="hover:bg-slate-50/80 transition-colors">
                <x-admin.checkbox :value="$user->id" bulk-id="verification-bulk" name="ids[]" />
                <x-admin.td-number :index="$requests->firstItem() + $loop->index" />
                <td class="px-4 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        @if($user->profile && $user->profile->avatar)
                            <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $user->profile->avatar) }}" alt="{{ $user->name }}">
                        @else
                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                <span class="text-gray-600 font-medium">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                        @endif
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                            @if($user->profile && $user->profile->bio)
                                <div class="text-sm text-gray-500">{{ \Illuminate\Support\Str::limit($user->profile->bio, 50) }}</div>
                            @endif
                        </div>
                    </div>
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm">
                    @if($user->role === 'user')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                            Upgrade ke penulis
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Verifikasi penulis
                        </span>
                    @endif
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $user->email }}
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm">
                    @if($user->verification_type)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->verification_type === 'perorangan' ? 'bg-purple-100 text-purple-800' : 'bg-indigo-100 text-indigo-800' }}">
                            <i class="fas {{ $user->verification_type === 'perorangan' ? 'fa-user' : 'fa-building' }} mr-1"></i>
                            {{ $user->verification_type === 'perorangan' ? 'Perorangan' : 'Lembaga' }}
                        </span>
                    @else
                        <span class="text-gray-400">—</span>
                    @endif
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm">
                    @if($user->verification_document)
                        @php
                            $extension = pathinfo($user->verification_document, PATHINFO_EXTENSION);
                            $isPdf = strtolower($extension) === 'pdf';
                            $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png']);
                            $docIcon = $isPdf ? 'fas fa-file-pdf' : ($isImage ? 'fas fa-file-image' : 'fas fa-file');
                        @endphp
                        <x-admin.action-icon
                            :href="asset('storage/' . $user->verification_document)"
                            :icon="$docIcon"
                            color="blue"
                            title="Lihat Dokumen"
                            target="_blank"
                        />
                    @else
                        <span class="text-gray-400 text-xs">Tidak ada</span>
                    @endif
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ $user->articles_count }} artikel
                    </span>
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $user->verification_requested_at ? $user->verification_requested_at->format('d-m-Y H:i') : '—' }}
                </td>
                <x-admin.actions>
                    @if($user->role === 'penulis' && $user->username)
                        <x-admin.action-icon
                            :href="route('penulis.public-profile', $user->username)"
                            icon="fas fa-eye"
                            color="blue"
                            title="Lihat Profil"
                            target="_blank"
                        />
                    @endif
                    <x-admin.action-icon
                        :href="route('admin.verification-requests.approve', $user)"
                        method="POST"
                        icon="fas fa-check"
                        color="green"
                        title="{{ $user->role === 'user' ? 'Setujui Upgrade' : 'Setujui Verifikasi' }}"
                        confirm="{{ $user->role === 'user' ? 'Setujui upgrade '.$user->name.' menjadi penulis terverifikasi?' : 'Setujui verifikasi untuk '.$user->name.'? Artikel pending akan otomatis dipublish.' }}"
                    />
                    <x-admin.action-icon
                        type="button"
                        icon="fas fa-times"
                        color="red"
                        title="{{ $user->role === 'user' ? 'Tolak Upgrade' : 'Tolak Verifikasi' }}"
                        onclick="showRejectModal({{ $user->id }}, {{ json_encode($user->name) }}, {{ json_encode($user->role === 'user' ? 'upgrade' : 'verifikasi') }})"
                    />
                </x-admin.actions>
            </tr>
        @empty
            <tr>
                <td colspan="10" class="px-4 py-12 text-center text-gray-500">
                    <i class="fas fa-inbox text-4xl mb-4 text-gray-300"></i>
                    <p class="text-lg font-medium text-gray-700">Tidak ada permintaan</p>
                    <p class="text-sm">Semua permintaan upgrade/verifikasi telah ditinjau.</p>
                </td>
            </tr>
        @endforelse
    </x-admin.table>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <h3 id="rejectModalTitle" class="text-lg font-semibold text-gray-900 mb-4">Tolak Permintaan</h3>
            <form id="rejectModalForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Alasan Penolakan <span class="text-gray-500">(Opsional, akan dilihat pemohon)</span>
                    </label>
                    <textarea
                        id="reason"
                        name="reason"
                        rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-news-accent focus:border-news-accent"
                        placeholder="Masukkan alasan penolakan..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeRejectModal()" class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 text-white bg-red-600 rounded-md hover:bg-red-700">
                        Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showRejectModal(userId, userName, kind) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectModalForm');
    const title = document.getElementById('rejectModalTitle');
    form.action = '{{ route("admin.verification-requests.reject", ":id") }}'.replace(':id', userId);
    title.textContent = kind === 'upgrade'
        ? 'Tolak Upgrade: ' + userName
        : 'Tolak Verifikasi: ' + userName;
    modal.classList.remove('hidden');
}

function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    modal.classList.add('hidden');
    document.getElementById('reason').value = '';
}

document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});
</script>
@endpush
@endsection
