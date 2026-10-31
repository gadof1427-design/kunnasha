{{--
    Small decorative line in the *other* language: English under Arabic text,
    Arabic under English text. Line breaks in the translation become <br>.
--}}
@props(['key', 'replace' => []])

@php
    $accentLocale = app()->getLocale() === 'ar' ? 'en' : 'ar';
@endphp

<p lang="{{ $accentLocale }}" dir="{{ $accentLocale === 'ar' ? 'rtl' : 'ltr' }}" {{ $attributes->class(['font-quote', 'italic' => $accentLocale === 'en']) }}>{!! nl2br(e(__($key, $replace, $accentLocale))) !!}</p>
