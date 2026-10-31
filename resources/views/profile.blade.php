@extends('Layout.app')

@section('title', __('profile.title').' | كُنّاشة')

@section('content')

<div class="px-5 py-10 text-[#292929] sm:px-8">
    <div class="mx-auto max-w-5xl">

        {{-- Profile Header --}}
        <section class="kn-panel overflow-hidden">

            <div class="relative h-36 overflow-hidden bg-linear-to-l from-[#4a0013] via-[#7c0221] to-[#b6233e] sm:h-44">
                <div class="kn-pattern absolute inset-0 opacity-[0.08]" aria-hidden="true"></div>
                <span class="brand-heading absolute bottom-3 end-8 text-6xl text-white/15" aria-hidden="true">كُنّاشة</span>
            </div>

            <div class="px-6 pb-8 sm:px-10">
                <div class="flex flex-col items-center gap-4 text-center sm:flex-row sm:items-end sm:gap-6 sm:text-start">
                    {{-- z-10 keeps the photo above the cover it overlaps --}}
                    <div class="relative z-10 -mt-14 shrink-0">
                        <x-avatar :user="$user" size="h-28 w-28 text-4xl" class="shadow-lg ring-4 ring-[#fbf7ee]" />
                    </div>

                    <div class="min-w-0 flex-1 sm:pb-1">
                        <h1 class="brand-heading text-3xl font-bold text-[#7c0221] sm:text-4xl">
                            {{ $user->name }}
                        </h1>

                        <p class="mt-1 flex flex-wrap items-center justify-center gap-x-3 gap-y-1 text-sm text-[#707070] sm:justify-start">
                            @if ($user->username)
                                <span dir="ltr" class="font-semibold text-[#8c1126]">{{ '@'.$user->username }}</span>
                            @endif
                            <span dir="ltr">{{ $user->email }}</span>
                        </p>
                    </div>

                    @if ($user->username)
                        <a href="{{ route('users.show', $user->username) }}" class="btn-ghost shrink-0 px-5 py-2.5">
                            {{ __('profile.public_page') }}
                        </a>
                    @endif
                </div>

                <p class="mt-6 font-quote text-lg leading-9 text-[#5b4a43]">
                    {{ __('profile.intro') }}
                </p>
            </div>
        </section>

        <div class="mt-7">
            <x-flash />
        </div>

        {{-- Profile Settings --}}
        <section class="kn-panel p-6 sm:p-9">

            <div class="mb-7">
                <h2 class="brand-heading text-3xl font-bold text-[#7c0221]">
                    {{ __('profile.settings') }}
                </h2>
                <p class="mt-1 text-sm text-[#707070]">
                    {{ __('profile.settings_hint') }}
                </p>
            </div>

            <form
                action="{{ route('profile.update') }}"
                method="POST"
                enctype="multipart/form-data"
                class="space-y-6">

                @csrf
                @method('PUT')

                <div class="grid gap-6 sm:grid-cols-2">
                    {{-- Name --}}
                    <div>
                        <label for="name" class="mb-2 block text-sm font-semibold text-[#484848]">
                            {{ __('profile.name') }}
                        </label>

                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            required
                            minlength="2"
                            maxlength="50"
                            aria-describedby="name-hint"
                            class="kn-field">

                        <p id="name-hint" class="mt-2 text-xs text-[#8a8a8a]">
                            {{ __('profile.name_hint') }}
                        </p>

                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Username --}}
                    <div>
                        <label for="profile-username" class="mb-2 block text-sm font-semibold text-[#484848]">
                            {{ __('profile.username') }}
                        </label>

                        <input
                            id="profile-username"
                            type="text"
                            name="username"
                            value="{{ old('username', $user->username) }}"
                            minlength="3"
                            maxlength="30"
                            pattern="[A-Za-z0-9_]+"
                            required
                            dir="ltr"
                            class="kn-field text-left">

                        <p class="mt-2 text-xs text-[#8a8a8a]">
                            {{ __('profile.username_hint') }}
                        </p>

                        @error('username')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Profile Photo --}}
                <div>
                    <p class="mb-3 text-sm font-semibold text-[#484848]">
                        {{ __('profile.photo') }}
                    </p>

                    <div class="flex flex-col gap-5 rounded-2xl border border-dashed border-[#a8834a]/40 bg-white/60 p-5 sm:flex-row sm:items-center">
                        <div data-photo-preview class="shrink-0 self-center">
                            <x-avatar :user="$user" size="h-24 w-24 text-3xl" class="ring-4 ring-[#f9e0b0]/70" />
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-3">
                                <input
                                    id="profile-photo"
                                    type="file"
                                    name="profile_photo"
                                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                    data-photo-input
                                    class="peer sr-only">

                                <label for="profile-photo" class="btn-ghost cursor-pointer px-5 py-2.5 peer-focus-visible:ring-4 peer-focus-visible:ring-[#f9e0b0]/60">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 8h3l2-3h6l2 3h3v11H4z"/><circle cx="12" cy="13" r="3.5"/></svg>
                                    {{ $user->profile_photo_path ? __('profile.change_photo') : __('profile.choose_photo') }}
                                </label>

                                @if ($user->profile_photo_path)
                                    <button
                                        type="submit"
                                        form="delete-profile-photo"
                                        class="rounded-full border border-[#D4D4D4] px-5 py-2.5 text-sm font-semibold text-[#b6233e] transition hover:border-[#b6233e] hover:bg-[#b6233e]/5">
                                        {{ __('profile.delete_photo') }}
                                    </button>
                                @endif
                            </div>

                            <p data-photo-name class="mt-3 text-xs text-[#8a8a8a]">
                                {{ __('profile.photo_hint') }}
                            </p>
                        </div>
                    </div>

                    @error('profile_photo')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="border-t border-[#a8834a]/20 pt-6">
                    <button type="submit" class="btn-primary px-8 text-sm">
                        {{ __('profile.save') }}
                    </button>
                </div>
            </form>

            @if ($user->profile_photo_path)
                {{-- Kept outside the settings form because forms can't be nested; the "حذف الصورة" button targets it by id. --}}
                <form
                    id="delete-profile-photo"
                    action="{{ route('profile.photo.destroy') }}"
                    method="POST"
                    data-confirm="{{ __('profile.delete_photo_confirm') }}">
                    @csrf
                    @method('DELETE')
                </form>
            @endif
        </section>

        {{-- Statistics --}}
        <section class="mt-7 grid grid-cols-1 gap-5 sm:grid-cols-2">

            <div class="kn-panel flex items-center justify-between gap-4 p-7">
                <div>
                    <p class="text-sm text-[#707070]">{{ __('profile.total_quotes') }}</p>
                    <p class="brand-heading mt-1 text-5xl font-bold text-[#7c0221]">
                        {{ $quotesCount }}
                    </p>
                </div>

                <svg class="h-14 w-14 text-[#a8834a]/60" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" aria-hidden="true">
                    <path d="M8 10c6-2 11-1 16 3v26c-5-4-10-5-16-3z"/>
                    <path d="M40 10c-6-2-11-1-16 3v26c5-4 10-5 16-3z"/>
                </svg>
            </div>

            <div class="relative overflow-hidden rounded-3xl bg-linear-to-l from-[#7c0221] to-[#4a0013] p-7 text-[#fff8cf] shadow-[0_22px_48px_-30px_rgb(88_0_22/0.7)]">
                <div class="kn-pattern absolute inset-0 opacity-[0.07]" aria-hidden="true"></div>
                <div class="relative">
                    <p class="text-sm text-[#f9e0b0]/80">{{ __('profile.literary_space') }}</p>
                    <p class="brand-heading mt-2 text-2xl font-bold leading-relaxed text-[#f9e0b0]">
                        {!! nl2br(e(__('layout.tagline'))) !!}
                    </p>
                </div>
            </div>
        </section>

        {{-- User Quotes --}}
        <div class="mb-7 mt-14 flex flex-wrap items-end justify-between gap-4">
            <div>
                <h2 class="brand-heading text-3xl font-bold text-[#7c0221] sm:text-4xl">
                    {{ __('profile.my_quotes') }}
                </h2>
                <p class="mt-1 text-sm text-[#707070]">
                    {{ __('profile.my_quotes_hint') }}
                </p>
            </div>

            <button type="button" data-open-composer class="btn-primary px-6 py-2.5 text-sm">
                {{ __('profile.add_quote') }}
            </button>
        </div>

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
                <p class="brand-heading text-2xl font-bold text-[#7c0221]">
                    {{ __('profile.empty_title') }}
                </p>

                <x-ornament class="mx-auto mt-4 w-32 text-[#a8834a]" />

                <p class="mt-4 text-sm leading-7 text-[#707070]">
                    {{ __('profile.empty_hint') }}
                </p>

                <button type="button" data-open-composer class="btn-primary mt-6 px-7 text-sm">
                    {{ __('profile.write_first') }}
                </button>
            </div>

        @endif

        {{-- Back to Home --}}
        <div class="mt-12 text-center">
            <a
                href="{{ route('home') }}"
                class="text-sm font-semibold text-[#7c0221] underline decoration-[#a8834a] underline-offset-4 transition hover:text-[#580016]">
                {{ __('layout.back_home') }}
            </a>
        </div>

    </div>
</div>
@endsection
