@extends('layouts.admin-simple')

@section('title', 'Newsletter - Admin Panel')
@section('page-title', 'Manajemen Newsletter')
@section('page-subtitle', 'Kelola subscriber newsletter dan kirim email')

@section('content')
<div class="space-y-6">
    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="text-lg font-semibold text-news-ink">Daftar Subscriber</h3>
            <p class="text-sm text-news-muted">Total {{ $subscribers->total() }} subscriber</p>
        </div>
        <div class="flex gap-2">
            <button class="bg-news-accent text-white px-4 py-2 rounded-lg hover:bg-news-ink transition-colors flex items-center">
                <i class="fas fa-paper-plane mr-2"></i>
                Kirim Newsletter
            </button>
            <button class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center">
                <i class="fas fa-download mr-2"></i>
                Export CSV
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-news-line p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-news-paper rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-news-accent"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-news-muted">Total Subscriber</p>
                    <p class="text-2xl font-semibold text-news-ink">{{ $subscribers->total() }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-news-line p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-day text-green-600"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-news-muted">Hari Ini</p>
                    <p class="text-2xl font-semibold text-news-ink">
                        {{ \App\Models\NewsletterSubscriber::whereDate('created_at', today())->count() }}
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-news-line p-6">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-news-paper rounded-lg flex items-center justify-center">
                        <i class="fas fa-calendar-week text-news-ink"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-news-muted">Minggu Ini</p>
                    <p class="text-2xl font-semibold text-news-ink">
                        {{ \App\Models\NewsletterSubscriber::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count() }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Subscribers Table -->
    <div class="bg-white rounded-xl shadow-sm border border-news-line overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-news-line">
                <thead class="bg-news-paper">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">
                            Email
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">
                            IP Address
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">
                            User Agent
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">
                            Tanggal Subscribe
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-news-muted uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-news-line">
                    @forelse($subscribers as $subscriber)
                    <tr class="hover:bg-news-paper">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8">
                                    <div class="h-8 w-8 rounded-full bg-news-ink flex items-center justify-center">
                                        <span class="text-white text-xs font-medium">
                                            {{ strtoupper(substr($subscriber->email, 0, 1)) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-news-ink">
                                        {{ $subscriber->email }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($subscriber->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle mr-1"></i>
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-news-muted">
                            {{ $subscriber->ip_address ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-news-muted max-w-xs truncate">
                                {{ $subscriber->user_agent ?? '-' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-news-muted">
                            {{ $subscriber->created_at->format('d-m-Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <button class="text-news-accent hover:text-news-ink" title="Kirim Email">
                                    <i class="fas fa-envelope"></i>
                                </button>
                                <button class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="text-red-600 hover:text-red-900" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="text-news-muted">
                                <i class="fas fa-envelope text-4xl mb-4"></i>
                                <p class="text-lg font-medium">Belum ada subscriber</p>
                                <p class="text-sm">Subscriber newsletter akan muncul di sini</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($subscribers->hasPages())
    <div class="flex justify-center">
        {{ $subscribers->links() }}
    </div>
    @endif
</div>
@endsection
