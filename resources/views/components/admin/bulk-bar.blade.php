@props([
    'action' => '',
    'bulkId' => 'admin-bulk-form',
    'options' => [],
])

<form id="{{ $bulkId }}" method="POST" action="{{ $action }}" class="flex flex-col sm:flex-row sm:flex-wrap sm:items-center gap-2 sm:gap-3">
    @csrf
    <span class="text-sm text-news-muted">
        <span id="{{ $bulkId }}-count" class="font-semibold text-news-accent">0</span> dipilih
    </span>
    <select name="action" required
            class="w-full sm:w-auto border border-news-line px-3 py-2 sm:py-1.5 text-sm focus:ring-2 focus:ring-news-accent/30 focus:border-news-accent bg-white">
        <option value="">Pilih aksi</option>
        @foreach($options as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
        @endforeach
    </select>
    <div class="flex flex-wrap items-center gap-2">
        <button type="button"
                onclick="(function(form){ var n=form.querySelectorAll('input[data-synced-checkbox]').length; if(!n){ window.pesbarAlert('Pilih minimal satu item terlebih dahulu.'); return; } window.pesbarConfirmSubmit(form, 'Jalankan aksi massal untuk ' + n + ' item terpilih?'); })(this.form)"
                class="inline-flex items-center justify-center px-3 py-2 sm:py-1.5 bg-news-accent text-white text-sm hover:bg-news-ink transition-colors touch-target sm:min-h-0 sm:min-w-0">
            <i class="fas fa-bolt mr-1.5"></i>
            Jalankan
        </button>
        <button type="button"
                onclick="window.adminTableClearSelection('{{ $bulkId }}')"
                class="inline-flex items-center justify-center px-3 py-2 sm:py-1.5 border border-news-line text-news-muted text-sm hover:text-news-ink hover:border-news-ink transition-colors touch-target sm:min-h-0 sm:min-w-0">
            Batal
        </button>
    </div>
    @if($slot->isNotEmpty())
        <div class="w-full sm:w-auto">
            {{ $slot }}
        </div>
    @endif
</form>
