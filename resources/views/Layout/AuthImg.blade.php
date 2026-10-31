@extends('Layout.app')

@section('auth-screen', true)

@section('content')

<div class="relative flex min-h-screen w-full flex-col items-center justify-center overflow-hidden px-4 pb-10 pt-20 sm:px-6">

    {{-- Full-page background image --}}
    <img
        src="{{ asset('images/signup.jpeg') }}"
        alt=""
        class="absolute inset-0 h-full w-full object-cover object-center"
    >

    {{-- Dark overlay + pattern --}}
    <div class="absolute inset-0 bg-linear-to-b from-[#38000f]/75 via-[#38000f]/65 to-[#1f0008]/85" aria-hidden="true"></div>
    <div class="kn-pattern absolute inset-0 opacity-[0.05]" aria-hidden="true"></div>

    {{-- Top bar: back home + language --}}
    <div class="absolute inset-x-0 top-0 z-10 flex items-center justify-between px-5 py-5 sm:px-8">
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 rounded-full px-3 py-2 text-sm font-semibold text-[#fff8cf]/90 transition hover:bg-white/10 hover:text-white">
            <svg class="h-4 w-4 rtl:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 6l-6 6 6 6"/></svg>
            {{ __('layout.home') }}
        </a>

        <a href="{{ route('language.switch', app()->getLocale() === 'ar' ? 'en' : 'ar') }}"
           lang="{{ app()->getLocale() === 'ar' ? 'en' : 'ar' }}"
           class="rounded-full border border-[#f9e0b0]/50 px-4 py-1.5 text-xs font-bold text-[#f9e0b0] transition hover:bg-[#f9e0b0] hover:text-[#580016]"
           aria-label="{{ __('layout.switch.label') }}">
            {{ __('layout.switch.short') }}
        </a>
    </div>

    {{-- Form container --}}
    <div class="relative z-10 w-full max-w-lg">

        <div class="mb-7 text-center">
            <a href="{{ route('home') }}" class="brand-name text-5xl font-bold text-[#f9e0b0] sm:text-6xl">
                كُنّاشة
            </a>

            <x-ornament class="mx-auto mt-3 w-44 text-[#f9e0b0]/70" />

            <p class="mt-4 font-quote text-lg leading-9 text-[#fff8cf]">
                {!! nl2br(e(__('layout.tagline'))) !!}
            </p>

            <x-accent key="layout.tagline" class="mt-1 text-sm leading-6 text-[#f9e0b0]/75" />
        </div>

        <div class="kn-frame rounded-3xl border border-[#f9e0b0]/40 bg-[#fbf7ee]/95 p-6 shadow-2xl backdrop-blur-md sm:p-9" style="--kn-accent: #a8834a;">
            @yield('form')
        </div>

    </div>
</div>

@endsection
