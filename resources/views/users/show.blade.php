@extends('Layout.app')

@section('title', $user->name.' | كُنّاشة')
@section('description', __('users.description', ['name' => $user->name]))

@section('content')
<div class="px-5 py-10 text-[#292929] sm:px-8">
    <div class="mx-auto max-w-5xl">

        {{-- Member header --}}
        <section class="kn-panel overflow-hidden">
            <div class="relative h-32 overflow-hidden bg-linear-to-l from-[#4a0013] via-[#7c0221] to-[#b6233e] sm:h-40">
                <div class="kn-pattern absolute inset-0 opacity-[0.08]" aria-hidden="true"></div>
            </div>

            <div class="px-6 pb-9 text-center sm:px-10">
                <div class="relative z-10 -mt-14 flex justify-center">
                    <x-avatar :user="$user" size="h-28 w-28 text-4xl" class="shadow-lg ring-4 ring-[#fbf7ee]" />
                </div>

                <h1 class="brand-heading mt-4 text-4xl font-bold text-[#7c0221]">{{ $user->name }}</h1>
                <p class="mt-1 text-sm font-semibold text-[#8c1126]" dir="ltr">{{ '@'.$user->username }}</p>

                <x-ornament class="mx-auto mt-4 w-40 text-[#a8834a]" />

                <p class="mt-4 flex flex-wrap items-center justify-center gap-x-3 gap-y-1 text-sm text-[#5b4a43]">
                    <span>{{ trans_choice('messages.quotes_count', $quotesCount) }}</span>
                    <span aria-hidden="true">·</span>
                    <span>{{ __('users.member_since', ['date' => $user->created_at->locale(app()->getLocale())->translatedFormat('F Y')]) }}</span>
                </p>

                @if (auth()->id() === $user->id)
                    <a href="{{ route('profile') }}" class="btn-ghost mt-6 px-6 py-2.5">{{ __('users.edit_profile') }}</a>
                @endif
            </div>
        </section>

        {{-- Quotes --}}
        <div class="mb-8 mt-14 text-center">
            <x-accent key="users.notebook_of" :replace="['name' => $user->name]" class="text-sm text-[#a8834a]" />
            <h2 class="brand-heading mt-1 text-3xl font-bold text-[#7c0221] sm:text-4xl">{{ __('users.notebook_of', ['name' => $user->name]) }}</h2>
            <x-ornament class="mx-auto mt-3 w-40 text-[#a8834a]" />
        </div>

        <x-flash />

        @if ($quotes->isNotEmpty())

            <div data-quotes-grid class="space-y-6">
                @foreach ($quotes as $quote)
                    <x-quote-card :quote="$quote" variant="row" :index="$loop->index" />
                @endforeach
            </div>

            @if ($quotes->hasMorePages() || $quotes->currentPage() > 1)
                <nav data-pagination class="mt-12 flex flex-wrap items-center justify-center gap-3" aria-label="{{ __('layout.pagination') }}">
                    @if ($quotes->currentPage() > 1)
                        <a href="{{ $quotes->previousPageUrl() }}" data-prev-page rel="prev" class="btn-ghost">{{ __('layout.previous') }}</a>
                    @endif

                    @if ($quotes->hasMorePages())
                        <a href="{{ $quotes->nextPageUrl() }}" data-load-more rel="next" class="btn-primary px-9 text-sm">{{ __('layout.load_more') }}</a>
                    @endif
                </nav>
            @endif

        @else

            <div class="kn-panel px-6 py-14 text-center">
                <p class="brand-heading text-2xl font-bold text-[#7c0221]">{{ __('users.empty') }}</p>
                <x-ornament class="mx-auto mt-4 w-32 text-[#a8834a]" />
                <a href="{{ route('explore') }}" class="btn-ghost mt-6 px-6 py-2.5">{{ __('users.discover_others') }}</a>
            </div>

        @endif

    </div>
</div>
@endsection
