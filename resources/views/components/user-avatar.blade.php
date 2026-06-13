@props([
    'user',
    'size' => 'md',
    'class' => '',
])

@php
    $sizeMap = [
        'xs' => 'h-7 w-7 text-[10px]',
        'sm' => 'h-9 w-9 text-xs',
        'md' => 'h-11 w-11 text-sm',
        'lg' => 'h-14 w-14 text-base',
        'xl' => 'h-20 w-20 text-xl',
    ];

    $sizeClass = $sizeMap[$size] ?? $sizeMap['md'];
    $name = trim($user->name ?? 'U');
    $initials = collect(explode(' ', $name))
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_substr($part, 0, 1))
        ->implode('');
@endphp

<div {{ $attributes->merge(['class' => 'relative inline-flex flex-none items-center justify-center overflow-hidden rounded-full bg-sky-100 text-sky-700 font-semibold ring-1 ring-black/5 ' . $sizeClass . ' ' . $class]) }}>
    @if($user->profile_photo_url)
        <img
            src="{{ $user->profile_photo_url }}"
            alt="{{ $user->name }}"
            class="absolute inset-0 h-full w-full rounded-full bg-white object-contain object-center p-[2px]"
            loading="lazy"
        >
    @else
        <span class="select-none text-center leading-none tracking-tight">
            {{ strtoupper($initials ?: 'U') }}
        </span>
    @endif
</div>