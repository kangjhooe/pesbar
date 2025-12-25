@extends('layouts.admin-simple')

@section('title', 'Permintaan Verifikasi Penulis')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Permintaan Verifikasi Penulis</h1>
        <p class="text-gray-600">Tinjau dan setujui permintaan verifikasi dari penulis</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            {{ session('error') }}
        </div>
    @endif

    @if($requests->count() > 0)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Penulis
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Email
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tipe
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Dokumen
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total Artikel
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tanggal Permintaan
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($requests as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
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
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($user->verification_type)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $user->verification_type === 'perorangan' ? 'bg-purple-100 text-purple-800' : 'bg-indigo-100 text-indigo-800' }}">
                                        <i class="fas {{ $user->verification_type === 'perorangan' ? 'fa-user' : 'fa-building' }} mr-1"></i>
                                        {{ $user->verification_type === 'perorangan' ? 'Perorangan' : 'Lembaga' }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($user->verification_document)
                                    @php
                                        $extension = pathinfo($user->verification_document, PATHINFO_EXTENSION);
                                        $isPdf = strtolower($extension) === 'pdf';
                                        $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png']);
                                    @endphp
                                    <a href="{{ asset('storage/' . $user->verification_document) }}" 
                                       target="_blank" 
                                       class="inline-flex items-center text-blue-600 hover:text-blue-900"
                                       title="Lihat Dokumen">
                                        <i class="fas {{ $isPdf ? 'fa-file-pdf' : ($isImage ? 'fa-file-image' : 'fa-file') }} mr-1"></i>
                                        <span class="text-xs">Lihat Dokumen</span>
                                    </a>
                                @else
                                    <span class="text-gray-400 text-xs">Tidak ada</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $user->articles()->count() }} artikel
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->verification_requested_at ? $user->verification_requested_at->format('d-m-Y H:i') : '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('penulis.public-profile', $user->username) }}" 
                                       class="text-blue-600 hover:text-blue-900" 
                                       title="Lihat Profil"
                                       target="_blank">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <form method="POST" action="{{ route('admin.verification-requests.approve', $user) }}" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                class="text-green-600 hover:text-green-900" 
                                                title="Setujui Verifikasi"
                                                onclick="return confirm('Setujui verifikasi untuk {{ $user->name }}? Artikel pending akan otomatis dipublish.')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    
                                    <form method="POST" action="{{ route('admin.verification-requests.reject', $user) }}" class="inline" id="reject-form-{{ $user->id }}">
                                        @csrf
                                        <button type="button" 
                                                class="text-red-600 hover:text-red-900" 
                                                title="Tolak Verifikasi"
                                                onclick="showRejectModal({{ $user->id }}, '{{ $user->name }}')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $requests->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Tidak ada permintaan verifikasi</h3>
            <p class="text-gray-500">Semua permintaan verifikasi telah ditinjau.</p>
        </div>
    @endif
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Tolak Permintaan Verifikasi</h3>
            <form id="rejectModalForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Alasan Penolakan <span class="text-gray-500">(Opsional)</span>
                    </label>
                    <textarea 
                        id="reason" 
                        name="reason" 
                        rows="3" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
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

<script>
function showRejectModal(userId, userName) {
    const modal = document.getElementById('rejectModal');
    const form = document.getElementById('rejectModalForm');
    form.action = '{{ route("admin.verification-requests.reject", ":id") }}'.replace(':id', userId);
    modal.classList.remove('hidden');
}

function closeRejectModal() {
    const modal = document.getElementById('rejectModal');
    modal.classList.add('hidden');
    document.getElementById('reason').value = '';
}

// Close modal when clicking outside
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeRejectModal();
    }
});
</script>
@endsection

