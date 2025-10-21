@extends('layouts.admin-simple')

@section('title', 'Kontak Penting')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 mb-1 text-dark">
                        <i class="fas fa-address-book text-primary me-2"></i>
                        Kontak Penting
                    </h2>
                    <p class="text-muted mb-0">Kelola daftar kontak penting untuk layanan darurat</p>
                </div>
                <a href="{{ route('admin.contact-importants.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Tambah Kontak
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Kontak
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $contacts->total() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-address-book fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Kontak Aktif
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $contacts->where('is_active', true)->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Kontak Tidak Aktif
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $contacts->where('is_active', false)->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Jenis Layanan
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $contacts->pluck('type')->unique()->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Kontak Penting</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" 
                           data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" 
                             aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">Aksi:</div>
                            <a class="dropdown-item" href="#" onclick="exportContacts()">
                                <i class="fas fa-download fa-sm fa-fw mr-2 text-gray-400"></i>
                                Export Data
                            </a>
                            <a class="dropdown-item" href="#" onclick="importContacts()">
                                <i class="fas fa-upload fa-sm fa-fw mr-2 text-gray-400"></i>
                                Import Data
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Search and Filter Section -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control" id="searchInput" 
                                       placeholder="Cari nama, telepon, atau alamat...">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="typeFilter">
                                <option value="">Semua Jenis</option>
                                <option value="polisi">Polisi</option>
                                <option value="rumah_sakit">Rumah Sakit</option>
                                <option value="pemadam_kebakaran">Pemadam Kebakaran</option>
                                <option value="ambulans">Ambulans</option>
                                <option value="posko_bencana">Posko Bencana</option>
                                <option value="kantor_camat">Kantor Camat</option>
                                <option value="puskesmas">Puskesmas</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="statusFilter">
                                <option value="">Semua Status</option>
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover" id="contactsTable">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                        </div>
                                    </th>
                                    <th width="5%">No</th>
                                    <th width="25%">Kontak</th>
                                    <th width="15%">Jenis</th>
                                    <th width="15%">Telepon</th>
                                    <th width="20%">Alamat</th>
                                    <th width="10%">Status</th>
                                    <th width="5%">Urutan</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($contacts as $index => $contact)
                                    <tr data-type="{{ $contact->type }}" data-status="{{ $contact->is_active ? '1' : '0' }}">
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input contact-checkbox" type="checkbox" 
                                                       value="{{ $contact->id }}">
                                            </div>
                                        </td>
                                        <td class="fw-bold text-primary">{{ $contacts->firstItem() + $index }}</td>
                                        <td>
                                            <div class="contact-info">
                                                <h6 class="mb-1 fw-bold text-dark">{{ $contact->name }}</h6>
                                                @if($contact->description)
                                                    <small class="text-muted">{{ Str::limit($contact->description, 50) }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border">
                                                <i class="fas fa-tag me-1"></i>
                                                {{ ucwords(str_replace('_', ' ', $contact->type)) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($contact->phone)
                                                <a href="tel:{{ $contact->phone }}" class="text-decoration-none d-flex align-items-center">
                                                    <i class="fas fa-phone text-success me-2"></i>
                                                    <span class="fw-medium">{{ $contact->formatted_phone }}</span>
                                                </a>
                                            @else
                                                <span class="text-muted">
                                                    <i class="fas fa-phone-slash me-2"></i>Tidak ada
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($contact->address)
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-map-marker-alt text-warning me-2"></i>
                                                    <span>{{ Str::limit($contact->address, 35) }}</span>
                                                </div>
                                            @else
                                                <span class="text-muted">
                                                    <i class="fas fa-map-marker-alt me-2"></i>Tidak ada
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($contact->is_active)
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle me-1"></i>Aktif
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-times-circle me-1"></i>Tidak Aktif
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ $contact->sort_order }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.contact-importants.show', $contact) }}" 
                                                   class="btn btn-sm btn-outline-info" title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.contact-importants.edit', $contact) }}" 
                                                   class="btn btn-sm btn-outline-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('admin.contact-importants.toggle-status', $contact) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm {{ $contact->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }}" 
                                                            title="{{ $contact->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                        <i class="fas {{ $contact->is_active ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('admin.contact-importants.destroy', $contact) }}" 
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus kontak ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <div class="empty-state">
                                                <i class="fas fa-address-book fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">Belum ada kontak penting</h5>
                                                <p class="text-muted">Mulai dengan menambahkan kontak penting pertama Anda</p>
                                                <a href="{{ route('admin.contact-importants.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus me-2"></i>Tambah Kontak Pertama
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Bulk Actions -->
                    <div class="row mt-3" id="bulkActions" style="display: none;">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body py-2">
                                    <div class="d-flex align-items-center">
                                        <span class="me-3">
                                            <span id="selectedCount">0</span> item dipilih
                                        </span>
                                        <div class="btn-group">
                                            <button class="btn btn-sm btn-success" onclick="bulkActivate()">
                                                <i class="fas fa-check me-1"></i>Aktifkan
                                            </button>
                                            <button class="btn btn-sm btn-secondary" onclick="bulkDeactivate()">
                                                <i class="fas fa-times me-1"></i>Nonaktifkan
                                            </button>
                                            <button class="btn btn-sm btn-danger" onclick="bulkDelete()">
                                                <i class="fas fa-trash me-1"></i>Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($contacts->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $contacts->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
/* Border left colors for stats cards */
.border-left-primary { border-left: 4px solid #4e73df !important; }
.border-left-success { border-left: 4px solid #1cc88a !important; }
.border-left-warning { border-left: 4px solid #f6c23e !important; }
.border-left-info { border-left: 4px solid #36b9cc !important; }

/* Text colors */
.text-gray-800 { color: #5a5c69 !important; }
.text-gray-300 { color: #dddfeb !important; }
.text-gray-400 { color: #858796 !important; }
.text-primary { color: #4e73df !important; }
.text-success { color: #1cc88a !important; }
.text-warning { color: #f6c23e !important; }
.text-info { color: #36b9cc !important; }
.text-secondary { color: #858796 !important; }
.text-muted { color: #6c757d !important; }

/* Typography */
.font-weight-bold { font-weight: 700 !important; }
.text-xs { font-size: 0.7rem !important; }
.text-uppercase { text-transform: uppercase !important; }
.h3 { font-size: 1.75rem !important; }
.h5 { font-size: 1.25rem !important; }
.h6 { font-size: 1rem !important; }
.fw-bold { font-weight: 700 !important; }
.fw-medium { font-weight: 500 !important; }

/* Spacing */
.mb-0 { margin-bottom: 0 !important; }
.mb-1 { margin-bottom: 0.25rem !important; }
.mb-3 { margin-bottom: 1rem !important; }
.mb-4 { margin-bottom: 1.5rem !important; }
.mt-3 { margin-top: 1rem !important; }
.mt-4 { margin-top: 1.5rem !important; }
.me-1 { margin-right: 0.25rem !important; }
.me-2 { margin-right: 0.5rem !important; }
.me-3 { margin-right: 1rem !important; }
.mr-2 { margin-right: 0.5rem !important; }
.py-2 { padding-top: 0.5rem !important; padding-bottom: 0.5rem !important; }
.py-3 { padding-top: 1rem !important; padding-bottom: 1rem !important; }
.py-5 { padding-top: 3rem !important; padding-bottom: 3rem !important; }

/* Layout */
.h-100 { height: 100% !important; }
.d-flex { display: flex !important; }
.justify-content-between { justify-content: space-between !important; }
.justify-content-center { justify-content: center !important; }
.align-items-center { align-items: center !important; }
.flex-row { flex-direction: row !important; }
.no-gutters { margin-right: 0 !important; margin-left: 0 !important; }
.no-gutters > .col, .no-gutters > [class*="col-"] { padding-right: 0 !important; padding-left: 0 !important; }

/* Contact info styling */
.contact-info {
    padding: 0.5rem 0;
}

.contact-info h6 {
    font-size: 1rem;
    line-height: 1.3;
    margin-bottom: 0.25rem;
}

.contact-info small {
    font-size: 0.8rem;
    line-height: 1.2;
    display: block;
    margin-top: 0.25rem;
}

/* Empty state */
.empty-state { padding: 2rem; }

/* Animations */
.animated--fade-in { animation: fadeIn 0.3s ease-in-out; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

/* Table improvements */
.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
    transform: translateY(-1px);
    transition: all 0.2s ease;
}

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
    background-color: #f8f9fa;
    padding: 1rem 0.75rem;
}

.table td {
    padding: 1rem 0.75rem;
    vertical-align: middle;
    border-top: 1px solid #e9ecef;
}

/* Badge improvements */
.badge {
    font-size: 0.75rem;
    padding: 0.5rem 0.75rem;
    border-radius: 0.5rem;
}

/* Button improvements */
.btn-group .btn {
    border-radius: 0.375rem;
    margin-right: 0.25rem;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

/* Responsive */
@media (max-width: 768px) {
    .table-responsive { font-size: 0.875rem; }
    .btn-group .btn { padding: 0.25rem 0.5rem; font-size: 0.75rem; }
    .contact-info h6 { font-size: 0.9rem; }
    .contact-info small { font-size: 0.75rem; }
}
</style>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('searchInput');
    const typeFilter = document.getElementById('typeFilter');
    const statusFilter = document.getElementById('statusFilter');
    const table = document.getElementById('contactsTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedType = typeFilter.value;
        const selectedStatus = statusFilter.value;

        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const cells = row.getElementsByTagName('td');
            
            if (cells.length === 0) continue;
            
            const name = cells[2].textContent.toLowerCase();
            const phone = cells[4].textContent.toLowerCase();
            const address = cells[5].textContent.toLowerCase();
            const type = row.getAttribute('data-type');
            const status = row.getAttribute('data-status');

            const matchesSearch = searchTerm === '' || 
                name.includes(searchTerm) || 
                phone.includes(searchTerm) || 
                address.includes(searchTerm);
            
            const matchesType = selectedType === '' || type === selectedType;
            const matchesStatus = selectedStatus === '' || status === selectedStatus;

            if (matchesSearch && matchesType && matchesStatus) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        }
    }

    searchInput.addEventListener('input', filterTable);
    typeFilter.addEventListener('change', filterTable);
    statusFilter.addEventListener('change', filterTable);

    // Bulk selection functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const contactCheckboxes = document.querySelectorAll('.contact-checkbox');
    const bulkActions = document.getElementById('bulkActions');
    const selectedCount = document.getElementById('selectedCount');

    selectAllCheckbox.addEventListener('change', function() {
        contactCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkActions();
    });

    contactCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateBulkActions();
            updateSelectAllState();
        });
    });

    function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.contact-checkbox:checked');
        const count = checkedBoxes.length;
        
        selectedCount.textContent = count;
        
        if (count > 0) {
            bulkActions.style.display = 'block';
        } else {
            bulkActions.style.display = 'none';
        }
    }

    function updateSelectAllState() {
        const totalCheckboxes = contactCheckboxes.length;
        const checkedCheckboxes = document.querySelectorAll('.contact-checkbox:checked').length;
        
        if (checkedCheckboxes === 0) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = false;
        } else if (checkedCheckboxes === totalCheckboxes) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = true;
        } else {
            selectAllCheckbox.indeterminate = true;
        }
    }
});

// Bulk action functions
function bulkActivate() {
    const checkedBoxes = document.querySelectorAll('.contact-checkbox:checked');
    if (checkedBoxes.length === 0) return;
    
    if (confirm(`Aktifkan ${checkedBoxes.length} kontak yang dipilih?`)) {
        const contactIds = Array.from(checkedBoxes).map(cb => cb.value);
        
        fetch('{{ route("admin.contact-importants.bulk-activate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ contact_ids: contactIds })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                location.reload();
            } else {
                showAlert('error', 'Terjadi kesalahan saat mengaktifkan kontak.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'Terjadi kesalahan saat mengaktifkan kontak.');
        });
    }
}

function bulkDeactivate() {
    const checkedBoxes = document.querySelectorAll('.contact-checkbox:checked');
    if (checkedBoxes.length === 0) return;
    
    if (confirm(`Nonaktifkan ${checkedBoxes.length} kontak yang dipilih?`)) {
        const contactIds = Array.from(checkedBoxes).map(cb => cb.value);
        
        fetch('{{ route("admin.contact-importants.bulk-deactivate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ contact_ids: contactIds })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                location.reload();
            } else {
                showAlert('error', 'Terjadi kesalahan saat menonaktifkan kontak.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'Terjadi kesalahan saat menonaktifkan kontak.');
        });
    }
}

function bulkDelete() {
    const checkedBoxes = document.querySelectorAll('.contact-checkbox:checked');
    if (checkedBoxes.length === 0) return;
    
    if (confirm(`Hapus ${checkedBoxes.length} kontak yang dipilih? Tindakan ini tidak dapat dibatalkan.`)) {
        const contactIds = Array.from(checkedBoxes).map(cb => cb.value);
        
        fetch('{{ route("admin.contact-importants.bulk-delete") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ contact_ids: contactIds })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', data.message);
                location.reload();
            } else {
                showAlert('error', 'Terjadi kesalahan saat menghapus kontak.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'Terjadi kesalahan saat menghapus kontak.');
        });
    }
}

function exportContacts() {
    window.location.href = '{{ route("admin.contact-importants.export") }}';
}

function importContacts() {
    alert('Fitur import akan segera tersedia.');
}

function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="fas ${iconClass} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    const cardBody = document.querySelector('.card-body');
    cardBody.insertAdjacentHTML('afterbegin', alertHtml);
    
    setTimeout(() => {
        const alert = cardBody.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 5000);
}
</script>
@endsection
