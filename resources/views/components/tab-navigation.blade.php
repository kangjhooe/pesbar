@props([
    'tabs' => [],
    'activeTab' => null,
    'id' => 'tab-navigation-' . uniqid()
])

<div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-2 mb-8" id="{{ $id }}">
    <div class="flex flex-wrap gap-2">
        @foreach($tabs as $index => $tab)
            @php
                $tabId = $tab['id'] ?? 'tab-' . $index;
                $isActive = $activeTab === $tabId || ($index === 0 && !$activeTab);
            @endphp
            <a href="#{{ $tabId }}" 
               class="group flex items-center px-6 py-3 rounded-xl font-semibold text-sm transition-all duration-300 {{ $isActive ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-lg transform scale-105' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}"
               data-tab="#{{ $tabId }}-content"
               role="tab"
               aria-selected="{{ $isActive ? 'true' : 'false' }}"
               aria-controls="{{ $tabId }}-content">
                @if(isset($tab['icon']))
                    <i class="{{ $tab['icon'] }} mr-2 {{ $isActive ? 'text-white' : 'text-gray-500 group-hover:text-gray-700' }}"></i>
                @endif
                {{ $tab['label'] }}
            </a>
        @endforeach
    </div>
</div>

@foreach($tabs as $index => $tab)
    @php
        $tabId = $tab['id'] ?? 'tab-' . $index;
        $isActive = $activeTab === $tabId || ($index === 0 && !$activeTab);
    @endphp
    <div id="{{ $tabId }}-content" 
         class="tab-content transition-all duration-500 ease-in-out {{ $isActive ? 'active opacity-100 transform translate-y-0' : 'opacity-0 transform translate-y-4 pointer-events-none' }}"
         role="tabpanel"
         aria-labelledby="{{ $tabId }}"
         style="{{ $isActive ? '' : 'display: none;' }}">
        <div class="animate-fade-in-up">
            {{ $tab['content'] ?? '' }}
        </div>
    </div>
@endforeach

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabNavigation = document.getElementById('{{ $id }}');
    if (!tabNavigation) return;

    const tabItems = tabNavigation.querySelectorAll('[data-tab]');
    const tabContents = document.querySelectorAll('.tab-content');

    tabItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            const targetTab = this.getAttribute('data-tab');
            const targetContent = document.querySelector(targetTab);
            
            if (!targetContent) return;

            // Remove active class from all tabs and contents
            tabItems.forEach(tab => {
                tab.classList.remove('bg-gradient-to-r', 'from-blue-500', 'to-blue-600', 'text-white', 'shadow-lg', 'scale-105');
                tab.classList.add('text-gray-600');
                tab.setAttribute('aria-selected', 'false');
                
                // Update icon colors
                const icon = tab.querySelector('i');
                if (icon) {
                    icon.classList.remove('text-white');
                    icon.classList.add('text-gray-500');
                }
            });

            tabContents.forEach(content => {
                content.classList.remove('active', 'opacity-100', 'transform', 'translate-y-0');
                content.classList.add('opacity-0', 'transform', 'translate-y-4', 'pointer-events-none');
                content.style.display = 'none';
            });

            // Add active class to clicked tab
            this.classList.remove('text-gray-600');
            this.classList.add('bg-gradient-to-r', 'from-blue-500', 'to-blue-600', 'text-white', 'shadow-lg', 'scale-105');
            this.setAttribute('aria-selected', 'true');
            
            // Update icon color
            const icon = this.querySelector('i');
            if (icon) {
                icon.classList.remove('text-gray-500');
                icon.classList.add('text-white');
            }

            // Show target content with animation
            setTimeout(() => {
                targetContent.style.display = 'block';
                setTimeout(() => {
                    targetContent.classList.remove('opacity-0', 'transform', 'translate-y-4', 'pointer-events-none');
                    targetContent.classList.add('active', 'opacity-100', 'transform', 'translate-y-0');
                }, 10);
            }, 50);
        });
    });
});
</script>
@endpush
