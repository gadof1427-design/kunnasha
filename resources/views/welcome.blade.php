@extends('Layout.app')

@section('title', 'كُنّاشة | KUNNASHA')

@section('content')

<div class="text-[#292929]">

    {{-- Hero --}}
    <section class="relative isolate mx-auto max-w-6xl overflow-hidden px-6 pb-16 pt-14 text-center md:pb-20 md:pt-20">

        {{-- Faint geometric rosette behind the wordmark --}}
        <svg class="pointer-events-none absolute left-1/2 top-2 -z-10 h-[30rem] w-[30rem] -translate-x-1/2 text-[#7c0221] opacity-[0.06] md:h-[38rem] md:w-[38rem]"
             viewBox="0 0 200 200" fill="none" stroke="currentColor" stroke-width="0.6" aria-hidden="true">
            <g transform="translate(100 100)">
                <circle r="96"/>
                <circle r="91"/>
                <rect x="-66" y="-66" width="132" height="132"/>
                <rect x="-66" y="-66" width="132" height="132" transform="rotate(45)"/>
                <rect x="-46" y="-46" width="92" height="92"/>
                <rect x="-46" y="-46" width="92" height="92" transform="rotate(45)"/>
                <rect x="-26" y="-26" width="52" height="52"/>
                <rect x="-26" y="-26" width="52" height="52" transform="rotate(45)"/>
            </g>
        </svg>

        <p dir="ltr" class="font-quote text-xs tracking-[0.5em] text-[#a8834a]">KUNNASHA</p>

        <h1 class="brand-heading kn-emboss mt-2 text-7xl font-bold leading-[1.35] text-[#7c0221] md:text-8xl">
            كُنّاشة
        </h1>

        <x-ornament class="mx-auto mt-3 w-60 text-[#a8834a]" />

        <p class="mx-auto mt-6 max-w-xl font-quote text-xl leading-10 text-[#5b4a43] md:text-2xl md:leading-[2.6rem]">
            {!! nl2br(e(__('layout.tagline'))) !!}
        </p>

        <x-accent key="layout.tagline" class="mt-3 text-sm leading-6 text-[#b6233e]/80" />

        <div class="mt-10 flex flex-wrap items-center justify-center gap-3">
            @auth
                <button type="button" data-open-composer class="btn-primary px-9 text-sm">
                    {{ __('home.cta_member') }}
                </button>
            @else
                <a href="{{ route('signup') }}" class="btn-primary px-9 text-sm">
                    {{ __('home.cta_guest') }}
                </a>
            @endauth

            <a href="#quotes" class="btn-ghost px-9">
                {{ __('home.discover') }}
            </a>
        </div>

    </section>

    {{-- Quote of the day --}}
    @if ($featured)
        <section class="mx-auto max-w-3xl px-6 pb-20" aria-labelledby="quote-of-the-day">
            <div class="mb-7 text-center">
                <x-accent key="home.quote_of_the_day" class="text-sm text-[#a8834a]" />
                <h2 id="quote-of-the-day" class="brand-heading mt-1 text-3xl font-bold text-[#7c0221] md:text-4xl">{{ __('home.quote_of_the_day') }}</h2>
                <x-ornament class="mx-auto mt-3 w-40 text-[#a8834a]" />
            </div>

            <x-quote-card :quote="$featured" variant="hero" />
        </section>
    @endif

    {{-- Quotes --}}
    <section id="quotes" class="mx-auto max-w-6xl scroll-mt-28 px-6 pb-28">

        <div class="mb-10 text-center">
            <x-accent key="home.collection" class="text-sm text-[#a8834a]" />
            <h2 class="brand-heading mt-1 text-4xl font-bold text-[#7c0221] md:text-5xl">{{ __('home.collection') }}</h2>
            <x-ornament class="mx-auto mt-3 w-48 text-[#a8834a]" />
            <p class="mt-4 text-sm text-[#707070]">{{ __('home.collection_hint') }}</p>
        </div>

        <x-flash />

        @if ($quotes->isEmpty())

            <div class="kn-panel px-6 py-16 text-center">
                <p class="brand-heading text-3xl text-[#7c0221]">{{ __('home.empty_title') }}</p>
                <x-ornament class="mx-auto mt-4 w-32 text-[#a8834a]" />
                <p class="mt-4 text-sm text-[#707070]">{{ __('home.empty_hint') }}</p>
            </div>

        @else

            <div data-quotes-grid class="grid gap-7 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($quotes as $quote)
                    <x-quote-card :quote="$quote" :index="$loop->index" />
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

        @endif

    </section>

</div>

@endsection
