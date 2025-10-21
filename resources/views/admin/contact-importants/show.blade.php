@extends('layouts.admin-simple')

@section('title', 'Detail Kontak Penting')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detail Kontak Penting</h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.contact-importants.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%"><strong>Nama Instansi/Lembaga</strong></td>
                                    <td>{{ $contactImportant->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Jenis Kontak</strong></td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ ucwords(str_replace('_', ' ', $contactImportant->type)) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Nomor Telepon</strong></td>
                                    <td>
                                        @if($contactImportant->phone)
                                            <a href="tel:{{ $contactImportant->phone }}" class="text-decoration-none">
                                                <i class="fas fa-phone"></i> {{ $contactImportant->formatted_phone }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Alamat</strong></td>
                                    <td>{{ $contactImportant->address ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Deskripsi</strong></td>
                                    <td>{{ $contactImportant->description ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status</strong></td>
                                    <td>
                                        @if($contactImportant->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Tidak Aktif</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Urutan Tampil</strong></td>
                                    <td>{{ $contactImportant->sort_order }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Dibuat</strong></td>
                                    <td>{{ $contactImportant->created_at->format('d-m-Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Diperbarui</strong></td>
                                    <td>{{ $contactImportant->updated_at->format('d-m-Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-header">
                                    <h5 class="card-title mb-0">Aksi</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('admin.contact-importants.edit', $contactImportant) }}" 
                                           class="btn btn-warning">
                                            <i class="fas fa-edit"></i> Edit Kontak
                                        </a>
                                        
                                        <form action="{{ route('admin.contact-importants.toggle-status', $contactImportant) }}" 
                                              method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn {{ $contactImportant->is_active ? 'btn-secondary' : 'btn-success' }} w-100">
                                                <i class="fas {{ $contactImportant->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i> 
                                                {{ $contactImportant->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                        
                                        <form action="{{ route('admin.contact-importants.destroy', $contactImportant) }}" 
                                              method="POST"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus kontak ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger w-100">
                                                <i class="fas fa-trash"></i> Hapus Kontak
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            @if($contactImportant->phone)
                                <div class="card bg-light mt-3">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Test Telepon</h5>
                                    </div>
                                    <div class="card-body">
                                        <a href="tel:{{ $contactImportant->phone }}" class="btn btn-success w-100">
                                            <i class="fas fa-phone"></i> Hubungi Sekarang
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
