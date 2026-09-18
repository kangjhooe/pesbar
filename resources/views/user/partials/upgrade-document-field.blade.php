@props([
    'name',
    'id',
    'label',
    'hint' => 'PDF, JPG, PNG — maks. 5MB',
    'errorKey' => null,
])

@php
    $errorKey = $errorKey ?? $name;
@endphp

<div>
    <label for="{{ $id }}" class="block text-sm font-medium text-news-ink mb-2">
        {{ $label }} <span class="text-news-accent">*</span>
    </label>
    <div class="border-2 border-dashed border-news-line bg-white p-4 text-center hover:border-news-accent transition-colors">
        <input type="file"
               name="{{ $name }}"
               id="{{ $id }}"
               accept=".pdf,.jpg,.jpeg,.png"
               class="hidden"
               onchange="handleFileSelect(this)">
        <label for="{{ $id }}" class="cursor-pointer block">
            <i class="fas fa-cloud-upload-alt text-2xl text-news-muted mb-1"></i>
            <p class="text-sm text-news-muted mb-1">
                <span class="text-news-accent font-semibold">Klik untuk upload</span>
            </p>
            <p class="text-xs text-news-muted">PDF, JPG, PNG — maks. 5MB</p>
        </label>
        <div id="{{ $id }}_name" class="mt-2 text-sm text-news-ink hidden"></div>
    </div>
    <p class="mt-1 text-xs text-news-muted">{{ $hint }}</p>
    @error($errorKey)
        <p class="mt-1 text-sm text-news-accent">{{ $message }}</p>
    @enderror
</div>
