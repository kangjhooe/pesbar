<div class="border border-news-line bg-news-ink text-white p-5">
    <h2 class="text-xs font-bold uppercase tracking-[0.15em] mb-2">Newsletter</h2>
    <p class="text-sm text-white/70 mb-4">Dapatkan berita terbaru langsung di email Anda.</p>
    <form action="{{ route('newsletter.subscribe') }}" method="POST" class="space-y-3">
        @csrf
        <input type="email"
               name="email"
               placeholder="Email Anda"
               required
               class="w-full px-3 py-2 bg-white text-news-ink text-sm border-0 focus:outline-none focus:ring-2 focus:ring-news-accent">
        <button type="submit"
                class="w-full bg-news-accent text-white font-semibold py-2 text-sm hover:bg-red-800 transition-colors">
            Berlangganan
        </button>
    </form>
</div>
