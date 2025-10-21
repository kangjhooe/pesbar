@extends('layouts.admin-simple')

@section('title', 'Manajemen Polling')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Manajemen Polling</h1>
            <p class="text-gray-600 mt-1">Kelola polling dan survey untuk engagement masyarakat</p>
        </div>
        <div class="mt-4 lg:mt-0">
            <a href="{{ route('admin.polls.create') }}" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>
                Tambah Polling
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i class="fas fa-poll text-blue-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Total Polling</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Aktif</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['active'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    <i class="fas fa-play text-yellow-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Berlangsung</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['running'] }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    <i class="fas fa-stop text-red-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Selesai</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $stats['finished'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Polls Table -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Daftar Polling</h3>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="select-all" class="rounded border-gray-300">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Polling</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Suara</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($polls as $poll)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" name="poll_ids[]" value="{{ $poll->id }}" class="poll-checkbox rounded border-gray-300">
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-lg bg-gradient-to-r from-purple-500 to-pink-600 flex items-center justify-center">
                                        <i class="fas fa-poll text-white text-sm"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ Str::limit($poll->title, 50) }}</div>
                                    <div class="text-sm text-gray-500">{{ $poll->options->count() }} pilihan</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $poll->poll_type_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ number_format($poll->total_votes) }}</div>
                            <div class="text-sm text-gray-500">suara</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                @if($poll->start_date)
                                    {{ $poll->formatted_start_date }}
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </div>
                            <div class="text-sm text-gray-500">
                                @if($poll->end_date)
                                    s/d {{ $poll->formatted_end_date }}
                                @else
                                    <span class="text-gray-400">Tidak terbatas</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col space-y-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $poll->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $poll->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $poll->status_color }}-100 text-{{ $poll->status_color }}-800">
                                    {{ $poll->status_label }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.polls.show', $poll) }}" class="text-blue-600 hover:text-blue-900" title="Lihat">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.polls.edit', $poll) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.polls.toggle-status', $poll) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-{{ $poll->is_active ? 'yellow' : 'green' }}-600 hover:text-{{ $poll->is_active ? 'yellow' : 'green' }}-900" title="{{ $poll->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <i class="fas fa-{{ $poll->is_active ? 'pause' : 'play' }}"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.polls.reset-votes', $poll) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin mereset suara polling ini?')">
                                    @csrf
                                    <button type="submit" class="text-orange-600 hover:text-orange-900" title="Reset Suara">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.polls.destroy', $poll) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus polling ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <i class="fas fa-poll text-4xl mb-4"></i>
                                <p class="text-lg font-medium">Belum ada polling</p>
                                <p class="text-sm">Mulai dengan membuat polling pertama Anda</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($polls->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $polls->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Bulk Actions -->
<div id="bulk-actions" class="fixed bottom-4 right-4 bg-white rounded-lg shadow-lg p-4 hidden">
    <div class="flex items-center space-x-4">
        <span class="text-sm text-gray-600">Aksi untuk <span id="selected-count">0</span> item:</span>
        <form id="bulk-form" method="POST" class="flex space-x-2">
            @csrf
            <input type="hidden" name="action" id="bulk-action">
            <input type="hidden" name="poll_ids" id="bulk-poll-ids">
            <button type="submit" name="bulk-action" value="activate" class="btn-sm btn-success">
                <i class="fas fa-play mr-1"></i>Aktifkan
            </button>
            <button type="submit" name="bulk-action" value="deactivate" class="btn-sm btn-warning">
                <i class="fas fa-pause mr-1"></i>Nonaktifkan
            </button>
            <button type="submit" name="bulk-action" value="reset_votes" class="btn-sm btn-warning" onclick="return confirm('Apakah Anda yakin ingin mereset suara polling yang dipilih?')">
                <i class="fas fa-undo mr-1"></i>Reset Suara
            </button>
            <button type="submit" name="bulk-action" value="delete" class="btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus polling yang dipilih?')">
                <i class="fas fa-trash mr-1"></i>Hapus
            </button>
        </form>
        <button onclick="hideBulkActions()" class="text-gray-400 hover:text-gray-600">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>

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
