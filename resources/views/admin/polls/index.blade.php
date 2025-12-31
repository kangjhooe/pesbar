@extends('layouts.admin-simple')

@section('title', 'Manajemen Polling')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Manajemen Polling</h1>
            <p class="text-gray-500">Kelola polling dan survey untuk engagement masyarakat</p>
        </div>
        <div class="mt-4 lg:mt-0">
            <a href="{{ route('admin.polls.create') }}" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-lg shadow-md hover:from-blue-700 hover:to-blue-800 hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                <i class="fas fa-plus mr-2"></i>
                Tambah Polling
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl shadow-md hover:shadow-xl transition-all duration-300 p-6 border border-blue-200/50">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-700 mb-1">Total Polling</p>
                    <p class="text-3xl font-bold text-blue-900">{{ $stats['total'] }}</p>
                </div>
                <div class="p-3 bg-blue-500 rounded-xl shadow-lg">
                    <i class="fas fa-poll text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl shadow-md hover:shadow-xl transition-all duration-300 p-6 border border-green-200/50">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-green-700 mb-1">Aktif</p>
                    <p class="text-3xl font-bold text-green-900">{{ $stats['active'] }}</p>
                </div>
                <div class="p-3 bg-green-500 rounded-xl shadow-lg">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl shadow-md hover:shadow-xl transition-all duration-300 p-6 border border-amber-200/50">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-amber-700 mb-1">Berlangsung</p>
                    <p class="text-3xl font-bold text-amber-900">{{ $stats['running'] }}</p>
                </div>
                <div class="p-3 bg-amber-500 rounded-xl shadow-lg">
                    <i class="fas fa-play text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl shadow-md hover:shadow-xl transition-all duration-300 p-6 border border-red-200/50">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-red-700 mb-1">Selesai</p>
                    <p class="text-3xl font-bold text-red-900">{{ $stats['finished'] }}</p>
                </div>
                <div class="p-3 bg-red-500 rounded-xl shadow-lg">
                    <i class="fas fa-stop text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Polls Table -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="px-6 py-5 bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                <i class="fas fa-list mr-2 text-blue-600"></i>
                Daftar Polling
            </h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left">
                            <input type="checkbox" id="select-all" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 cursor-pointer">
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Polling</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Suara</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Periode</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($polls as $poll)
                    <tr class="hover:bg-blue-50/50 transition-colors duration-150 group">
                        <td class="px-6 py-5 whitespace-nowrap">
                            <input type="checkbox" name="poll_ids[]" value="{{ $poll->id }}" class="poll-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 cursor-pointer">
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12">
                                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-purple-500 via-pink-500 to-rose-500 flex items-center justify-center shadow-md group-hover:shadow-lg transition-shadow">
                                        <i class="fas fa-poll text-white text-lg"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">{{ Str::limit($poll->title, 50) }}</div>
                                    <div class="text-sm text-gray-500 mt-0.5">
                                        <i class="fas fa-list-ul text-xs mr-1"></i>
                                        {{ $poll->options->count() }} pilihan
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 border border-blue-200">
                                {{ $poll->poll_type_label }}
                            </span>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="p-2 bg-gray-100 rounded-lg mr-2">
                                    <i class="fas fa-vote-yea text-gray-600 text-xs"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-gray-900">{{ number_format($poll->total_votes) }}</div>
                                    <div class="text-xs text-gray-500">suara</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap">
                            <div class="text-sm text-gray-900 font-medium">
                                @if($poll->start_date)
                                    <i class="fas fa-calendar-alt text-xs mr-1 text-gray-400"></i>
                                    {{ $poll->formatted_start_date }}
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                @if($poll->end_date)
                                    <i class="fas fa-calendar-check text-xs mr-1"></i>
                                    s/d {{ $poll->formatted_end_date }}
                                @else
                                    <span class="text-gray-400">Tidak terbatas</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap">
                            <div class="flex flex-col space-y-1.5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $poll->is_active ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-gray-100 text-gray-700 border border-gray-200' }}">
                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $poll->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                    {{ $poll->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                                @php
                                    $statusColors = [
                                        'inactive' => 'bg-gray-100 text-gray-700 border-gray-200',
                                        'upcoming' => 'bg-blue-100 text-blue-700 border-blue-200',
                                        'running' => 'bg-green-100 text-green-700 border-green-200',
                                        'finished' => 'bg-red-100 text-red-700 border-red-200'
                                    ];
                                    $statusColor = $statusColors[$poll->status] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusColor }} border">
                                    {{ $poll->status_label }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap">
                            <div class="flex items-center space-x-1">
                                <a href="{{ route('admin.polls.show', $poll) }}" class="p-2 text-blue-600 hover:bg-blue-100 hover:text-blue-700 rounded-lg transition-all duration-200" title="Lihat">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.polls.edit', $poll) }}" class="p-2 text-indigo-600 hover:bg-indigo-100 hover:text-indigo-700 rounded-lg transition-all duration-200" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.polls.toggle-status', $poll) }}" method="POST" class="inline">
                                    @csrf
                                    @php
                                        $toggleClass = $poll->is_active 
                                            ? 'p-2 text-amber-600 hover:bg-amber-100 hover:text-amber-700 rounded-lg transition-all duration-200' 
                                            : 'p-2 text-green-600 hover:bg-green-100 hover:text-green-700 rounded-lg transition-all duration-200';
                                    @endphp
                                    <button type="submit" class="{{ $toggleClass }}" title="{{ $poll->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <i class="fas fa-{{ $poll->is_active ? 'pause' : 'play' }}"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.polls.reset-votes', $poll) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin mereset suara polling ini?')">
                                    @csrf
                                    <button type="submit" class="p-2 text-orange-600 hover:bg-orange-100 hover:text-orange-700 rounded-lg transition-all duration-200" title="Reset Suara">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.polls.destroy', $poll) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus polling ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-100 hover:text-red-700 rounded-lg transition-all duration-200" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-poll text-gray-400 text-3xl"></i>
                                </div>
                                <p class="text-lg font-semibold text-gray-700 mb-1">Belum ada polling</p>
                                <p class="text-sm text-gray-500 mb-4">Mulai dengan membuat polling pertama Anda</p>
                                <a href="{{ route('admin.polls.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                    <i class="fas fa-plus mr-2"></i>
                                    Buat Polling Baru
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($polls->hasPages())
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $polls->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Bulk Actions -->
<div id="bulk-actions" class="fixed bottom-6 right-6 bg-white rounded-xl shadow-2xl border border-gray-200 p-5 hidden z-50 animate-slide-up">
    <div class="flex items-center space-x-4">
        <div class="flex items-center space-x-2 bg-blue-50 px-3 py-2 rounded-lg border border-blue-200">
            <i class="fas fa-check-circle text-blue-600"></i>
            <span class="text-sm font-semibold text-gray-700">
                <span id="selected-count" class="text-blue-600 font-bold">0</span> item dipilih
            </span>
        </div>
        <form id="bulk-form" method="POST" class="flex space-x-2">
            @csrf
            <input type="hidden" name="action" id="bulk-action">
            <input type="hidden" name="poll_ids" id="bulk-poll-ids">
            <button type="submit" name="bulk-action" value="activate" class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors shadow-md hover:shadow-lg">
                <i class="fas fa-play mr-1.5"></i>Aktifkan
            </button>
            <button type="submit" name="bulk-action" value="deactivate" class="inline-flex items-center px-3 py-2 bg-amber-600 text-white text-sm font-medium rounded-lg hover:bg-amber-700 transition-colors shadow-md hover:shadow-lg">
                <i class="fas fa-pause mr-1.5"></i>Nonaktifkan
            </button>
            <button type="submit" name="bulk-action" value="reset_votes" class="inline-flex items-center px-3 py-2 bg-orange-600 text-white text-sm font-medium rounded-lg hover:bg-orange-700 transition-colors shadow-md hover:shadow-lg" onclick="return confirm('Apakah Anda yakin ingin mereset suara polling yang dipilih?')">
                <i class="fas fa-undo mr-1.5"></i>Reset Suara
            </button>
            <button type="submit" name="bulk-action" value="delete" class="inline-flex items-center px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors shadow-md hover:shadow-lg" onclick="return confirm('Apakah Anda yakin ingin menghapus polling yang dipilih?')">
                <i class="fas fa-trash mr-1.5"></i>Hapus
            </button>
        </form>
        <button onclick="hideBulkActions()" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

<style>
@keyframes slide-up {
    from {
        transform: translateY(100%);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}
.animate-slide-up {
    animation: slide-up 0.3s ease-out;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    const pollCheckboxes = document.querySelectorAll('.poll-checkbox');
    const bulkActions = document.getElementById('bulk-actions');
    const selectedCount = document.getElementById('selected-count');
    const bulkForm = document.getElementById('bulk-form');
    const bulkActionInput = document.getElementById('bulk-action');
    const bulkPollIdsInput = document.getElementById('bulk-poll-ids');

    // Select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        pollCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkActions();
    });

    // Individual checkbox functionality
    pollCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateBulkActions();
        });
    });

    // Bulk form submission
    bulkForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const selectedCheckboxes = document.querySelectorAll('.poll-checkbox:checked');
        const pollIds = Array.from(selectedCheckboxes).map(cb => cb.value);
        
        bulkPollIdsInput.value = JSON.stringify(pollIds);
        
        // Submit form
        this.submit();
    });

    function updateBulkActions() {
        const selectedCheckboxes = document.querySelectorAll('.poll-checkbox:checked');
        const count = selectedCheckboxes.length;
        
        if (count > 0) {
            selectedCount.textContent = count;
            bulkActions.classList.remove('hidden');
        } else {
            bulkActions.classList.add('hidden');
        }
    }

    function hideBulkActions() {
        bulkActions.classList.add('hidden');
        pollCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        selectAllCheckbox.checked = false;
    }
});
</script>
@endsection
