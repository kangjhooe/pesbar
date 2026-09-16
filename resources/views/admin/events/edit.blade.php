@extends('layouts.admin-simple')

@section('title', 'Edit Event')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-news-ink">Edit Event</h1>
            <p class="text-news-muted mt-1">Perbarui event: {{ $event->title }}</p>
        </div>
        <a href="{{ route('admin.events.index') }}" class="btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali
        </a>
    </div>

    <div class="bg-white border border-news-line">
        <form action="{{ route('admin.events.update', $event) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-6">
                <div>
                    <h3 class="text-lg font-medium text-news-ink mb-4">Informasi Dasar</h3>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="lg:col-span-2">
                            <label for="title" class="block text-sm font-medium text-news-ink mb-2">Judul Event *</label>
                            <input type="text" name="title" id="title" value="{{ old('title', $event->title) }}"
                                   class="form-input @error('title') border-red-500 @enderror" required>
                            @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="lg:col-span-2">
                            <label for="description" class="block text-sm font-medium text-news-ink mb-2">Deskripsi</label>
                            <textarea name="description" id="description" rows="4"
                                      class="form-input @error('description') border-red-500 @enderror">{{ old('description', $event->description) }}</textarea>
                            @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="event_date" class="block text-sm font-medium text-news-ink mb-2">Tanggal Event *</label>
                            <input type="date" name="event_date" id="event_date"
                                   value="{{ old('event_date', $event->event_date?->format('Y-m-d')) }}"
                                   class="form-input @error('event_date') border-red-500 @enderror" required>
                            @error('event_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="start_time" class="block text-sm font-medium text-news-ink mb-2">Waktu Mulai</label>
                            <input type="time" name="start_time" id="start_time"
                                   value="{{ old('start_time', $event->start_time ? substr((string) $event->start_time, 0, 5) : '') }}"
                                   class="form-input @error('start_time') border-red-500 @enderror">
                            @error('start_time')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="end_time" class="block text-sm font-medium text-news-ink mb-2">Waktu Selesai</label>
                            <input type="time" name="end_time" id="end_time"
                                   value="{{ old('end_time', $event->end_time ? substr((string) $event->end_time, 0, 5) : '') }}"
                                   class="form-input @error('end_time') border-red-500 @enderror">
                            @error('end_time')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="location" class="block text-sm font-medium text-news-ink mb-2">Lokasi</label>
                            <input type="text" name="location" id="location" value="{{ old('location', $event->location) }}"
                                   class="form-input @error('location') border-red-500 @enderror">
                            @error('location')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="organizer" class="block text-sm font-medium text-news-ink mb-2">Penyelenggara</label>
                            <input type="text" name="organizer" id="organizer" value="{{ old('organizer', $event->organizer) }}"
                                   class="form-input @error('organizer') border-red-500 @enderror">
                            @error('organizer')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-news-ink mb-4">Pengaturan Event</h3>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <label for="event_type" class="block text-sm font-medium text-news-ink mb-2">Tipe Event *</label>
                            <select name="event_type" id="event_type" class="form-select @error('event_type') border-red-500 @enderror" required>
                                @foreach(['pemerintah','masyarakat','budaya','olahraga','pendidikan','kesehatan','lainnya'] as $type)
                                    <option value="{{ $type }}" {{ old('event_type', $event->event_type) == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                                @endforeach
                            </select>
                            @error('event_type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="priority" class="block text-sm font-medium text-news-ink mb-2">Prioritas *</label>
                            <select name="priority" id="priority" class="form-select @error('priority') border-red-500 @enderror" required>
                                <option value="low" {{ old('priority', $event->priority) == 'low' ? 'selected' : '' }}>Rendah</option>
                                <option value="medium" {{ old('priority', $event->priority) == 'medium' ? 'selected' : '' }}>Sedang</option>
                                <option value="high" {{ old('priority', $event->priority) == 'high' ? 'selected' : '' }}>Tinggi</option>
                            </select>
                            @error('priority')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="image" class="block text-sm font-medium text-news-ink mb-2">Gambar Event</label>
                            @if($event->image)
                                <img src="{{ asset('storage/' . $event->image) }}" alt="" class="h-20 object-cover rounded border border-news-line mb-2">
                            @endif
                            <input type="file" name="image" id="image" accept="image/*" class="form-input @error('image') border-red-500 @enderror">
                            @error('image')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="contact_info" class="block text-sm font-medium text-news-ink mb-2">Informasi Kontak</label>
                            <input type="text" name="contact_info" id="contact_info" value="{{ old('contact_info', $event->contact_info) }}"
                                   class="form-input @error('contact_info') border-red-500 @enderror">
                            @error('contact_info')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-news-ink mb-4">Status</h3>
                    <div class="space-y-4">
                        <label class="flex items-center gap-2 text-sm text-news-ink">
                            <input type="checkbox" name="is_public" value="1" {{ old('is_public', $event->is_public) ? 'checked' : '' }}
                                   class="h-4 w-4 text-news-accent focus:ring-news-accent border-news-line rounded">
                            Event Publik (ditampilkan di widget)
                        </label>
                        <label class="flex items-center gap-2 text-sm text-news-ink">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $event->is_active) ? 'checked' : '' }}
                                   class="h-4 w-4 text-news-accent focus:ring-news-accent border-news-line rounded">
                            Event Aktif
                        </label>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-news-paper border-t border-news-line flex justify-end space-x-3">
                <a href="{{ route('admin.events.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary"><i class="fas fa-save mr-2"></i>Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
