@extends('layouts.admin-simple')

@section('title', 'Manajemen Newsletter - Admin Panel')
@section('page-title', 'Manajemen Newsletter')
@section('page-subtitle', 'Kelola subscriber dari form newsletter publik')

@section('content')
<div class="space-y-6">
    @include('admin.partials.widget-placement-note', [
        'items' => [
            'Form subscribe di sidebar beranda',
            'Form subscribe di sidebar artikel',
            'Halaman ini hanya mengelola daftar subscriber (bukan widget itu sendiri)',
        ],
    ])

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-envelope text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Subscriber</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $subscribers->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600">
                    <i class="fas fa-calendar-day text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Hari Ini</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $subscribers->where('created_at', '>=', today())->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-calendar-week text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Minggu Ini</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $subscribers->where('created_at', '>=', now()->subWeek())->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-4">
        <button onclick="openModal('send-newsletter-modal')"
                class="bg-news-accent text-white px-6 py-2 rounded-lg hover:bg-red-800 transition-colors flex items-center">
            <i class="fas fa-paper-plane mr-2"></i>
            Kirim Newsletter
        </button>

        <button onclick="exportSubscribers()"
                class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center">
            <i class="fas fa-download mr-2"></i>
            Export Subscriber
        </button>
    </div>

    <div>
        <h3 class="text-lg font-medium text-gray-900 mb-4">Daftar Subscriber</h3>

        <x-admin.table :paginator="$subscribers" :bulk="true" bulk-id="newsletter-bulk">
            <x-slot:bulkBar>
                <x-admin.bulk-bar
                    :action="route('admin.newsletter.bulk')"
                    bulk-id="newsletter-bulk"
                    :options="['activate' => 'Aktifkan', 'deactivate' => 'Nonaktifkan', 'delete' => 'Hapus']"
                />
            </x-slot:bulkBar>

            <x-slot:head>
                <x-admin.checkbox all bulk-id="newsletter-bulk" />
                <x-admin.th :sortable="false" label="#" align="center" class="w-14" />
                <x-admin.th column="email" label="Email" />
                <x-admin.th column="is_active" label="Status" />
                <x-admin.th column="created_at" label="Bergabung" />
                <x-admin.th :sortable="false" label="Aksi" align="right" />
            </x-slot:head>

            @forelse($subscribers as $subscriber)
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <x-admin.checkbox :value="$subscriber->id" bulk-id="newsletter-bulk" name="ids[]" />
                    <x-admin.td-number :index="$subscribers->firstItem() + $loop->index" />
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900">{{ $subscriber->email }}</div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        @if($subscriber->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1"></i>
                                Tidak Aktif
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $subscriber->created_at->format('d-m-Y H:i') }}
                    </td>
                    <x-admin.actions>
                        <x-admin.action-icon
                            :href="route('admin.newsletter.remove', $subscriber)"
                            method="DELETE"
                            icon="fas fa-trash"
                            color="red"
                            title="Hapus"
                            confirm="Apakah Anda yakin ingin menghapus subscriber ini?"
                        />
                    </x-admin.actions>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center text-gray-500">
                        <i class="fas fa-envelope text-4xl mb-4 text-gray-300"></i>
                        <p class="text-lg font-medium text-gray-700">Belum ada subscriber</p>
                        <p class="text-sm">Subscriber newsletter akan muncul di sini</p>
                    </td>
                </tr>
            @endforelse
        </x-admin.table>
    </div>
</div>

<!-- Send Newsletter Modal -->
<div id="send-newsletter-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Kirim Newsletter</h3>
            </div>
            <form action="{{ route('admin.newsletter.send') }}" method="POST">
                @csrf
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
                        <input type="text" id="subject" name="subject" required
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent"
                               placeholder="Judul newsletter">
                    </div>
                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-1">Konten</label>
                        <textarea id="content" name="content" rows="10" required
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent"
                                  placeholder="Konten newsletter..."></textarea>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            <p class="text-sm text-blue-800">
                                Newsletter akan dikirim ke {{ $subscribers->total() }} subscriber aktif.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('send-newsletter-modal')"
                            class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-news-accent text-white rounded-lg hover:bg-red-800 transition-colors">
                        <i class="fas fa-paper-plane mr-2"></i>Kirim Newsletter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openModal(modalId) {
    document.getElementById(modalId).classList.remove('hidden');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.add('hidden');
}

function exportSubscribers() {
    const subscribers = @json($subscribers->pluck('email'));
    const csvContent = "data:text/csv;charset=utf-8," + subscribers.join('\n');
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "newsletter_subscribers.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('bg-black')) {
        e.target.classList.add('hidden');
    }
});
</script>
@endpush
@endsection
