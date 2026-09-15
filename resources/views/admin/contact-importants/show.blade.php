@extends('layouts.admin-simple')

@section('title', 'Detail Kontak Penting')
@section('page-title', 'Detail Kontak Penting')
@section('page-subtitle', 'Lihat detail kontak penting')

@section('content')
<div class="max-w-5xl space-y-4">
    @include('admin.partials.widget-placement-note', [
        'items' => [
            'Sidebar beranda (widget Kontak Penting)',
            'Sidebar halaman artikel (versi ringkas)',
        ],
    ])

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between gap-3">
            <h3 class="text-lg font-semibold text-gray-900">{{ $contactImportant->name }}</h3>
            <a href="{{ route('admin.contact-importants.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>

        <div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <dl class="divide-y divide-gray-100">
                    <div class="py-3 grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">Nama</dt>
                        <dd class="sm:col-span-2 text-sm text-gray-900">{{ $contactImportant->name }}</dd>
                    </div>
                    <div class="py-3 grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">Jenis</dt>
                        <dd class="sm:col-span-2">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-50 text-news-accent">
                                {{ ucwords(str_replace('_', ' ', $contactImportant->type)) }}
                            </span>
                        </dd>
                    </div>
                    <div class="py-3 grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">Telepon</dt>
                        <dd class="sm:col-span-2 text-sm text-gray-900">
                            @if($contactImportant->phone)
                                <a href="tel:{{ $contactImportant->phone }}" class="text-news-accent hover:underline">
                                    <i class="fas fa-phone mr-1"></i>{{ $contactImportant->formatted_phone }}
                                </a>
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </dd>
                    </div>
                    <div class="py-3 grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">Alamat</dt>
                        <dd class="sm:col-span-2 text-sm text-gray-900">{{ $contactImportant->address ?: '-' }}</dd>
                    </div>
                    <div class="py-3 grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">Deskripsi</dt>
                        <dd class="sm:col-span-2 text-sm text-gray-900">{{ $contactImportant->description ?: '-' }}</dd>
                    </div>
                    <div class="py-3 grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                        <dd class="sm:col-span-2">
                            @if($contactImportant->is_active)
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">Tidak Aktif</span>
                            @endif
                        </dd>
                    </div>
                    <div class="py-3 grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">Urutan</dt>
                        <dd class="sm:col-span-2 text-sm text-gray-900">{{ $contactImportant->sort_order }}</dd>
                    </div>
                    <div class="py-3 grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">Dibuat</dt>
                        <dd class="sm:col-span-2 text-sm text-gray-900">{{ $contactImportant->created_at->format('d-m-Y H:i') }}</dd>
                    </div>
                    <div class="py-3 grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">Diperbarui</dt>
                        <dd class="sm:col-span-2 text-sm text-gray-900">{{ $contactImportant->updated_at->format('d-m-Y H:i') }}</dd>
                    </div>
                </dl>
            </div>

            <div class="space-y-4">
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 space-y-3">
                    <h4 class="text-sm font-semibold text-gray-900">Aksi</h4>
                    <a href="{{ route('admin.contact-importants.edit', $contactImportant) }}"
                       class="w-full btn-secondary justify-center">
                        <i class="fas fa-edit mr-2"></i>Edit Kontak
                    </a>
                    <form action="{{ route('admin.contact-importants.toggle-status', $contactImportant) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="w-full btn-secondary justify-center">
                            <i class="fas {{ $contactImportant->is_active ? 'fa-eye-slash' : 'fa-eye' }} mr-2"></i>
                            {{ $contactImportant->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                        </button>
                    </form>
                    <form action="{{ route('admin.contact-importants.destroy', $contactImportant) }}" method="POST"
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus kontak ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                            <i class="fas fa-trash mr-2"></i>Hapus Kontak
                        </button>
                    </form>
                </div>

                @if($contactImportant->phone)
                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                        <h4 class="text-sm font-semibold text-gray-900 mb-3">Test Telepon</h4>
                        <a href="tel:{{ $contactImportant->phone }}" class="w-full btn-primary justify-center">
                            <i class="fas fa-phone mr-2"></i>Hubungi Sekarang
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
