@extends('layouts.penulis')

@section('title', 'Media Library')
@section('page-title', 'Media Library')
@section('page-subtitle', 'Kelola file media Anda')

@push('styles')
<style>
    .media-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1.5rem;
    }
    .media-item {
        position: relative;
        aspect-ratio: 1;
        border-radius: 0.5rem;
        overflow: hidden;
        background: #f3f4f6;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .media-item:hover {
        transform: scale(1.05);
    }
    .media-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .media-item .overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);
        padding: 0.75rem;
        color: white;
        font-size: 0.75rem;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 px-4 sm:px-6 lg:px-8 py-6 lg:py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Media Library</h1>
                <p class="text-gray-600 mt-1">Kelola dan gunakan file media Anda</p>
            </div>
            <div class="flex gap-2">
                <button onclick="openUploadModal()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="fas fa-upload mr-2"></i>Upload Media
                </button>
                <a href="{{ route('penulis.dashboard') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 text-green-800 px-4 py-3 rounded">
        {{ session('success') }}
    </div>
    @endif

    <!-- Media Grid -->
    @if(count($paginatedFiles) > 0)
    <div class="media-grid mb-6">
        @foreach($paginatedFiles as $file)
        <div class="media-item group" data-url="{{ $file['url'] }}" data-path="{{ $file['path'] }}">
            <img src="{{ $file['url'] }}" alt="{{ $file['name'] }}" onerror="this.src='{{ asset('images/placeholder.png') }}'">
            <div class="overlay opacity-0 group-hover:opacity-100 transition-opacity">
                <div class="font-semibold truncate">{{ Str::limit($file['name'], 20) }}</div>
                <div class="text-xs mt-1">{{ number_format($file['size'] / 1024, 2) }} KB</div>
            </div>
            <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <div class="flex gap-1">
                    <button onclick="copyUrl('{{ $file['url'] }}')" class="p-2 bg-blue-600 text-white rounded hover:bg-blue-700" title="Copy URL">
                        <i class="fas fa-copy text-xs"></i>
                    </button>
                    <button onclick="deleteMedia('{{ $file['path'] }}')" class="p-2 bg-red-600 text-white rounded hover:bg-red-700" title="Hapus">
                        <i class="fas fa-trash text-xs"></i>
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($totalPages > 1)
    <div class="flex justify-center gap-2">
        @for($i = 1; $i <= $totalPages; $i++)
        <a href="?page={{ $i }}" class="px-4 py-2 {{ $currentPage == $i ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }} rounded-lg transition">
            {{ $i }}
        </a>
        @endfor
    </div>
    @endif
    @else
    <div class="bg-white rounded-xl shadow-md p-12 text-center">
        <i class="fas fa-images text-6xl text-gray-400 mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum ada media</h3>
        <p class="text-gray-600 mb-4">Upload file media pertama Anda untuk memulai</p>
        <button onclick="openUploadModal()" class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
            <i class="fas fa-upload mr-2"></i>Upload Media
        </button>
    </div>
    @endif
</div>

<!-- Upload Modal -->
<div id="uploadModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center">
    <div class="bg-white rounded-xl shadow-xl p-6 max-w-md w-full mx-4">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-semibold text-gray-900">Upload Media</h3>
            <button onclick="closeUploadModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="uploadForm" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File</label>
                <input type="file" name="file" id="fileInput" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                <p class="text-xs text-gray-500 mt-1">Format: JPEG, PNG, JPG, GIF, WebP (Max: 5MB)</p>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                    <i class="fas fa-upload mr-2"></i>Upload
                </button>
                <button type="button" onclick="closeUploadModal()" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition">
                    Batal
                </button>
            </div>
        </form>
        <div id="uploadProgress" class="hidden mt-4">
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div id="progressBar" class="bg-blue-600 h-2 rounded-full transition-all" style="width: 0%"></div>
            </div>
            <p class="text-sm text-gray-600 mt-2 text-center">Mengupload...</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openUploadModal() {
        document.getElementById('uploadModal').classList.remove('hidden');
        document.getElementById('uploadModal').classList.add('flex');
    }

    function closeUploadModal() {
        document.getElementById('uploadModal').classList.add('hidden');
        document.getElementById('uploadModal').classList.remove('flex');
        document.getElementById('uploadForm').reset();
        document.getElementById('uploadProgress').classList.add('hidden');
    }

    function copyUrl(url) {
        navigator.clipboard.writeText(url).then(() => {
            alert('URL berhasil disalin!');
        });
    }

    function deleteMedia(path) {
        if (!confirm('Yakin ingin menghapus file ini?')) return;

        fetch('{{ route("penulis.media.delete") }}', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ path: path })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Gagal menghapus file: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus file');
        });
    }

    document.getElementById('uploadForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const progressDiv = document.getElementById('uploadProgress');
        const progressBar = document.getElementById('progressBar');
        
        progressDiv.classList.remove('hidden');
        progressBar.style.width = '0%';

        fetch('{{ route("penulis.media.upload") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                progressBar.style.width = '100%';
                setTimeout(() => {
                    location.reload();
                }, 500);
            } else {
                alert('Gagal upload: ' + (data.message || 'Unknown error'));
                progressDiv.classList.add('hidden');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat upload');
            progressDiv.classList.add('hidden');
        });
    });
</script>
@endpush
@endsection

