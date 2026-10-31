{{--
    Standalone layout for error pages: no navbar or database calls, so it still
    renders when the error itself comes from the database or the main layout.
--}}
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#580016">
    <meta name="robots" content="noindex">
    <title>@yield('title') | كُنّاشة</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:ital,wght@0,400;0,700;1,400&family=Aref+Ruqaa:wght@400;700&family=Noto+Naskh+Arabic:wght@400;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="flex min-h-screen items-center justify-center px-5 py-12 antialiased">

    <div class="pointer-events-none fixed inset-0 -z-10 bg-[#ece3d8] bg-cover bg-center"
         style="background-image: url('{{ asset('images/background.webp') }}');"
         aria-hidden="true"></div>

    <main class="kn-panel kn-frame w-full max-w-lg px-8 py-12 text-center sm:px-12" style="--kn-accent: #a8834a;">

        <a href="{{ url('/') }}" class="inline-block" aria-label="{{ __('errors.logo_label') }}">
            <img src="{{ asset('images/logo-mark.webp') }}" alt="كُنّاشة" width="365" height="240" class="mx-auto h-16 w-auto">
        </a>

        <p class="brand-heading mt-6 text-7xl font-bold leading-none text-[#7c0221]/90" dir="ltr">@yield('code')</p>

        <x-ornament class="mx-auto mt-6 w-44 text-[#a8834a]" />

        <h1 class="brand-heading mt-6 text-3xl font-bold text-[#7c0221]">@yield('title')</h1>

        <p class="mx-auto mt-4 max-w-sm font-quote text-lg leading-9 text-[#5b4a43]">@yield('message')</p>

        <div class="mt-9 flex flex-wrap items-center justify-center gap-3">
            <a href="{{ url('/') }}" class="btn-primary px-8 text-sm">{{ __('errors.home') }}</a>
            @yield('actions')
        </div>
    </main>

</body>
</html>
