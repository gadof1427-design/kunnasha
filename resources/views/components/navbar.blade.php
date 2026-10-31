@props(['page' => ''])

@php
    $otherLocale = app()->getLocale() === 'ar' ? 'en' : 'ar';

    // One query per page (it used to run twice).
    $unreadCount = auth()->check() ? auth()->user()->unreadNotifications()->count() : 0;

    $link = 'rounded-full px-2 py-2 text-xs font-semibold transition sm:px-5 sm:text-sm';
    $iconBtn = 'relative flex h-9 w-9 items-center justify-center rounded-full border border-[#7c0221]/30 text-[#7c0221] transition hover:bg-[#7c0221] hover:text-[#fff8cf] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#7c0221] sm:h-10 sm:w-10';
@endphp

<nav class="sticky top-0 z-50 w-full border-b border-[#a8834a]/35 bg-[#ECE3D8] shadow-[0_8px_24px_-16px_rgb(88_0_22/0.45)]"
     style="background-image: url('{{ asset('images/nav-paper.webp') }}'); background-size: cover; background-position: center;">

    <div class="relative mx-auto flex h-16 w-full max-w-7xl items-center justify-between gap-1.5 px-2 sm:h-24 sm:gap-2 sm:px-6 lg:px-8">

        {{-- Logo: at the start on phones, centred on larger screens --}}
        <a href="{{ route('home') }}"
           class="relative z-10 shrink-0 sm:absolute sm:left-1/2 sm:top-1/2 sm:-translate-x-1/2 sm:-translate-y-1/2"
           aria-label="{{ __('layout.nav.home_label') }}">
            <img
                src="{{ asset('images/logo-mark.webp') }}"
                alt="كُنّاشة"
                width="365"
                height="240"
                class="h-9 w-auto sm:h-[4.5rem]">
        </a>

        {{-- Main links --}}
        <div class="relative z-10 flex items-center gap-1 sm:gap-2">
            <a href="{{ route('home') }}"
               @class([
                   $link,
                   'bg-[#7c0221] text-[#fff8cf]' => request()->routeIs('home'),
                   'text-[#7c0221] hover:bg-[#7c0221]/10' => ! request()->routeIs('home'),
               ])
               @if (request()->routeIs('home')) aria-current="page" @endif>
                {{ __('layout.nav.home') }}
            </a>

            <a href="{{ route('explore') }}"
               @class([
                   $link,
                   'bg-[#7c0221] text-[#fff8cf]' => request()->routeIs('explore'),
                   'text-[#7c0221] hover:bg-[#7c0221]/10' => ! request()->routeIs('explore'),
               ])
               @if (request()->routeIs('explore')) aria-current="page" @endif>
                {{ __('layout.nav.explore') }}
            </a>
        </div>

        {{-- Account actions --}}
        <div class="relative z-10 flex items-center gap-1 sm:gap-3">

            {{-- Language switch (compact on phones so it fits beside the account buttons) --}}
            <a href="{{ route('language.switch', $otherLocale) }}"
               lang="{{ $otherLocale }}"
               class="inline-flex h-8 min-w-8 items-center justify-center rounded-full border border-[#7c0221]/30 px-1.5 text-xs font-bold text-[#7c0221] transition hover:bg-[#7c0221] hover:text-[#fff8cf] sm:h-auto sm:px-3 sm:py-2"
               aria-label="{{ __('layout.switch.label') }}">
                {{ __('layout.switch.short') }}
            </a>

            @auth

                {{-- Notifications --}}
                <a href="{{ route('notifications.index') }}"
                   class="{{ $iconBtn }}"
                   aria-label="{{ __('layout.nav.notifications') }}{{ $unreadCount > 0 ? ' ('.$unreadCount.')' : '' }}">

                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 0 0-5-5.9V4a1 1 0 0 0-2 0v1.1A6 6 0 0 0 6 11v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0a3 3 0 0 1-6 0"/>
                    </svg>

                    @if ($unreadCount > 0)
                        <span class="absolute -right-1 -top-1 flex h-5 min-w-5 items-center justify-center rounded-full bg-[#b6233e] px-1 text-[10px] font-bold text-white ring-2 ring-[#ECE3D8]">
                            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                        </span>
                    @endif
                </a>

                {{-- Profile --}}
                <a href="{{ route('profile') }}"
                   class="flex items-center gap-2 rounded-full border border-[#7c0221]/30 p-1 text-sm font-semibold text-[#7c0221] transition hover:bg-[#7c0221]/10 sm:pe-3"
                   aria-label="{{ __('layout.nav.profile') }}">

                    <x-avatar :user="auth()->user()" size="h-8 w-8 sm:h-9 sm:w-9" />

                    <span class="hidden max-w-24 truncate lg:inline">
                        {{ auth()->user()->name }}
                    </span>
                </a>

                {{-- Logout --}}
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="flex h-9 items-center justify-center rounded-full px-2 text-xs font-semibold text-[#7c0221] transition hover:bg-[#7c0221]/10 sm:px-3 sm:text-sm"
                            aria-label="{{ __('layout.nav.logout_label') }}">
                        <svg class="h-5 w-5 sm:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 4h3a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-3M10 16l-4-4m0 0 4-4m-4 4h11"/></svg>
                        <span class="hidden sm:inline">{{ __('layout.nav.logout') }}</span>
                    </button>
                </form>

            @else

                <a href="{{ route('login') }}"
                   class="rounded-full px-2 py-2 text-xs font-semibold text-[#7c0221] transition hover:bg-[#7c0221]/10 sm:px-3 sm:text-sm">
                    {{ __('layout.nav.login') }}
                </a>

                <a href="{{ route('signup') }}"
                   class="rounded-full bg-[#7c0221] px-3 py-2 text-xs font-bold text-[#fff8cf] transition hover:bg-[#580016] sm:px-5 sm:text-sm">
                    {{ __('layout.nav.signup') }}
                </a>

            @endauth

        </div>

    </div>
</nav>
