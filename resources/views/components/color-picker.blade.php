@props(['name' => 'color', 'selected' => '#FFFFFF', 'size' => 'h-9 w-9'])

@php
    $selected = \App\Support\QuoteStyle::normalize($selected);
@endphp

<div class="flex flex-wrap gap-3" role="radiogroup" aria-label="{{ __('quotes.color') }}">
    @foreach (\App\Models\Quote::COLORS as $color)
        @php $cs = \App\Support\QuoteStyle::for($color); @endphp

        <label class="cursor-pointer">
            <input
                type="radio"
                name="{{ $name }}"
                value="{{ $color }}"
                class="peer sr-only"
                aria-label="{{ __('quotes.colors.'.ltrim($color, '#')) }}"
                data-color-radio
                data-bg="{{ $cs['bg'] }}"
                data-fg="{{ $cs['fg'] }}"
                data-accent="{{ $cs['accent'] }}"
                @checked(\App\Support\QuoteStyle::normalize($color) === $selected)>

            <span
                class="block {{ $size }} rounded-full border border-[#D4D4D4] ring-2 ring-transparent ring-offset-2 transition peer-checked:ring-[#7c0221] peer-focus-visible:ring-[#7c0221]"
                style="background-color: {{ $color }}"></span>
        </label>
    @endforeach
</div>
