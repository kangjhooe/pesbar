@extends('layouts.admin-simple')

@section('title', 'Edit Kontak Penting')
@section('page-title', 'Edit Kontak Penting')
@section('page-subtitle', 'Perbarui kontak untuk widget sidebar publik')

@section('content')
<div class="max-w-4xl space-y-4">
    @include('admin.partials.widget-placement-note', [
        'items' => [
            'Sidebar beranda (widget Kontak Penting)',
            'Sidebar halaman artikel (versi ringkas)',
        ],
    ])

    <div class="bg-white rounded-xl shadow-sm border border-news-line overflow-hidden">
        <div class="px-6 py-4 border-b border-news-line flex items-center justify-between">
            <h3 class="text-lg font-semibold text-news-ink">Edit Kontak</h3>
            <a href="{{ route('admin.contact-importants.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>

        <form action="{{ route('admin.contact-importants.update', $contactImportant) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            @if($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-news-ink mb-1">Nama Instansi/Lembaga <span class="text-news-accent">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $contactImportant->name) }}" required
                           class="form-input @error('name') border-red-500 @enderror"
                           placeholder="Contoh: Polres Pesisir Barat">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="type" class="block text-sm font-medium text-news-ink mb-1">Jenis Kontak <span class="text-news-accent">*</span></label>
                    <select id="type" name="type" required class="form-input @error('type') border-red-500 @enderror">
                        <option value="">Pilih Jenis Kontak</option>
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}" {{ old('type', $contactImportant->type) == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="phone" class="block text-sm font-medium text-news-ink mb-1">Nomor Telepon</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone', $contactImportant->phone) }}"
                           class="form-input @error('phone') border-red-500 @enderror"
                           placeholder="Contoh: 0721-123456">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="sort_order" class="block text-sm font-medium text-news-ink mb-1">Urutan Tampil</label>
                    <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $contactImportant->sort_order) }}" min="0"
                           class="form-input @error('sort_order') border-red-500 @enderror" placeholder="0">
                    <p class="mt-1 text-xs text-news-muted">Angka lebih kecil ditampilkan lebih dulu</p>
                    @error('sort_order')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="address" class="block text-sm font-medium text-news-ink mb-1">Alamat</label>
                <textarea id="address" name="address" rows="2" class="form-input @error('address') border-red-500 @enderror"
                          placeholder="Alamat lengkap instansi/lembaga">{{ old('address', $contactImportant->address) }}</textarea>
                @error('address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-news-ink mb-1">Deskripsi</label>
                <textarea id="description" name="description" rows="3" class="form-input @error('description') border-red-500 @enderror"
                          placeholder="Deskripsi tambahan atau informasi penting">{{ old('description', $contactImportant->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <label class="inline-flex items-center gap-2 text-sm text-news-ink">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" id="is_active" name="is_active" value="1"
                       class="rounded border-news-line text-news-accent focus:ring-news-accent"
                       {{ old('is_active', $contactImportant->is_active) ? 'checked' : '' }}>
                Aktif (tampil di widget publik)
            </label>

            <div class="flex flex-wrap gap-3 pt-2 border-t border-news-line">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save mr-2"></i>Perbarui
                </button>
                <a href="{{ route('admin.contact-importants.index') }}" class="btn-secondary">
                    <i class="fas fa-times mr-2"></i>Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
