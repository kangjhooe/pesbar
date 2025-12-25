@props(['user', 'size' => 'sm'])

@if($user)
@php
    // Size configurations
    $sizeConfigs = [
        'xs' => [
            'container' => 'w-4 h-4',
            'icon' => 'text-[8px]',
        ],
        'sm' => [
            'container' => 'w-5 h-5',
            'icon' => 'text-[10px]',
        ],
        'md' => [
            'container' => 'w-6 h-6',
            'icon' => 'text-xs',
        ],
        'lg' => [
            'container' => 'w-7 h-7',
            'icon' => 'text-sm',
        ],
    ];
    
    $sizeConfig = $sizeConfigs[$size] ?? $sizeConfigs['sm'];
    
    // Determine badge configuration based on role and verification status
    $config = null;
    
    if ($user->role === 'admin') {
        // Admin: icon istimewa (star/crown)
        $config = [
            'label' => 'Admin',
            'class' => 'bg-gradient-to-br from-red-600 to-rose-700 text-white',
            'icon' => 'fas fa-star',
        ];
    } elseif ($user->role === 'penulis' && $user->isVerified()) {
        // Penulis terverifikasi: centang berdasarkan tipe
        if ($user->verification_type === 'lembaga') {
            // Penulis lembaga: centang hijau
            $config = [
                'label' => 'Penulis Lembaga Terverifikasi',
                'class' => 'bg-green-500 text-white',
                'icon' => 'fas fa-check-circle',
            ];
        } else {
            // Penulis perorangan: centang biru
            $config = [
                'label' => 'Penulis Terverifikasi',
                'class' => 'bg-blue-500 text-white',
                'icon' => 'fas fa-check-circle',
            ];
        }
    } elseif ($user->role === 'penulis') {
        // Penulis belum terverifikasi: tetap centang merah
        $config = [
            'label' => 'Penulis',
            'class' => 'bg-red-500 text-white',
            'icon' => 'fas fa-check-circle',
        ];
    } elseif ($user->role === 'editor') {
        // Editor: tetap dengan icon pencil
        $config = [
            'label' => 'Editor',
            'class' => 'bg-yellow-500 text-white',
            'icon' => 'fas fa-pencil',
        ];
    } else {
        // User biasa: centang merah
        $config = [
            'label' => 'User',
            'class' => 'bg-red-500 text-white',
            'icon' => 'fas fa-check-circle',
        ];
    }
@endphp

<span class="relative inline-flex items-center justify-center {{ $sizeConfig['container'] }} rounded-full {{ $config['class'] }} transition-all shadow-sm" 
      title="{{ $config['label'] }}">
    <i class="{{ $config['icon'] }} {{ $sizeConfig['icon'] }}"></i>
</span>
@endif

