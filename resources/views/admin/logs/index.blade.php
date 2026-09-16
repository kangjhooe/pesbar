@extends('layouts.admin-simple')

@section('title', 'Log Sistem - Admin Panel')
@section('page-title', 'Log Sistem')
@section('page-subtitle', 'Monitor log aplikasi dan error')

@section('content')
<div class="space-y-6">
    <div class="bg-white border border-news-line p-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="text-lg font-semibold text-news-ink">Log Laravel</h3>
                <p class="text-sm text-news-muted">Hingga 100 baris terakhir (dari 500 baris paling baru)</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.logs.index', array_filter(['level' => $level ?: null, 'q' => $search ?: null])) }}"
                   class="bg-news-accent text-white px-4 py-2 hover:bg-news-ink transition-colors inline-flex items-center">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Refresh
                </a>

                <form action="{{ route('admin.logs.clear') }}" method="POST" class="inline">
                    @csrf
                    <button type="button"
                            class="bg-news-accent text-white px-4 py-2 hover:bg-news-ink transition-colors inline-flex items-center"
                            onclick="window.pesbarConfirmSubmit(this.form, 'Hapus semua isi laravel.log?', {danger:true})">
                        <i class="fas fa-trash mr-2"></i>
                        Hapus Log
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="bg-white border border-news-line p-6">
        <h3 class="text-lg font-semibold text-news-ink mb-4">Filter Log</h3>
        <form method="GET" action="{{ route('admin.logs.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-news-ink mb-1">Level</label>
                <select name="level" class="w-full border border-news-line px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent">
                    <option value="">Semua Level</option>
                    @foreach(['ERROR','WARNING','INFO','DEBUG'] as $lvl)
                        <option value="{{ $lvl }}" {{ $level === $lvl ? 'selected' : '' }}>{{ $lvl }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-news-ink mb-1">Pencarian</label>
                <input type="text" name="q" value="{{ $search }}" placeholder="Cari dalam log..."
                       class="w-full border border-news-line px-3 py-2 focus:ring-2 focus:ring-news-accent focus:border-news-accent">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-news-ink text-white px-4 py-2 hover:bg-news-accent transition-colors">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('admin.logs.index') }}" class="px-4 py-2 border border-news-line text-news-ink hover:bg-news-paper">Reset</a>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white border border-news-line p-6">
            <p class="text-sm font-medium text-news-muted">Error</p>
            <p class="text-2xl font-semibold text-news-ink">{{ collect($logs)->filter(fn($log) => str_contains(strtoupper($log), 'ERROR'))->count() }}</p>
        </div>
        <div class="bg-white border border-news-line p-6">
            <p class="text-sm font-medium text-news-muted">Warning</p>
            <p class="text-2xl font-semibold text-news-ink">{{ collect($logs)->filter(fn($log) => str_contains(strtoupper($log), 'WARNING'))->count() }}</p>
        </div>
        <div class="bg-white border border-news-line p-6">
            <p class="text-sm font-medium text-news-muted">Info</p>
            <p class="text-2xl font-semibold text-news-ink">{{ collect($logs)->filter(fn($log) => str_contains(strtoupper($log), 'INFO'))->count() }}</p>
        </div>
        <div class="bg-white border border-news-line p-6">
            <p class="text-sm font-medium text-news-muted">Ditampilkan</p>
            <p class="text-2xl font-semibold text-news-ink">{{ count($logs) }}</p>
        </div>
    </div>

    <div class="bg-white border border-news-line">
        <div class="px-6 py-4 border-b border-news-line">
            <h3 class="text-lg font-semibold text-news-ink">Isi Log</h3>
        </div>

        @if(count($logs) > 0)
        <div class="p-6">
            <div class="bg-gray-900 p-4 overflow-x-auto max-h-[32rem] overflow-y-auto">
                <pre class="text-sm font-mono whitespace-pre-wrap">@foreach($logs as $log)
@php
    $upper = strtoupper($log);
    $color = str_contains($upper, 'ERROR') || str_contains($upper, 'CRITICAL')
        ? 'text-red-400'
        : (str_contains($upper, 'WARNING') ? 'text-yellow-400' : (str_contains($upper, 'INFO') ? 'text-blue-400' : 'text-green-400'));
@endphp
<span class="{{ $color }}">{{ $log }}</span>
@endforeach</pre>
            </div>
        </div>
        @else
        <div class="p-12 text-center text-news-muted">
            <i class="fas fa-file-alt text-4xl mb-4"></i>
            <p class="text-lg font-medium">Log kosong / tidak ada yang cocok filter</p>
        </div>
        @endif
    </div>

    <div class="bg-white border border-news-line p-6">
        <h3 class="text-lg font-semibold text-news-ink mb-4">Informasi Log</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
            <div>
                <h4 class="font-medium text-news-ink mb-1">File</h4>
                <p class="text-news-muted font-mono">storage/logs/laravel.log</p>
            </div>
            <div>
                <h4 class="font-medium text-news-ink mb-1">Ukuran</h4>
                <p class="text-news-muted">{{ number_format(($fileSize ?? 0) / 1024, 2) }} KB</p>
            </div>
            <div>
                <h4 class="font-medium text-news-ink mb-1">Terakhir diupdate</h4>
                <p class="text-news-muted">
                    {{ !empty($lastModified) ? date('d-m-Y H:i:s', $lastModified) : '—' }}
                </p>
            </div>
            <div>
                <h4 class="font-medium text-news-ink mb-1">Default log level</h4>
                <p class="text-news-muted">{{ config('logging.channels.stack.level', config('logging.default')) }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
