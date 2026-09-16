@extends('layouts.admin-simple')

@section('title', 'Tambah Event Popup')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-2xl mx-auto">
        <div class="flex items-center mb-6">
            <a href="{{ route('admin.event-popups.index') }}" class="text-news-muted hover:text-news-ink mr-4">
                <i class="fas fa-arrow-left"></i>
            </a>
            <h1 class="text-2xl font-bold text-news-ink">Tambah Event Popup</h1>
        </div>

        <div class="bg-white border border-news-line p-6">
            <form action="{{ route('admin.event-popups.store') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label for="title" class="block text-sm font-medium text-news-ink mb-2">Judul Event</label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           value="{{ old('title') }}"
                           class="w-full px-3 py-2 border border-news-line rounded-md focus:outline-none focus:ring-2 focus:ring-news-accent focus:border-transparent @error('title') border-red-500 @enderror"
                           placeholder="Masukkan judul event popup"
                           required>
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="message" class="block text-sm font-medium text-news-ink mb-2">Pesan</label>
                    <textarea id="message" 
                              name="message" 
                              rows="4"
                              class="w-full px-3 py-2 border border-news-line rounded-md focus:outline-none focus:ring-2 focus:ring-news-accent focus:border-transparent @error('message') border-red-500 @enderror"
                              placeholder="Masukkan pesan yang akan ditampilkan di popup"
                              required>{{ old('message') }}</textarea>
                    @error('message')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-news-ink mb-2">Tanggal Mulai</label>
                        <input type="date" 
                               id="start_date" 
                               name="start_date" 
                               value="{{ old('start_date') }}"
                               class="w-full px-3 py-2 border border-news-line rounded-md focus:outline-none focus:ring-2 focus:ring-news-accent focus:border-transparent @error('start_date') border-red-500 @enderror"
                               required>
                        @error('start_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-news-ink mb-2">Tanggal Selesai</label>
                        <input type="date" 
                               id="end_date" 
                               name="end_date" 
                               value="{{ old('end_date') }}"
                               class="w-full px-3 py-2 border border-news-line rounded-md focus:outline-none focus:ring-2 focus:ring-news-accent focus:border-transparent @error('end_date') border-red-500 @enderror"
                               required>
                        @error('end_date')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="status" 
                               value="1"
                               {{ old('status', true) ? 'checked' : '' }}
                               class="rounded border-news-line text-news-accent shadow-sm focus:border-news-accent focus:ring focus:ring-news-accent/30 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-news-ink">Aktifkan event popup</span>
                    </label>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.event-popups.index') }}" class="px-4 py-2 border border-news-line rounded-md text-news-ink hover:bg-news-paper transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-4 py-2 bg-news-accent text-white rounded-md hover:bg-news-ink transition-colors">
                        <i class="fas fa-save mr-2"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    startDateInput.min = today;
    
    startDateInput.addEventListener('change', function() {
        endDateInput.min = this.value;
    });
});
</script>
@endsection
