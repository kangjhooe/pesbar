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
            <h3 class="text-lg font-semibold text-news-ink">Daftar Kontak Penting</h3>
            <p class="text-sm text-news-muted">Total {{ $contacts->total() }} kontak</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.contact-importants.export') }}"
               class="inline-flex items-center px-4 py-2 border border-news-line text-news-ink hover:border-news-ink hover:bg-white transition-colors">
                <i class="fas fa-download mr-2"></i>Export
            </a>
            <a href="{{ route('admin.contact-importants.create') }}"
               class="inline-flex items-center px-4 py-2 bg-news-accent text-white rounded-lg hover:bg-news-ink transition-colors">
                <i class="fas fa-plus mr-2"></i>Tambah Kontak
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white border border-news-line p-4">
            <div class="flex items-center">
                <div class="p-2 bg-news-paper">
                    <i class="fas fa-address-book text-news-accent"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-news-muted">Total Kontak</p>
                    <p class="text-2xl font-bold text-news-ink">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-news-line p-4">
            <div class="flex items-center">
                <div class="p-2 bg-news-paper">
                    <i class="fas fa-check-circle text-news-ink"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-news-muted">Kontak Aktif</p>
                    <p class="text-2xl font-bold text-news-ink">{{ $stats['active'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-news-line p-4">
            <div class="flex items-center">
                <div class="p-2 bg-news-paper">
                    <i class="fas fa-times-circle text-news-ink"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-news-muted">Tidak Aktif</p>
                    <p class="text-2xl font-bold text-news-ink">{{ $stats['inactive'] }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white border border-news-line p-4">
            <div class="flex items-center">
                <div class="p-2 bg-news-paper">
                    <i class="fas fa-tags text-news-ink"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-news-muted">Jenis Layanan</p>
                    <p class="text-2xl font-bold text-news-ink">{{ $stats['types'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Server-side filters -->
    <div class="bg-white border border-news-line p-4">
        <form method="GET" action="{{ route('admin.contact-importants.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-news-ink mb-1">Cari</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama, telepon, atau alamat..."
                       class="w-full border border-news-line rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent">
            </div>
            <div>
                <label class="block text-sm font-medium text-news-ink mb-1">Jenis</label>
                <select name="type" class="w-full border border-news-line rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent">
                    <option value="">Semua Jenis</option>
                    @foreach(['polisi'=>'Polisi','rumah_sakit'=>'Rumah Sakit','pemadam_kebakaran'=>'Pemadam Kebakaran','ambulans'=>'Ambulans','posko_bencana'=>'Posko Bencana','kantor_camat'=>'Kantor Camat','puskesmas'=>'Puskesmas','lainnya'=>'Lainnya'] as $value => $label)
                        <option value="{{ $value }}" {{ request('type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-news-ink mb-1">Status</label>
                <select name="status" class="w-full border border-news-line rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>
            <div class="md:col-span-4 flex flex-wrap gap-2">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-news-ink text-white text-sm hover:bg-news-accent">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('admin.contact-importants.index') }}" class="inline-flex items-center px-4 py-2 border border-news-line text-sm text-news-ink hover:bg-news-paper">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Bulk uses contact_ids via separate JSON endpoints — keep custom bulk bar --}}
    <x-admin.table :paginator="$contacts" :bulk="true" bulk-id="contacts-bulk" id="contactsTableWrap">
        <x-slot:bulkBar>
            <div class="flex flex-wrap items-center gap-3">
                <span class="text-sm text-news-muted">
                    <span id="contacts-bulk-count" class="font-semibold text-news-accent">0</span> dipilih
                </span>
                <button type="button" onclick="bulkActivate()"
                        class="inline-flex items-center px-3 py-1.5 bg-news-accent text-white text-sm hover:bg-news-ink transition-colors">
                    <i class="fas fa-check mr-1.5"></i>Aktifkan
                </button>
                <button type="button" onclick="bulkDeactivate()"
                        class="inline-flex items-center px-3 py-1.5 bg-news-ink text-white text-sm rounded-lg hover:bg-news-accent transition-colors">
                    <i class="fas fa-times mr-1.5"></i>Nonaktifkan
                </button>
                <button type="button" onclick="bulkDelete()"
                        class="inline-flex items-center px-3 py-1.5 border border-news-accent text-news-accent text-sm hover:bg-news-accent hover:text-white transition-colors">
                    <i class="fas fa-trash mr-1.5"></i>Hapus
                </button>
                <button type="button" onclick="window.adminTableClearSelection('contacts-bulk')"
                        class="inline-flex items-center px-3 py-1.5 border border-news-line text-news-ink text-sm hover:border-news-ink hover:bg-white transition-colors">
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
            <tr class="hover:bg-news-paper transition-colors contact-row"
                data-type="{{ $contact->type }}"
                data-status="{{ $contact->is_active ? '1' : '0' }}">
                <x-admin.checkbox :value="$contact->id" bulk-id="contacts-bulk" name="contact_ids[]" />
                <x-admin.td-number :index="$contacts->firstItem() + $loop->index" />
                <td class="px-4 py-4">
                    <div class="text-sm font-medium text-news-ink">{{ $contact->name }}</div>
                    @if($contact->description)
                        <div class="text-sm text-news-muted">{{ Str::limit($contact->description, 50) }}</div>
                    @endif
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-news-paper text-news-ink border border-news-line">
                        {{ ucwords(str_replace('_', ' ', $contact->type)) }}
                    </span>
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm">
                    @if($contact->phone)
                        <a href="tel:{{ $contact->phone }}" class="text-green-700 hover:text-green-900">
                            <i class="fas fa-phone mr-1"></i>{{ $contact->formatted_phone }}
                        </a>
                    @else
                        <span class="text-news-muted">Tidak ada</span>
                    @endif
                </td>
                <td class="px-4 py-4 text-sm text-news-muted">
                    @if($contact->address)
                        <i class="fas fa-map-marker-alt text-amber-500 mr-1"></i>{{ Str::limit($contact->address, 35) }}
                    @else
                        <span class="text-news-muted">Tidak ada</span>
                    @endif
                </td>
                <td class="px-4 py-4 whitespace-nowrap">
                    @if($contact->is_active)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-800">
                            Aktif
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-news-paper text-news-ink">
                            Tidak Aktif
                        </span>
                    @endif
                </td>
                <td class="px-4 py-4 whitespace-nowrap text-sm text-news-muted">
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
                <td colspan="9" class="px-4 py-12 text-center text-news-muted">
                    <i class="fas fa-address-book text-4xl mb-4 text-news-muted"></i>
                    <p class="text-lg font-medium text-news-ink">Belum ada kontak penting</p>
                    <p class="text-sm mb-4">Mulai dengan menambahkan kontak penting pertama Anda</p>
                    <a href="{{ route('admin.contact-importants.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-news-accent text-white rounded-lg hover:bg-news-ink transition-colors">
                        <i class="fas fa-plus mr-2"></i>Tambah Kontak Pertama
                    </a>
                </td>
            </tr>
        @endforelse
    </x-admin.table>
</div>

@push('scripts')
<script>
function getSelectedContactIds() {
    return Array.from(document.querySelectorAll('.admin-row-checkbox[data-bulk-id="contacts-bulk"]:checked'))
        .map(cb => cb.value);
}

async function bulkRequest(url, confirmMsg) {
    const contactIds = getSelectedContactIds();
    if (contactIds.length === 0) {
        window.pesbarAlert('Pilih minimal satu kontak terlebih dahulu.');
        return;
    }
    const ok = await window.pesbarConfirm(confirmMsg.replace('{n}', contactIds.length), { danger: true });
    if (!ok) return;

    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ contact_ids: contactIds })
        });
        const data = await response.json().catch(() => ({}));
        if (response.ok && data.success) {
            location.reload();
            return;
        }
        const msg = data.message
            || (data.errors ? Object.values(data.errors).flat()[0] : null)
            || 'Terjadi kesalahan saat memproses aksi massal.';
        window.pesbarAlert(msg);
    } catch (error) {
        console.error('Error:', error);
        window.pesbarAlert('Terjadi kesalahan saat memproses aksi massal.');
    }
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
