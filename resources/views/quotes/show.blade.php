@extends('Layout.app')

@php
    $ogTitle = $quote->author
        ? __('quotes.og_title_author', ['author' => $quote->author])
        : __('quotes.og_title');
    $ogText = \Illuminate\Support\Str::limit($quote->content, 155);
@endphp

@section('title', $ogTitle.' | كُنّاشة')
@section('description', $ogText)

@section('og')
    <meta property="og:type" content="article">
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:description" content="«{{ $ogText }}»">
    <meta property="og:url" content="{{ route('quotes.show', $quote) }}">
    <meta property="og:image" content="{{ asset('images/og.jpg') }}">
    <meta name="twitter:card" content="summary_large_image">
@endsection

@section('content')
<div class="px-4 py-14 sm:px-6">

    <div class="mx-auto max-w-3xl">

        <x-flash />

        <x-quote-card :quote="$quote" variant="hero" />

        <div class="mt-12 text-center">
            <x-ornament class="mx-auto w-44 text-[#a8834a]" />

            @guest
                <p class="brand-heading mt-6 text-3xl font-bold text-[#7c0221]">
                    {{ __('quotes.liked_it') }}
                </p>
                <p class="mt-2 font-quote text-lg leading-9 text-[#5b4a43]">
                    {{ __('quotes.start_yours') }}
                </p>
            @endguest

            <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                @guest
                    <a href="{{ route('signup') }}" class="btn-primary px-8 text-sm">
                        {{ __('quotes.start_cta') }}
                    </a>
                @endguest

                @if ($quote->user?->username)
                    <a href="{{ route('users.show', $quote->user->username) }}" class="btn-ghost px-8">
                        {{ __('quotes.more_from', ['name' => $quote->user->name]) }}
                    </a>
                @endif

                <a href="{{ route('explore') }}" class="btn-ghost px-8">
                    {{ __('quotes.discover_more') }}
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
