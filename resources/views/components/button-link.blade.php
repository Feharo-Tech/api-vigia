@props([
    'href',
    'color' => 'blue',
])

<a href="{{ $href }}" {{ $attributes->merge(['class' => "hidden sm:inline-block bg-{$color}-500 hover:bg-{$color}-600 text-white px-4 py-2 rounded"]) }}>
    {{ $slot }}
</a>
