@extends('Layout.app')

@section('title', __('explore.title').' | كُنّاشة')

@section('content')
<div class="px-4 py-12 text-[#292929] sm:px-8">

    <div class="mx-auto max-w-5xl">

        {{-- Page heading --}}
        <header class="mb-10 text-center">
            <x-accent key="explore.eyebrow" class="text-sm text-[#a8834a]" />

            <h1 class="brand-heading mt-2 text-5xl font-bold text-[#7c0221] sm:text-6xl">
                {{ __('explore.heading') }}
            </h1>

            <x-ornament class="mx-auto mt-4 w-52 text-[#a8834a]" />

            <p class="mt-5 font-quote text-lg leading-9 text-[#5b4a43]">
                {{ __('explore.intro') }}
            </p>

            <x-accent key="explore.intro_accent" class="mt-1 text-sm text-[#707070]" />
        </header>


        {{-- Search and sorting --}}
        <form
            action="{{ route('explore') }}"
            method="GET"
            role="search"
            class="kn-panel p-3 sm:p-4"
        >
            <div class="flex flex-col gap-3 sm:flex-row">

                <div class="relative min-w-0 flex-1">
                    <label for="search" class="sr-only">{{ __('explore.search_label') }}</label>

                    <svg class="pointer-events-none absolute inset-y-0 start-4 my-auto h-5 w-5 text-[#a8834a]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="6.5"/><path d="m20 20-4.2-4.2"/></svg>

                    <input
                        id="search"
                        name="q"
                        type="search"
                        value="{{ $search }}"
                        dir="auto"
                        aria-describedby="search-hint"
                        placeholder="{{ __('explore.search_placeholder') }}"
                        class="kn-field py-3.5 ps-12"
                    >
                </div>

                <div class="flex gap-2 sm:gap-3">
                    <label for="sort" class="sr-only">{{ __('explore.sort_label') }}</label>

                    <select
                        id="sort"
                        name="sort"
                        class="kn-field min-w-0 flex-1 text-sm sm:w-auto sm:flex-none"
                    >
                        <option value="newest" @selected($sort === 'newest')>
                            {{ __('explore.newest') }}
                        </option>

                        <option value="oldest" @selected($sort === 'oldest')>
                            {{ __('explore.oldest') }}
                        </option>
                    </select>

                    <button type="submit" class="btn-primary rounded-2xl px-7 text-sm">
                        {{ __('explore.search') }}
                    </button>
                </div>

            </div>

            <p id="search-hint" class="mt-3 px-2 text-xs text-[#8a8a8a]">
                {{ __('explore.search_hint') }}
            </p>
        </form>


        {{-- Quotes heading --}}
        <div class="mb-7 mt-14 flex flex-wrap items-end justify-between gap-4">
            <div>
                <h2 class="brand-heading text-3xl font-bold text-[#7c0221] sm:text-4xl">
                    @if ($search !== '')
                        {{ __('explore.results_for', ['search' => $search]) }}
                    @else
                        {{ __('explore.heading_all') }}
                    @endif
                </h2>

                <p class="mt-1 text-sm text-[#5b4a43]">
                    {{ __('explore.shared_hint') }}
                </p>

                <x-accent key="explore.shared_accent" class="mt-1 text-sm text-[#707070]" />
            </div>

            <div class="flex items-center gap-2">
                @if ($search !== '')
                    <a href="{{ route('explore') }}" class="text-sm font-semibold text-[#7c0221] underline decoration-[#a8834a] underline-offset-4 hover:text-[#580016]">
                        {{ __('explore.clear_search') }}
                    </a>
                @endif

                <span class="shrink-0 rounded-full border border-[#a8834a]/40 bg-[#fbf7ee] px-4 py-2 text-sm font-semibold text-[#7c0221]">
                    {{ trans_choice('messages.quotes_count', $quotes->total()) }}
                </span>
            </div>
        </div>

        <x-flash />

        {{-- Quotes list --}}
        @if ($quotes->isNotEmpty())

            <div data-quotes-grid class="space-y-6">
                @foreach ($quotes as $quote)
                    <x-quote-card :quote="$quote" variant="row" :index="$loop->index" />
                @endforeach
            </div>

            @if ($quotes->hasMorePages() || $quotes->currentPage() > 1)
                <nav data-pagination class="mt-12 flex flex-wrap items-center justify-center gap-3" aria-label="{{ __('layout.pagination') }}">
                    @if ($quotes->currentPage() > 1)
                        <a href="{{ $quotes->previousPageUrl() }}" data-prev-page rel="prev" class="btn-ghost">
                            {{ __('layout.previous') }}
                        </a>
                    @endif

                    @if ($quotes->hasMorePages())
                        <a href="{{ $quotes->nextPageUrl() }}" data-load-more rel="next" class="btn-primary px-9 text-sm">
                            {{ __('layout.load_more') }}
                        </a>
                    @endif
                </nav>
            @endif

        @else

            {{-- Empty state --}}
            <div class="kn-panel px-6 py-14 text-center">

                <p class="brand-heading text-3xl font-bold text-[#7c0221]">
                    {{ __('explore.empty_title') }}
                </p>

                <x-accent key="explore.empty_title" class="mt-2 text-sm text-[#707070]" />

                <x-ornament class="mx-auto mt-4 w-32 text-[#a8834a]" />

                @if ($search !== '')

                    <p class="mt-5 text-[#292929]">
                        {{ __('explore.no_match') }}
                    </p>

                    <p class="mt-1 text-sm text-[#707070]">
                        {{ __('explore.try_another') }}
                    </p>

                    <a href="{{ route('explore') }}" class="btn-primary mt-7 px-7 text-sm">
                        {{ __('explore.show_all') }}
                    </a>

                @else

                    <p class="mt-5 text-[#292929]">
                        {{ __('explore.none_yet') }}
                    </p>

                    <a href="{{ route('home') }}" class="btn-primary mt-7 px-7 text-sm">
                        {{ __('explore.back_home') }}
                    </a>

                @endif

            </div>

        @endif

    </div>
</div>
@endsection
