@extends('layouts.admin-simple')

@section('title', 'Backup Database - Admin Panel')
@section('page-title', 'Backup Database')
@section('page-subtitle', 'Kelola backup database dan file')

@section('content')
<div class="space-y-6">
    <!-- Backup Actions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Buat Backup</h3>
        <div class="flex flex-col sm:flex-row gap-4">
            <form action="{{ route('admin.backup.create') }}" method="POST" class="inline">
                @csrf
                <button type="submit" 
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center"
                        onclick="return confirm('Apakah Anda yakin ingin membuat backup database?')">
                    <i class="fas fa-database mr-2"></i>
                    Buat Backup Database
                </button>
            </form>
            
            <button onclick="downloadFullBackup()" 
                    class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center">
                <i class="fas fa-download mr-2"></i>
                Download Full Backup
            </button>
        </div>
        
        <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                <p class="text-sm text-yellow-800">
                    <strong>Peringatan:</strong> Backup database akan memakan waktu beberapa menit tergantung ukuran database. 
                    Pastikan tidak ada aktivitas yang sedang berlangsung saat membuat backup.
                </p>
            </div>
        </div>
    </div>

    <!-- Backup List -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Daftar Backup</h3>
            <p class="text-sm text-gray-600">{{ $backups->count() }} file backup</p>
        </div>
        
        @if($backups->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama File</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ukuran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Dibuat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($backups as $backup)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <i class="fas fa-database text-blue-600 mr-3"></i>
                                <div class="text-sm font-medium text-gray-900">{{ $backup['name'] }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ number_format($backup['size'] / 1024 / 1024, 2) }} MB
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ date('d-m-Y H:i:s', $backup['created']) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('admin.backup.download', $backup['name']) }}" 
                                   class="text-blue-600 hover:text-blue-900" title="Download">
                                    <i class="fas fa-download"></i>
                                </a>
                                
                                <button onclick="deleteBackup('{{ $backup['name'] }}')" 
                                        class="text-red-600 hover:text-red-900" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-12 text-center">
            <div class="text-gray-500">
                <i class="fas fa-database text-4xl mb-4"></i>
                <p class="text-lg font-medium">Belum ada backup</p>
                <p class="text-sm">Buat backup pertama Anda</p>
            </div>
        </div>
        @endif
    </div>

    <!-- Backup Settings -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Pengaturan Backup</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <h4 class="text-sm font-medium text-gray-900 mb-2">Auto Backup</h4>
                <p class="text-sm text-gray-600 mb-4">Konfigurasi backup otomatis</p>
                <div class="space-y-3">
                    <label class="flex items-center">
                        <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Aktifkan backup harian</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Aktifkan backup mingguan</span>
                    </label>
                    <label class="flex items-center">
                        <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Aktifkan backup bulanan</span>
                    </label>
                </div>
            </div>
            
            <div>
                <h4 class="text-sm font-medium text-gray-900 mb-2">Retention Policy</h4>
                <p class="text-sm text-gray-600 mb-4">Atur berapa lama backup disimpan</p>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Backup Harian</label>
                        <select class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="7">7 hari</option>
                            <option value="14">14 hari</option>
                            <option value="30" selected>30 hari</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Backup Mingguan</label>
                        <select class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="4">4 minggu</option>
                            <option value="8">8 minggu</option>
                            <option value="12" selected>12 minggu</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Backup Bulanan</label>
                        <select class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="6">6 bulan</option>
                            <option value="12" selected>12 bulan</option>
                            <option value="24">24 bulan</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-6 flex justify-end">
            <button class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                Simpan Pengaturan
            </button>
        </div>
    </div>

    <!-- Backup Information -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Backup</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-blue-50 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-info-circle text-blue-600 text-xl mr-3"></i>
                    <div>
                        <h4 class="text-sm font-medium text-blue-900">Lokasi Backup</h4>
                        <p class="text-sm text-blue-700">storage/app/backups/</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-green-50 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
                    <div>
                        <h4 class="text-sm font-medium text-green-900">Format Backup</h4>
                        <p class="text-sm text-green-700">SQL Dump</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-yellow-50 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-xl mr-3"></i>
                    <div>
                        <h4 class="text-sm font-medium text-yellow-900">Ukuran Maksimal</h4>
                        <p class="text-sm text-yellow-700">Tidak terbatas</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function downloadFullBackup() {
    if (confirm('Apakah Anda yakin ingin mendownload full backup? Ini akan memakan waktu beberapa menit.')) {
        // Here you would implement the full backup download logic
        alert('Fitur full backup akan segera tersedia.');
    }
}

function deleteBackup(filename) {
    if (confirm('Apakah Anda yakin ingin menghapus backup ini?')) {
        // Here you would implement the backup deletion logic
        alert('Fitur hapus backup akan segera tersedia.');
    }
}

// Auto refresh backup list every 30 seconds
setInterval(function() {
    // You can implement auto-refresh here if needed
}, 30000);
</script>
@endpush
@endsection
