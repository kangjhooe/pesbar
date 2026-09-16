@props([
    'action' => '',
    'bulkId' => 'admin-bulk-form',
    'options' => [],
])

<form id="{{ $bulkId }}" method="POST" action="{{ $action }}" class="flex flex-wrap items-center gap-3">
    @csrf
    <span class="text-sm text-news-muted">
        <span id="{{ $bulkId }}-count" class="font-semibold text-news-accent">0</span> dipilih
    </span>
    <select name="action" required
            class="border border-news-line px-3 py-1.5 text-sm focus:ring-2 focus:ring-news-accent/30 focus:border-news-accent bg-white">
        <option value="">Pilih aksi</option>
        @foreach($options as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
        @endforeach
    </select>
    <button type="button"
            onclick="window.pesbarConfirmSubmit(this.form, 'Jalankan aksi massal untuk item terpilih?')"
            class="inline-flex items-center px-3 py-1.5 bg-news-accent text-white text-sm hover:bg-news-ink transition-colors">
        <i class="fas fa-bolt mr-1.5"></i>
        Jalankan
    </button>
    <button type="button"
            onclick="window.adminTableClearSelection('{{ $bulkId }}')"
            class="inline-flex items-center px-3 py-1.5 border border-news-line text-news-muted text-sm hover:text-news-ink hover:border-news-ink transition-colors">
        Batal
    </button>
    {{ $slot }}
</form>
