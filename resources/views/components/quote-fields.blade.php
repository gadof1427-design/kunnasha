{{--
    Text, author and colour fields with a live preview card.
    Shared by the composer modal and the edit panel; app.js keeps the preview in sync
    inside any element marked [data-preview-root].
--}}
@props(['prefix', 'content' => '', 'author' => '', 'color' => '#FFFFFF'])

@php
    $style = \App\Support\QuoteStyle::for($color);
@endphp

<div class="space-y-5">
    <div>
        <div class="mb-2 flex items-center justify-between gap-3">
            <label for="{{ $prefix }}-content" class="text-sm font-semibold text-[#7c0221]">{{ __('quotes.content') }}</label>
            <span id="{{ $prefix }}-count" class="text-xs text-[#707070]" aria-live="polite"></span>
        </div>

        <textarea
            id="{{ $prefix }}-content"
            name="content"
            rows="4"
            required
            minlength="3"
            maxlength="{{ \App\Models\Quote::MAX_LENGTH }}"
            data-counter="{{ $prefix }}-count"
            data-preview-input
            dir="auto"
            aria-describedby="{{ $prefix }}-content-hint"
            placeholder="{{ __('quotes.content_placeholder') }}"
            class="kn-field font-quote text-lg leading-9">{{ $content }}</textarea>

        <p id="{{ $prefix }}-content-hint" class="mt-1.5 text-xs text-[#8a8a8a]">
            {{ __('quotes.content_hint', ['max' => \App\Models\Quote::MAX_LENGTH]) }}
        </p>
    </div>

    <div>
        <label for="{{ $prefix }}-author" class="mb-2 block text-sm font-semibold text-[#7c0221]">
            {{ __('quotes.author') }} <span class="font-normal text-[#707070]">{{ __('quotes.optional') }}</span>
        </label>

        <input
            id="{{ $prefix }}-author"
            type="text"
            name="author"
            maxlength="255"
            value="{{ $author }}"
            data-preview-author-input
            dir="auto"
            aria-describedby="{{ $prefix }}-author-hint"
            placeholder="{{ __('quotes.author_placeholder') }}"
            class="kn-field text-sm">

        <p id="{{ $prefix }}-author-hint" class="mt-1.5 text-xs text-[#8a8a8a]">
            {{ __('quotes.author_hint') }}
        </p>
    </div>

    <div>
        <p class="text-sm font-semibold text-[#7c0221]">{{ __('quotes.color') }}</p>
        <p class="mb-3 mt-1 text-xs text-[#8a8a8a]">{{ __('quotes.color_hint') }}</p>
        <x-color-picker name="color" :selected="$color" size="h-8 w-8" />
    </div>

    <div>
        <p class="mb-2 text-sm font-semibold text-[#7c0221]">{{ __('quotes.preview') }}</p>

        <div
            data-preview
            class="kn-frame rounded-2xl p-6 shadow-sm transition-colors duration-300"
            style="background-color: {{ $style['bg'] }}; color: {{ $style['fg'] }}; --kn-accent: {{ $style['accent'] }};">
            <span class="kn-mark" aria-hidden="true">«</span>
            <p data-preview-text dir="auto" class="mt-2 min-h-8 whitespace-pre-line break-words font-quote text-xl leading-[2]"></p>
            <span class="kn-mark mt-1 text-end" aria-hidden="true">»</span>
            <p data-preview-author dir="auto" class="brand-heading mt-3 text-lg" style="color: {{ $style['accent'] }};"></p>
        </div>
    </div>
</div>
