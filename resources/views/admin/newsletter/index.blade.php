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
        <div class="bg-white border border-news-line p-6">
            <div class="flex items-center">
                <div class="p-3 bg-news-paper text-news-ink">
                    <i class="fas fa-envelope text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-news-muted">Total Subscriber</p>
                    <p class="text-2xl font-semibold text-news-ink">{{ $subscribers->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-news-line p-6">
            <div class="flex items-center">
                <div class="p-3 bg-news-paper text-news-ink">
                    <i class="fas fa-calendar-day text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-news-muted">Hari Ini</p>
                    <p class="text-2xl font-semibold text-news-ink">{{ $subscribers->where('created_at', '>=', today())->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white border border-news-line p-6">
            <div class="flex items-center">
                <div class="p-3 bg-news-paper text-news-ink">
                    <i class="fas fa-calendar-week text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-news-muted">Minggu Ini</p>
                    <p class="text-2xl font-semibold text-news-ink">{{ $subscribers->where('created_at', '>=', now()->subWeek())->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-4">
        <button onclick="openModal('send-newsletter-modal')"
                class="btn-primary px-6 py-2 flex items-center">
            <i class="fas fa-paper-plane mr-2"></i>
            Kirim Newsletter
        </button>

        <a href="{{ route('admin.newsletter.export') }}"
           class="btn-secondary px-6 py-2 flex items-center">
            <i class="fas fa-download mr-2"></i>
            Export Subscriber
        </a>
    </div>

    <div>
        <h3 class="text-lg font-medium text-news-ink mb-4">Daftar Subscriber</h3>

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
                <tr class="hover:bg-news-paper transition-colors">
                    <x-admin.checkbox :value="$subscriber->id" bulk-id="newsletter-bulk" name="ids[]" />
                    <x-admin.td-number :index="$subscribers->firstItem() + $loop->index" />
                    <td class="px-4 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-news-ink">{{ $subscriber->email }}</div>
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap">
                        @if($subscriber->is_active)
                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold bg-emerald-50 text-emerald-800">
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 text-xs font-semibold bg-news-paper text-news-muted">
                                Tidak Aktif
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-4 whitespace-nowrap text-sm text-news-muted">
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
                    <td colspan="6" class="px-4 py-12 text-center text-news-muted">
                        <i class="fas fa-envelope text-4xl mb-4 text-news-muted"></i>
                        <p class="text-lg font-medium text-news-ink">Belum ada subscriber</p>
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
        <div class="bg-white border-2 border-news-ink max-w-2xl w-full">
            <div class="px-6 py-4 border-b border-news-line">
                <h3 class="text-lg font-semibold text-news-ink">Kirim Newsletter</h3>
            </div>
            <form action="{{ route('admin.newsletter.send') }}" method="POST">
                @csrf
                <div class="px-6 py-4 space-y-4">
                    <div>
                        <label for="subject" class="block text-sm font-medium text-news-ink mb-1">Subject</label>
                        <input type="text" id="subject" name="subject" required value="{{ old('subject') }}"
                               class="w-full border border-news-line rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent @error('subject') border-red-500 @enderror"
                               placeholder="Judul newsletter">
                        @error('subject')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="content" class="block text-sm font-medium text-news-ink mb-1">Konten</label>
                        <textarea id="content" name="content" rows="10" required
                                  class="w-full border border-news-line rounded-lg px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent @error('content') border-red-500 @enderror"
                                  placeholder="Konten newsletter...">{{ old('content') }}</textarea>
                        @error('content')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="bg-news-paper border border-news-line rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-news-accent mr-2"></i>
                            <p class="text-sm text-news-ink">
                                Newsletter akan dikirim ke {{ $activeSubscriberCount ?? 0 }} subscriber aktif.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="px-6 py-4 border-t border-news-line flex justify-end space-x-3">
                    <button type="button" onclick="closeModal('send-newsletter-modal')"
                            class="px-4 py-2 border border-news-line text-news-ink hover:border-news-ink hover:bg-white transition-colors">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-news-accent text-white rounded-lg hover:bg-news-ink transition-colors">
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

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('bg-black')) {
        e.target.classList.add('hidden');
    }
});

@if($errors->any() && old('subject'))
openModal('send-newsletter-modal');
@endif
</script>
@endpush
@endsection
