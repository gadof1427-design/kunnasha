{{--
    Single quote card, used on every page so they always look and behave the same.

    variant: grid (home)  |  row (explore, profile, user page)  |  hero (permalink, quote of the day)
--}}
@props(['quote', 'variant' => 'grid', 'index' => 0])

@php
    $s = \App\Support\QuoteStyle::for($quote->color);
    $isOwner = auth()->check() && auth()->id() === $quote->user_id;
    $url = route('quotes.show', $quote);
    $isGrid = $variant === 'grid';
    $isRow = $variant === 'row';
    $isHero = $variant === 'hero';
    $by = $quote->user;

    // Shorter quotes get larger type, so every card feels composed rather than empty.
    $length = mb_strlen($quote->content);
    $textSize = match (true) {
        $isHero => $length <= 140 ? 'text-3xl sm:text-4xl' : 'text-2xl sm:text-3xl',
        $length <= 80 => 'text-2xl',
        $length <= 220 => 'text-xl',
        default => 'text-lg',
    };

    // Re-open the edit panel with the user's text if validation failed.
    $editing = $isOwner && old('_form') === 'edit-'.$quote->id;
    $editContent = $editing ? old('content', $quote->content) : $quote->content;
    $editAuthor = $editing ? old('author', $quote->author) : $quote->author;
    $editColor = $editing ? old('color', $quote->color) : $quote->color;
@endphp

<article
    id="quote-{{ $quote->id }}"
    data-quote-card
    data-quote-id="{{ $quote->id }}"
    data-quote-text="{{ $quote->content }}"
    data-quote-author="{{ $quote->author }}"
    data-quote-by="{{ $by?->name }}"
    data-quote-url="{{ $url }}"
    data-quote-bg="{{ $s['bg'] }}"
    data-quote-fg="{{ $s['fg'] }}"
    data-quote-accent="{{ $s['accent'] }}"
    @class([
        'kn-rise kn-card kn-frame flex scroll-mt-28 flex-col overflow-hidden rounded-3xl border border-black/5',
        'min-h-72 p-7 sm:p-8' => $isGrid,
        'p-6 sm:p-8' => $isRow,
        'p-8 text-center sm:p-14' => $isHero,
    ])
    style="background-color: {{ $s['bg'] }}; color: {{ $s['fg'] }}; --kn-accent: {{ $s['accent'] }}; --i: {{ $index }};">

    <div @class([
        'flex flex-1 flex-col',
        'gap-6 sm:flex-row sm:items-stretch sm:gap-8' => $isRow,
    ])>

        {{-- Quote text --}}
        <blockquote class="flex min-w-0 flex-1 flex-col">
            <span @class(['kn-mark', 'text-center' => $isHero]) aria-hidden="true">«</span>

            {{-- dir="auto": an English quote on the Arabic site (or the reverse) keeps its punctuation in place --}}
            <p dir="auto" @class([
                'my-auto whitespace-pre-line break-words py-3 font-quote leading-[2]',
                $textSize,
            ])>{!! $quote->formattedContent() !!}</p>

            <span @class(['kn-mark', 'text-center' => $isHero, 'text-end' => ! $isHero]) aria-hidden="true">»</span>
        </blockquote>

        {{-- Author, contributor, date and actions --}}
        <div @class([
            'mt-5 border-t pt-5' => $isGrid || $isHero,
            'border-t pt-5 sm:flex sm:w-60 sm:shrink-0 sm:flex-col sm:justify-center sm:border-s sm:border-t-0 sm:ps-7 sm:pt-0' => $isRow,
        ]) style="border-color: {{ $s['line'] }};">

            <div class="min-w-0">
                @if ($quote->author)
                    <p dir="auto" @class(['brand-heading break-words leading-snug', 'text-xl' => ! $isHero, 'text-2xl' => $isHero]) style="color: {{ $s['accent'] }};">
                        — {{ $quote->author }}
                    </p>
                @endif

                <p @class([
                    'mt-2 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs leading-6',
                    'justify-center' => $isHero,
                ]) style="color: {{ $s['muted'] }};">
                    @if ($by)
                        @if ($by->username)
                            <a href="{{ route('users.show', $by->username) }}" class="inline-flex items-center gap-1.5 font-semibold hover:underline">
                                <x-avatar :user="$by" size="h-6 w-6 text-[10px]" class="ring-1 ring-current/25" />
                                <span>{{ __('quotes.added_by', ['name' => $by->name]) }}</span>
                            </a>
                        @else
                            <span class="inline-flex items-center gap-1.5">
                                <x-avatar :user="$by" size="h-6 w-6 text-[10px]" class="ring-1 ring-current/25" />
                                <span>{{ __('quotes.added_by', ['name' => $by->name]) }}</span>
                            </span>
                        @endif
                        <span aria-hidden="true">·</span>
                    @endif

                    {{-- Publication date (relative time on hover) --}}
                    <a href="{{ $url }}" class="hover:underline">
                        <time datetime="{{ $quote->created_at->toIso8601String() }}" title="{{ $quote->created_at->locale(app()->getLocale())->diffForHumans() }}">{{ $quote->created_at->locale(app()->getLocale())->translatedFormat('j F Y') }}</time>
                    </a>
                </p>
            </div>

            <div @class(['mt-4 flex flex-wrap items-center gap-1', 'justify-center' => $isHero]) role="group" aria-label="{{ __('quotes.actions') }}">

                <button type="button" data-action="copy" class="kn-icon-btn" title="{{ __('quotes.copy') }}" aria-label="{{ __('quotes.copy') }}">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="8" y="8" width="12" height="12" rx="2"/><path d="M16 8V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h2"/></svg>
                </button>

                <button type="button" data-action="share-image" class="kn-icon-btn" title="{{ __('quotes.share_image') }}" aria-label="{{ __('quotes.share_image_label') }}">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 15V4m0 0 4 4m-4-4L8 8"/><path d="M5 14v4a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-4"/></svg>
                </button>

                <button type="button" data-action="copy-link" class="kn-icon-btn" title="{{ __('quotes.copy_link') }}" aria-label="{{ __('quotes.copy_link_label') }}">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 14a4 4 0 0 0 5.66 0l3-3a4 4 0 0 0-5.66-5.66l-1 1"/><path d="M14 10a4 4 0 0 0-5.66 0l-3 3a4 4 0 0 0 5.66 5.66l1-1"/></svg>
                </button>

                @if ($isOwner)
                    <span class="mx-1 h-5 w-px" style="background-color: {{ $s['line'] }};" aria-hidden="true"></span>

                    <button type="button" data-action="toggle-edit" class="kn-icon-btn" title="{{ __('quotes.edit') }}" aria-label="{{ __('quotes.edit_label') }}" aria-expanded="{{ $editing ? 'true' : 'false' }}">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 20h4L18.5 9.5a2.12 2.12 0 0 0-3-3L5 17v3Z"/><path d="m14.5 7.5 3 3"/></svg>
                    </button>

                    <form action="{{ route('quotes.destroy', $quote) }}" method="POST"
                          data-confirm="{{ __('quotes.delete_confirm') }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="kn-icon-btn" title="{{ __('quotes.delete') }}" aria-label="{{ __('quotes.delete_label') }}">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16M10 11v6M14 11v6M6 7l1 12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2l1-12M9 7V4h6v3"/></svg>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Edit panel (owner only), with a live preview of the changes before saving --}}
    @if ($isOwner)
        <div data-edit-panel @class(['mt-6 rounded-2xl border border-[#D4D4D4] bg-white p-5 text-start text-[#292929] sm:p-6', 'hidden' => ! $editing])>
            <form action="{{ route('quotes.update', $quote) }}" method="POST" data-once data-preview-root class="space-y-6">
                @csrf
                @method('PUT')
                <input type="hidden" name="_form" value="edit-{{ $quote->id }}">

                @if ($editing && $errors->any())
                    <div role="alert" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        <ul class="list-inside list-disc space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <x-quote-fields :prefix="'edit-'.$quote->id" :content="$editContent" :author="$editAuthor" :color="$editColor" />

                <div class="flex flex-wrap gap-2">
                    <button type="submit" class="btn-primary px-6 py-2.5 text-sm">
                        {{ __('quotes.save_changes') }}
                    </button>
                    <button type="button" data-action="toggle-edit" class="btn-ghost px-6 py-2.5">
                        {{ __('quotes.cancel') }}
                    </button>
                </div>
            </form>
        </div>
    @endif
</article>
