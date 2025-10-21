@extends('layouts.admin-simple')

@section('title', 'Media Library - Admin Panel')
@section('page-title', 'Media Library')
@section('page-subtitle', 'Kelola file media dan gambar')

@section('content')
<div class="space-y-6">
    <!-- Upload Section -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Upload Media</h3>
        <form action="{{ route('admin.media.upload') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="flex items-center space-x-4">
                <input type="file" name="file" id="file" accept="image/*,video/*,audio/*,.pdf,.doc,.docx" 
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" required>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                    <i class="fas fa-upload mr-2"></i>
                    Upload
                </button>
            </div>
            <p class="text-sm text-gray-500">Maksimal 10MB. Format yang didukung: JPG, PNG, GIF, MP4, PDF, DOC, DOCX</p>
        </form>
    </div>

    <!-- Media Grid -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Media Files</h3>
            <p class="text-sm text-gray-600">{{ $mediaFiles->count() }} file</p>
        </div>
        
        @if($mediaFiles->count() > 0)
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @foreach($mediaFiles as $file)
                <div class="group relative bg-gray-100 rounded-lg overflow-hidden hover:shadow-lg transition-shadow">
                    @if(str_starts_with($file['type'], 'image/'))
                        <img src="{{ asset('storage/' . $file['name']) }}" 
                             alt="{{ $file['name'] }}" 
                             class="w-full h-32 object-cover">
                    @elseif(str_starts_with($file['type'], 'video/'))
                        <div class="w-full h-32 bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-video text-3xl text-gray-400"></i>
                        </div>
                    @elseif(str_starts_with($file['type'], 'audio/'))
                        <div class="w-full h-32 bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-music text-3xl text-gray-400"></i>
                        </div>
                    @elseif($file['type'] === 'application/pdf')
                        <div class="w-full h-32 bg-red-100 flex items-center justify-center">
                            <i class="fas fa-file-pdf text-3xl text-red-500"></i>
                        </div>
                    @elseif(str_contains($file['type'], 'document'))
                        <div class="w-full h-32 bg-blue-100 flex items-center justify-center">
                            <i class="fas fa-file-word text-3xl text-blue-500"></i>
                        </div>
                    @else
                        <div class="w-full h-32 bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-file text-3xl text-gray-400"></i>
                        </div>
                    @endif
                    
                    <!-- File Info -->
                    <div class="p-2">
                        <p class="text-xs text-gray-600 truncate" title="{{ $file['name'] }}">{{ $file['name'] }}</p>
                        <p class="text-xs text-gray-500">{{ number_format($file['size'] / 1024, 1) }} KB</p>
                        <p class="text-xs text-gray-500">{{ date('d/m/Y', $file['modified']) }}</p>
                    </div>
                    
                    <!-- Actions -->
                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all duration-200 flex items-center justify-center opacity-0 group-hover:opacity-100">
                        <div class="flex space-x-2">
                            <a href="{{ asset('storage/' . $file['name']) }}" 
                               target="_blank" 
                               class="bg-white text-gray-700 p-2 rounded-full hover:bg-gray-100 transition-colors"
                               title="Lihat">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button onclick="copyToClipboard('{{ asset('storage/' . $file['name']) }}')" 
                                    class="bg-white text-gray-700 p-2 rounded-full hover:bg-gray-100 transition-colors"
                                    title="Copy URL">
                                <i class="fas fa-copy"></i>
                            </button>
                            <form action="{{ route('admin.media.delete') }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="filename" value="{{ $file['name'] }}">
                                <button type="submit" 
                                        class="bg-white text-red-600 p-2 rounded-full hover:bg-red-50 transition-colors"
                                        title="Hapus"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus file ini?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="p-12 text-center">
            <div class="text-gray-500">
                <i class="fas fa-images text-4xl mb-4"></i>
                <p class="text-lg font-medium">Belum ada file media</p>
                <p class="text-sm">Upload file pertama Anda</p>
            </div>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success message
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50';
        toast.textContent = 'URL berhasil disalin!';
        document.body.appendChild(toast);
        
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 3000);
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
    });
}

// File upload preview
document.getElementById('file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const fileSize = (file.size / 1024 / 1024).toFixed(2);
        if (file.size > 10 * 1024 * 1024) {
            alert('File terlalu besar. Maksimal 10MB.');
            e.target.value = '';
        }
    }
});
</script>
@endpush
@endsection
