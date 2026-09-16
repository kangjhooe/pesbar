@extends('layouts.admin-simple')

@section('title', 'Kontak Penting')
@section('page-title', 'Kontak Penting')
@section('page-subtitle', 'Kontak darurat di sidebar beranda & artikel')

@section('content')
<div class="space-y-6">
    @include('admin.partials.widget-placement-note', [
        'items' => [
            'Sidebar beranda — widget Kontak Penting',
            'Sidebar halaman artikel (versi ringkas)',
        ],
    ])

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Daftar Kontak Penting</h3>
            <p class="text-sm text-gray-600">Total {{ $contacts->total() }} kontak</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.contact-importants.export') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                <i class="fas fa-download mr-2"></i>Export
            </a>
            <a href="{{ route('admin.contact-importants.create') }}"
               class="inline-flex items-center px-4 py-2 bg-news-accent text-white rounded-lg hover:bg-red-800 transition-colors">
                <i class="fas fa-plus mr-2"></i>Tambah Kontak
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i class="fas fa-address-book text-blue-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Total Kontak</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $contacts->total() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Kontak Aktif</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $contacts->where('is_active', true)->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-amber-100 rounded-lg">
                    <i class="fas fa-times-circle text-amber-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Tidak Aktif</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $contacts->where('is_active', false)->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-indigo-100 rounded-lg">
                    <i class="fas fa-tags text-indigo-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Jenis Layanan</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $contacts->pluck('type')->unique()->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Client-side filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                <input type="text" id="searchInput" placeholder="Cari nama, telepon, atau alamat..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis</label>
                <select id="typeFilter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent">
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
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="statusFilter" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent">
                    <option value="">Semua Status</option>
                    <option value="1">Aktif</option>
                    <option value="0">Tidak Aktif</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Bulk uses contact_ids via separate JSON endpoints — keep custom bulk bar --}}
    <x-admin.table :paginator="$contacts" :bulk="true" bulk-id="contacts-bulk" id="contactsTableWrap">
        <x-slot:bulkBar>
            <div class="flex flex-wrap items-center gap-3">
                <span class="text-sm text-gray-600">
                    <span id="contacts-bulk-count" class="font-semibold text-news-accent">0</span> dipilih
                </span>
                <button type="button" onclick="bulkActivate()"
                        class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors">
                    <i class="fas fa-check mr-1.5"></i>Aktifkan
                </button>
                <button type="button" onclick="bulkDeactivate()"
                        class="inline-flex items-center px-3 py-1.5 bg-gray-600 text-white text-sm rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-times mr-1.5"></i>Nonaktifkan
                </button>
                <button type="button" onclick="bulkDelete()"
                        class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 transition-colors">
                    <i class="fas fa-trash mr-1.5"></i>Hapus
                </button>
                <button type="button" onclick="window.adminTableClearSelection('contacts-bulk')"
                        class="inline-flex items-center px-3 py-1.5 bg-gray-200 text-gray-700 text-sm rounded-lg hover:bg-gray-300 transition-colors">
                    Batal
                </button>
            </div>
        </x-slot:bulkBar>

        <x-slot:head>
            <x-admin.checkbox all bulk-id="contacts-bulk" />
            <x-admin.th :sortable="false" label="#" align="center" class="w-14" />
            <x-admin.th column="name" label="Kontak" />
            <x-admin.th column="type" label="Jenis" />
            <x-admin.th :sortable="false" label="Telepon" />
            <x-admin.th :sortable="false" label="Alamat" />
            <x-admin.th column="is_active" label="Status" />
            <x-admin.th column="sort_order" label="Urutan" />
            <x-admin.th :sortable="false" label="Aksi" align="right" />
        </x-slot:head>

        @forelse($contacts as $contact)
            <tr class="hover:bg-slate-50/80 transition-colors contact-row"
                data-type="{{ $contact->type }}"
                data-status="{{ $contact->is_active ? '1' : '0' }}">
                <x-admin.checkbox :value="$contact->id" bulk-id="contacts-bulk" name="contact_ids[]" />
                <x-admin.td-number :index="$contacts->firstItem() + $loop->index" />
                <td class="px-4 py-4">
                    <div class="text-sm font-medium text-gray-900">{{ $contact->name }}</div>
                    @if($contact->description)
                        <div class="text-sm text-gray-500">{{ Str::limit($contact->description, 50) }}</div>
                    @endif
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-100 text-slate-700 border border-slate-200">
                        {{ ucwords(str_replace('_', ' ', $contact->type)) }}
                    </span>
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm">
                    @if($contact->phone)
                        <a href="tel:{{ $contact->phone }}" class="text-green-700 hover:text-green-900">
                            <i class="fas fa-phone mr-1"></i>{{ $contact->formatted_phone }}
                        </a>
                    @else
                        <span class="text-gray-400">Tidak ada</span>
                    @endif
                </td>
                <td class="px-4 py-4 text-sm text-gray-600">
                    @if($contact->address)
                        <i class="fas fa-map-marker-alt text-amber-500 mr-1"></i>{{ Str::limit($contact->address, 35) }}
                    @else
                        <span class="text-gray-400">Tidak ada</span>
                    @endif
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    @if($contact->is_active)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                            Tidak Aktif
                        </span>
                    @endif
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $contact->sort_order }}
                </td>
                <x-admin.actions>
                    <x-admin.action-icon :href="route('admin.contact-importants.show', $contact)" icon="fas fa-eye" color="blue" title="Lihat Detail" />
                    <x-admin.action-icon :href="route('admin.contact-importants.edit', $contact)" icon="fas fa-edit" color="amber" title="Edit" />
                    <x-admin.action-icon
                        :href="route('admin.contact-importants.toggle-status', $contact)"
                        method="PATCH"
                        :icon="$contact->is_active ? 'fas fa-eye-slash' : 'fas fa-eye'"
                        :color="$contact->is_active ? 'gray' : 'green'"
                        :title="$contact->is_active ? 'Nonaktifkan' : 'Aktifkan'"
                    />
                    <x-admin.action-icon
                        :href="route('admin.contact-importants.destroy', $contact)"
                        method="DELETE"
                        icon="fas fa-trash"
                        color="red"
                        title="Hapus"
                        confirm="Apakah Anda yakin ingin menghapus kontak ini?"
                    />
                </x-admin.actions>
            </tr>
        @empty
            <tr>
                <td colspan="9" class="px-4 py-12 text-center text-gray-500">
                    <i class="fas fa-address-book text-4xl mb-4 text-gray-300"></i>
                    <p class="text-lg font-medium text-gray-700">Belum ada kontak penting</p>
                    <p class="text-sm mb-4">Mulai dengan menambahkan kontak penting pertama Anda</p>
                    <a href="{{ route('admin.contact-importants.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-news-accent text-white rounded-lg hover:bg-red-800 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Tambah Kontak Pertama
                    </a>
                </td>
            </tr>
        @endforelse
    </x-admin.table>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const typeFilter = document.getElementById('typeFilter');
    const statusFilter = document.getElementById('statusFilter');

    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedType = typeFilter.value;
        const selectedStatus = statusFilter.value;
        const rows = document.querySelectorAll('.contact-row');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const type = row.getAttribute('data-type');
            const status = row.getAttribute('data-status');
            const matchesSearch = !searchTerm || text.includes(searchTerm);
            const matchesType = !selectedType || type === selectedType;
            const matchesStatus = !selectedStatus || status === selectedStatus;
            row.style.display = (matchesSearch && matchesType && matchesStatus) ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', filterTable);
    typeFilter.addEventListener('change', filterTable);
    statusFilter.addEventListener('change', filterTable);
});

function getSelectedContactIds() {
    return Array.from(document.querySelectorAll('.admin-row-checkbox[data-bulk-id="contacts-bulk"]:checked'))
        .map(cb => cb.value);
}

async function bulkRequest(url, confirmMsg) {
    const contactIds = getSelectedContactIds();
    if (contactIds.length === 0) return;
    const ok = await window.pesbarConfirm(confirmMsg.replace('{n}', contactIds.length), { danger: true });
    if (!ok) return;

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ contact_ids: contactIds })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Terjadi kesalahan.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat memproses aksi massal.');
    });
}

function bulkActivate() {
    bulkRequest('{{ route("admin.contact-importants.bulk-activate") }}', 'Aktifkan {n} kontak yang dipilih?');
}

function bulkDeactivate() {
    bulkRequest('{{ route("admin.contact-importants.bulk-deactivate") }}', 'Nonaktifkan {n} kontak yang dipilih?');
}

function bulkDelete() {
    bulkRequest('{{ route("admin.contact-importants.bulk-delete") }}', 'Hapus {n} kontak yang dipilih? Tindakan ini tidak dapat dibatalkan.');
}
</script>
@endpush
@endsection
