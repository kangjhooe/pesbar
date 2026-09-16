@if(\App\Services\ImpersonationManager::isImpersonating())
    <div class="sticky top-0 z-[70] bg-news-ink text-white border-b border-news-ink" role="status">
        <div class="max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-2.5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <p class="text-sm leading-snug">
                <i class="fas fa-user-secret mr-1.5 opacity-80" aria-hidden="true"></i>
                <span class="font-semibold">Mode impersonasi:</span>
                Anda melihat sebagai
                <span class="font-semibold">{{ Auth::user()->name }}</span>
                @if(\App\Services\ImpersonationManager::impersonatorName())
                    <span class="opacity-80">(admin: {{ \App\Services\ImpersonationManager::impersonatorName() }})</span>
                @endif.
                Tindakan di sini berlaku sebagai penulis ini.
            </p>
            <form method="POST" action="{{ route('impersonation.leave') }}" class="shrink-0">
                @csrf
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-3 py-1.5 text-sm font-semibold bg-white text-news-ink hover:bg-news-paper transition-colors w-full sm:w-auto">
                    <i class="fas fa-undo text-xs" aria-hidden="true"></i>
                    Kembali ke Admin
                </button>
            </form>
        </div>
    </div>
@endif
