@php
    $locale = app()->getLocale();
    $otherLocale = $locale === 'ar' ? 'en' : 'ar';
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#580016">
    <meta name="description" content="@yield('description', __('layout.meta_description'))">

    <title>@yield('title', 'كُنّاشة | KUNNASHA')</title>

    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    {{-- Link previews (WhatsApp, X, Telegram...). Pages can override with @section('og'). --}}
    @hasSection('og')
        @yield('og')
    @else
        <meta property="og:type" content="website">
        <meta property="og:title" content="@yield('title', 'كُنّاشة | KUNNASHA')">
        <meta property="og:description" content="{{ __('layout.og_description') }}">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:image" content="{{ asset('images/og.jpg') }}">
        <meta name="twitter:card" content="summary_large_image">
    @endif
    <meta property="og:site_name" content="كُنّاشة">
    <meta property="og:locale" content="{{ $locale === 'ar' ? 'ar_AR' : 'en_US' }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Aref+Ruqaa:wght@400;700&family=Noto+Naskh+Arabic:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Texts used by app.js (toasts, buttons), in the current language --}}
    <script>window.kunnasha = {{ Js::from(['i18n' => __('js')]) }};</script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="flex min-h-screen flex-col antialiased">

    {{-- Paper texture: one fixed layer behind everything (pages must not add their own, it would cover the footer). --}}
    <div class="pointer-events-none fixed inset-0 -z-10 bg-[#ece3d8] bg-cover bg-center"
         style="background-image: url('{{ asset('images/background.webp') }}');"
         aria-hidden="true"></div>

    @hasSection('auth-screen')
        @yield('content')
    @else

        <x-navbar :page="$page ?? ''" />

        <main class="flex-1">
            @yield('content')
        </main>

        <footer class="relative overflow-hidden bg-linear-to-b from-[#7c0221] to-[#4a0013] text-center text-[#fff8cf]">
            <div class="kn-pattern absolute inset-0 opacity-[0.06]" aria-hidden="true"></div>
            <div class="absolute inset-x-0 top-0 h-px bg-linear-to-r from-transparent via-[#f9e0b0]/60 to-transparent" aria-hidden="true"></div>

            <div class="relative mx-auto max-w-4xl px-6 pb-8 pt-14">

                <a href="{{ route('home') }}" class="brand-name text-4xl font-bold text-[#f9e0b0] transition hover:text-white">
                    كُنّاشة
                </a>

                <x-ornament class="mx-auto mt-4 w-52 text-[#f9e0b0]/70" />

                <p class="mt-5 font-quote text-lg leading-9 text-[#fff8cf]">
                    {!! nl2br(e(__('layout.tagline'))) !!}
                </p>

                <x-accent key="layout.tagline" class="mt-2 text-sm leading-6 text-[#f9e0b0]/75" />

                <nav class="mt-9 flex flex-wrap items-center justify-center gap-x-7 gap-y-3 text-sm font-semibold" aria-label="{{ __('layout.footer.links') }}">
                    <a href="{{ route('home') }}" class="text-[#fff8cf]/85 transition hover:text-[#f9e0b0]">{{ __('layout.nav.home') }}</a>
                    <a href="{{ route('explore') }}" class="text-[#fff8cf]/85 transition hover:text-[#f9e0b0]">{{ __('layout.nav.explore') }}</a>
                    @auth
                        <a href="{{ route('profile') }}" class="text-[#fff8cf]/85 transition hover:text-[#f9e0b0]">{{ __('layout.footer.profile') }}</a>
                        @if (auth()->user()->username)
                            <a href="{{ route('users.show', auth()->user()->username) }}" class="text-[#fff8cf]/85 transition hover:text-[#f9e0b0]">{{ __('layout.footer.public_page') }}</a>
                        @endif
                        <a href="{{ route('notifications.index') }}" class="text-[#fff8cf]/85 transition hover:text-[#f9e0b0]">{{ __('layout.footer.notifications') }}</a>
                    @else
                        <a href="{{ route('login') }}" class="text-[#fff8cf]/85 transition hover:text-[#f9e0b0]">{{ __('layout.footer.login') }}</a>
                        <a href="{{ route('signup') }}" class="text-[#fff8cf]/85 transition hover:text-[#f9e0b0]">{{ __('layout.footer.signup') }}</a>
                    @endauth
                </nav>

                <a href="{{ route('language.switch', $otherLocale) }}"
                   lang="{{ $otherLocale }}"
                   aria-label="{{ __('layout.switch.label') }}"
                   class="mt-7 inline-flex rounded-full border border-[#f9e0b0]/50 px-5 py-1.5 text-xs font-bold text-[#f9e0b0] transition hover:bg-[#f9e0b0] hover:text-[#580016]">
                    {{ __('layout.switch.long') }}
                </a>

                <p class="mt-10 border-t border-[#f9e0b0]/15 pt-6 text-xs text-[#f9e0b0]/70" dir="ltr">
                    © {{ date('Y') }} KUNNASHA. All rights reserved. by
                    <span class="font-bold text-[#f9e0b0]">Ghadof Abdulrahman</span>
                </p>

            </div>
        </footer>

        {{-- Add-quote button + modal, available on every page for signed-in users --}}
        @auth
            <x-quote-composer />
        @endauth

    @endif

</body>
</html>
