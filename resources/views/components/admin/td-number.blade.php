@props(['paginator' => null, 'index' => null])

@php
    $number = $index;
    if ($number === null && $paginator && isset($loop)) {
        $number = ($paginator->firstItem() ?? 0) + $loop->index;
    } elseif ($number === null && isset($loop)) {
        $number = $loop->iteration;
    }
@endphp

<td {{ $attributes->merge(['class' => 'px-4 py-4 whitespace-nowrap text-sm text-gray-500 font-medium tabular-nums w-14']) }}>
    {{ $number }}
</td>
