{{-- Decorative divider: hairlines fading out at both ends around an eight-point star. Colour and width come from the caller. --}}
<div {{ $attributes->merge(['class' => 'kn-ornament flex items-center gap-2']) }} aria-hidden="true">
    <span class="h-px flex-1 bg-current"></span>
    <span class="h-1 w-1 rotate-45 bg-current"></span>
    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3">
        <rect x="5.5" y="5.5" width="13" height="13"/>
        <rect x="5.5" y="5.5" width="13" height="13" transform="rotate(45 12 12)"/>
        <circle cx="12" cy="12" r="2" fill="currentColor" stroke="none"/>
    </svg>
    <span class="h-1 w-1 rotate-45 bg-current"></span>
    <span class="h-px flex-1 bg-current"></span>
</div>
