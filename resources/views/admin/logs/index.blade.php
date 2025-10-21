@extends('layouts.admin-simple')

@section('title', 'Log Sistem - Admin Panel')
@section('page-title', 'Log Sistem')
@section('page-subtitle', 'Monitor log aplikasi dan error')

@section('content')
<div class="space-y-6">
    <!-- Log Actions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Log Laravel</h3>
                <p class="text-sm text-gray-600">100 baris terakhir dari log aplikasi</p>
            </div>
            <div class="flex gap-2">
                <button onclick="refreshLogs()" 
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                    <i class="fas fa-sync-alt mr-2"></i>
                    Refresh
                </button>
                
                <a href="{{ route('admin.logs.clear') }}" 
                   class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors flex items-center"
                   onclick="return confirm('Apakah Anda yakin ingin menghapus semua log?')">
                    <i class="fas fa-trash mr-2"></i>
                    Hapus Log
                </a>
            </div>
        </div>
    </div>

    <!-- Log Content -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Isi Log</h3>
        </div>
        
        @if(count($logs) > 0)
        <div class="p-6">
            <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                <pre class="text-green-400 text-sm font-mono whitespace-pre-wrap">@foreach($logs as $log){{ $log }}@endforeach</pre>
            </div>
        </div>
        @else
        <div class="p-12 text-center">
            <div class="text-gray-500">
                <i class="fas fa-file-alt text-4xl mb-4"></i>
                <p class="text-lg font-medium">Log kosong</p>
                <p class="text-sm">Tidak ada log yang tersedia</p>
            </div>
        </div>
        @endif
    </div>

    <!-- Log Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600">
                    <i class="fas fa-exclamation-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Error</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ collect($logs)->filter(function($log) { return str_contains($log, 'ERROR'); })->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                    <i class="fas fa-exclamation-triangle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Warning</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ collect($logs)->filter(function($log) { return str_contains($log, 'WARNING'); })->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                    <i class="fas fa-info-circle text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Info</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ collect($logs)->filter(function($log) { return str_contains($log, 'INFO'); })->count() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-gray-100 text-gray-600">
                    <i class="fas fa-list text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Log</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ count($logs) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Log Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Filter Log</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Level</label>
                <select id="logLevel" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Semua Level</option>
                    <option value="ERROR">Error</option>
                    <option value="WARNING">Warning</option>
                    <option value="INFO">Info</option>
                    <option value="DEBUG">Debug</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
                <input type="text" id="logSearch" placeholder="Cari dalam log..."
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="flex items-end">
                <button onclick="filterLogs()" class="w-full bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
            </div>
        </div>
    </div>

    <!-- System Information -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Log</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-sm font-medium text-gray-900 mb-2">File Log</h4>
                <p class="text-sm text-gray-600 font-mono">storage/logs/laravel.log</p>
            </div>
            
            <div>
                <h4 class="text-sm font-medium text-gray-900 mb-2">Ukuran File</h4>
                <p class="text-sm text-gray-600">
                    @php
                        $logFile = storage_path('logs/laravel.log');
                        $fileSize = file_exists($logFile) ? filesize($logFile) : 0;
                    @endphp
                    {{ number_format($fileSize / 1024, 2) }} KB
                </p>
            </div>
            
            <div>
                <h4 class="text-sm font-medium text-gray-900 mb-2">Terakhir Diupdate</h4>
                <p class="text-sm text-gray-600">
                    @if(file_exists($logFile))
                        {{ date('d-m-Y H:i:s', filemtime($logFile)) }}
                    @else
                        File tidak ditemukan
                    @endif
                </p>
            </div>
            
            <div>
                <h4 class="text-sm font-medium text-gray-900 mb-2">Log Level</h4>
                <p class="text-sm text-gray-600">{{ config('logging.level', 'debug') }}</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function refreshLogs() {
    window.location.reload();
}

function filterLogs() {
    const level = document.getElementById('logLevel').value;
    const search = document.getElementById('logSearch').value;
    
    // Here you would implement the filtering logic
    // For now, just show an alert
    alert('Fitur filter log akan segera tersedia.');
}

// Auto refresh logs every 30 seconds
setInterval(function() {
    // You can implement auto-refresh here if needed
}, 30000);

// Syntax highlighting for log levels
document.addEventListener('DOMContentLoaded', function() {
    const logContent = document.querySelector('pre');
    if (logContent) {
        const lines = logContent.textContent.split('\n');
        let highlightedContent = '';
        
        lines.forEach(line => {
            if (line.includes('ERROR')) {
                highlightedContent += '<span class="text-red-400">' + line + '</span>\n';
            } else if (line.includes('WARNING')) {
                highlightedContent += '<span class="text-yellow-400">' + line + '</span>\n';
            } else if (line.includes('INFO')) {
                highlightedContent += '<span class="text-blue-400">' + line + '</span>\n';
            } else {
                highlightedContent += line + '\n';
            }
        });
        
        logContent.innerHTML = highlightedContent;
    }
});
</script>
@endpush
@endsection
