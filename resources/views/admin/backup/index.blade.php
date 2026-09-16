@extends('layouts.admin-simple')

@section('title', 'Backup - Admin Panel')
@section('page-title', 'Backup')
@section('page-subtitle', 'Kelola backup database dan file')

@section('content')
<div class="space-y-6">
    <div class="bg-white border border-news-line p-6">
        <h3 class="text-lg font-semibold text-news-ink mb-4">Buat Backup</h3>
        <div class="flex flex-col sm:flex-row flex-wrap gap-3">
            <form action="{{ route('admin.backup.create') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="database">
                <button type="button"
                        class="bg-news-accent text-white px-6 py-2 hover:bg-news-ink transition-colors inline-flex items-center"
                        onclick="window.pesbarConfirmSubmit(this.form, 'Buat backup database sekarang?')">
                    <i class="fas fa-database mr-2"></i>
                    Backup Database
                </button>
            </form>

            <form action="{{ route('admin.backup.create') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="files">
                <button type="button"
                        class="btn-secondary px-6 py-2 inline-flex items-center"
                        onclick="window.pesbarConfirmSubmit(this.form, 'Buat backup file media sekarang?')">
                    <i class="fas fa-folder mr-2"></i>
                    Backup Files
                </button>
            </form>

            <form action="{{ route('admin.backup.create') }}" method="POST">
                @csrf
                <input type="hidden" name="type" value="full">
                <button type="button"
                        class="bg-news-ink text-white px-6 py-2 hover:bg-news-accent transition-colors inline-flex items-center"
                        onclick="window.pesbarConfirmSubmit(this.form, 'Buat full backup (database + files)? Ini bisa memakan waktu.')">
                    <i class="fas fa-archive mr-2"></i>
                    Full Backup
                </button>
            </form>
        </div>

        <div class="mt-4 border border-news-line border-l-4 border-l-amber-500 bg-amber-50 px-4 py-3">
            <p class="text-sm text-news-ink">
                <strong>Catatan:</strong> Backup disimpan di <code class="text-xs">storage/app/backups/</code>.
                Total saat ini: {{ $storageInfo['backup_count'] ?? count($backups) }} file
                ({{ $storageInfo['total_size_human'] ?? '0 B' }}).
            </p>
        </div>
    </div>

    <div class="bg-white border border-news-line">
        <div class="px-6 py-4 border-b border-news-line">
            <h3 class="text-lg font-semibold text-news-ink">Daftar Backup</h3>
            <p class="text-sm text-news-muted">{{ count($backups) }} file backup</p>
        </div>

        @if(count($backups) > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-news-line">
                <thead class="bg-news-paper">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">Nama File</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">Ukuran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-news-line">
                    @foreach($backups as $backup)
                    <tr class="hover:bg-news-paper">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <i class="fas fa-file-archive text-news-accent mr-3"></i>
                                <div class="text-sm font-medium text-news-ink">{{ $backup['filename'] }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-news-ink capitalize">{{ $backup['type'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-news-ink">{{ $backup['size_human'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-news-muted">
                            {{ $backup['created_at']->format('d-m-Y H:i:s') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('admin.backup.download', $backup['filename']) }}"
                                   class="text-news-accent hover:text-news-ink" title="Download">
                                    <i class="fas fa-download"></i>
                                </a>
                                <form action="{{ route('admin.backup.delete', $backup['filename']) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button"
                                            class="text-news-accent hover:text-news-ink"
                                            title="Hapus"
                                            onclick="window.pesbarConfirmSubmit(this.form, 'Hapus backup ini?', {danger:true})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-12 text-center text-news-muted">
            <i class="fas fa-database text-4xl mb-4"></i>
            <p class="text-lg font-medium">Belum ada backup</p>
            <p class="text-sm">Buat backup pertama Anda</p>
        </div>
        @endif
    </div>
</div>
@endsection
