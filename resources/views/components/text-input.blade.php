@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm transition duration-150 ease-in-out']) }} style="pointer-events: auto; position: relative; z-index: 1;">
