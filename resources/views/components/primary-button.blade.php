<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-6 py-3 bg-news-ink border border-transparent font-bold text-sm text-white tracking-wide hover:bg-news-accent focus:bg-news-accent active:bg-primary-800 focus:outline-none focus:ring-2 focus:ring-news-accent focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed min-h-[44px] touch-target']) }}>
    {{ $slot }}
</button>
