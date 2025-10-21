# Tab Navigation Component

Komponen tab navigation yang responsif dan kompatibel dengan semua device.

## Penggunaan

```blade
<x-tab-navigation 
    :tabs="[
        [
            'id' => 'tab1',
            'label' => 'Tab Pertama',
            'icon' => 'fas fa-home',
            'content' => 'Konten tab pertama'
        ],
        [
            'id' => 'tab2',
            'label' => 'Tab Kedua',
            'icon' => 'fas fa-user',
            'content' => view('partials.tab-content')
        ]
    ]"
    active-tab="tab1"
/>
```

## Parameter

- `tabs` (array): Array berisi konfigurasi tab
  - `id` (string): ID unik untuk tab
  - `label` (string): Label yang ditampilkan pada tab
  - `icon` (string, optional): Class icon Font Awesome
  - `content` (string|view): Konten tab (bisa string atau view)
- `active-tab` (string, optional): ID tab yang aktif secara default

## Fitur

- ✅ Responsif untuk mobile, tablet, dan desktop
- ✅ Touch-friendly untuk perangkat mobile
- ✅ Smooth scrolling untuk tab yang banyak
- ✅ Animasi transisi yang halus
- ✅ Support untuk icon Font Awesome
- ✅ Deep linking dengan URL hash
- ✅ Keyboard navigation support
- ✅ ARIA attributes untuk accessibility

## CSS Classes

- `.tab-navigation`: Container utama tab
- `.tab-item`: Item tab individual
- `.tab-item.active`: Tab yang sedang aktif
- `.tab-content`: Container konten tab
- `.tab-content.active`: Konten tab yang sedang ditampilkan

## JavaScript Events

- Tab switching otomatis dengan JavaScript
- URL hash handling untuk deep linking
- Mobile menu integration
- Touch gesture support
