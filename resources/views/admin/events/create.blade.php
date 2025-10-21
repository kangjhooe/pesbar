@extends('layouts.admin-simple')

@section('title', 'Tambah Event')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tambah Event</h1>
            <p class="text-gray-600 mt-1">Buat event baru untuk agenda kegiatan</p>
        </div>
        <a href="{{ route('admin.events.index') }}" class="btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow">
        <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="p-6 space-y-6">
                <!-- Basic Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Dasar</h3>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="lg:col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Judul Event *</label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" 
                                   class="form-input @error('title') border-red-500 @enderror" 
                                   placeholder="Masukkan judul event" required>
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="lg:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                            <textarea name="description" id="description" rows="4" 
                                      class="form-input @error('description') border-red-500 @enderror" 
                                      placeholder="Deskripsi event">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="event_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Event *</label>
                            <input type="date" name="event_date" id="event_date" value="{{ old('event_date') }}" 
                                   class="form-input @error('event_date') border-red-500 @enderror" 
                                   min="{{ date('Y-m-d') }}" required>
                            @error('event_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">Waktu Mulai</label>
                            <input type="time" name="start_time" id="start_time" value="{{ old('start_time') }}" 
                                   class="form-input @error('start_time') border-red-500 @enderror">
                            @error('start_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">Waktu Selesai</label>
                            <input type="time" name="end_time" id="end_time" value="{{ old('end_time') }}" 
                                   class="form-input @error('end_time') border-red-500 @enderror">
                            @error('end_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700 mb-2">Lokasi</label>
                            <input type="text" name="location" id="location" value="{{ old('location') }}" 
                                   class="form-input @error('location') border-red-500 @enderror" 
                                   placeholder="Lokasi event">
                            @error('location')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="organizer" class="block text-sm font-medium text-gray-700 mb-2">Penyelenggara</label>
                            <input type="text" name="organizer" id="organizer" value="{{ old('organizer') }}" 
                                   class="form-input @error('organizer') border-red-500 @enderror" 
                                   placeholder="Nama penyelenggara">
                            @error('organizer')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Event Settings -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Pengaturan Event</h3>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <label for="event_type" class="block text-sm font-medium text-gray-700 mb-2">Tipe Event *</label>
                            <select name="event_type" id="event_type" 
                                    class="form-select @error('event_type') border-red-500 @enderror" required>
                                <option value="">Pilih tipe event</option>
                                <option value="pemerintah" {{ old('event_type') == 'pemerintah' ? 'selected' : '' }}>Pemerintah</option>
                                <option value="masyarakat" {{ old('event_type') == 'masyarakat' ? 'selected' : '' }}>Masyarakat</option>
                                <option value="budaya" {{ old('event_type') == 'budaya' ? 'selected' : '' }}>Budaya</option>
                                <option value="olahraga" {{ old('event_type') == 'olahraga' ? 'selected' : '' }}>Olahraga</option>
                                <option value="pendidikan" {{ old('event_type') == 'pendidikan' ? 'selected' : '' }}>Pendidikan</option>
                                <option value="kesehatan" {{ old('event_type') == 'kesehatan' ? 'selected' : '' }}>Kesehatan</option>
                                <option value="lainnya" {{ old('event_type') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            @error('event_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Prioritas *</label>
                            <select name="priority" id="priority" 
                                    class="form-select @error('priority') border-red-500 @enderror" required>
                                <option value="">Pilih prioritas</option>
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Rendah</option>
                                <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Sedang</option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>Tinggi</option>
                            </select>
                            @error('priority')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Gambar Event</label>
                            <input type="file" name="image" id="image" accept="image/*" 
                                   class="form-input @error('image') border-red-500 @enderror">
                            @error('image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Format: JPEG, PNG, JPG, GIF. Maksimal 2MB.</p>
                        </div>

                        <div>
                            <label for="contact_info" class="block text-sm font-medium text-gray-700 mb-2">Informasi Kontak</label>
                            <input type="text" name="contact_info" id="contact_info" value="{{ old('contact_info') }}" 
                                   class="form-input @error('contact_info') border-red-500 @enderror" 
                                   placeholder="Nomor telepon, email, dll">
                            @error('contact_info')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Status Settings -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Status</h3>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input type="checkbox" name="is_public" id="is_public" value="1" 
                                   {{ old('is_public', true) ? 'checked' : '' }} 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="is_public" class="ml-2 block text-sm text-gray-900">
                                Event Publik (ditampilkan di widget)
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1" 
                                   {{ old('is_active', true) ? 'checked' : '' }} 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                Event Aktif
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                <a href="{{ route('admin.events.index') }}" class="btn-secondary">
                    Batal
                </a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Event
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Set minimum date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('event_date').setAttribute('min', today);
    
    // Auto-fill end time when start time is selected
    const startTimeInput = document.getElementById('start_time');
    const endTimeInput = document.getElementById('end_time');
    
    startTimeInput.addEventListener('change', function() {
        if (this.value && !endTimeInput.value) {
            // Add 2 hours to start time as default end time
            const startTime = new Date('2000-01-01T' + this.value);
            const endTime = new Date(startTime.getTime() + 2 * 60 * 60 * 1000);
            endTimeInput.value = endTime.toTimeString().slice(0, 5);
        }
    });
});
</script>
@endsection
