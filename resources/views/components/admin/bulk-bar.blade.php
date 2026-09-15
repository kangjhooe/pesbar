@props([
    'action' => '',
    'bulkId' => 'admin-bulk-form',
    'options' => [],
])

<form id="{{ $bulkId }}" method="POST" action="{{ $action }}" class="flex flex-wrap items-center gap-3">
    @csrf
    <span class="text-sm text-gray-600">
        <span id="{{ $bulkId }}-count" class="font-semibold text-news-accent">0</span> dipilih
    </span>
    <select name="action" required
            class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-news-accent/30 focus:border-news-accent">
        <option value="">Pilih aksi</option>
        @foreach($options as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
        @endforeach
    </select>
    <button type="submit"
            onclick="return confirm('Jalankan aksi massal untuk item terpilih?')"
            class="inline-flex items-center px-3 py-1.5 bg-news-accent text-white text-sm rounded-lg hover:bg-red-800 transition-colors">
        <i class="fas fa-bolt mr-1.5"></i>
        Jalankan
    </button>
    <button type="button"
            onclick="window.adminTableClearSelection('{{ $bulkId }}')"
            class="inline-flex items-center px-3 py-1.5 bg-gray-200 text-gray-700 text-sm rounded-lg hover:bg-gray-300 transition-colors">
        Batal
    </button>
    {{ $slot }}
</form>
