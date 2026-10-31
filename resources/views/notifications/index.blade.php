@extends('Layout.app')

@section('title', __('notifications.title').' | كُنّاشة')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-12 sm:px-6">

    {{-- Header --}}
    <div class="mb-9 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="brand-heading text-4xl font-bold text-[#7c0221] sm:text-5xl">
                {{ __('notifications.title') }}
            </h1>
            <p class="mt-2 text-sm text-[#5b4a43]">
                {{ __('notifications.intro') }}
            </p>
        </div>

        @if (auth()->user()->unreadNotifications()->exists())
            <form action="{{ route('notifications.readAll') }}" method="POST">
                @csrf
                <button type="submit" class="btn-ghost px-5 py-2.5">
                    {{ __('notifications.mark_all') }}
                </button>
            </form>
        @endif
    </div>

    <x-ornament class="mb-9 w-full text-[#a8834a]/70" />

    <x-flash />

    {{-- Notifications --}}
    <div class="space-y-4">

        @forelse ($notifications as $notification)

            @php
                $data = $notification->data;
                $isUnread = is_null($notification->read_at);
            @endphp

            <form
                action="{{ route('notifications.open', $notification->id) }}"
                method="POST"
                class="relative"
            >
                @csrf

                <button type="submit"
                    @class([
                        'flex w-full items-start gap-4 rounded-3xl border p-5 text-start transition hover:-translate-y-0.5 hover:shadow-md sm:p-6',
                        'border-[#a8834a]/50 bg-[#fff8cf]/90' => $isUnread,
                        'border-[#a8834a]/20 bg-[#fbf7ee]/90' => ! $isUnread,
                    ])>

                    {{-- Avatar --}}
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#7c0221] text-lg font-bold text-[#fff8cf] ring-4 ring-[#f9e0b0]/50">
                        {{ mb_substr($data['mentioned_by_name'] ?? '?', 0, 1) }}
                    </span>

                    {{-- Content --}}
                    <span class="min-w-0 flex-1">

                        <span class="flex flex-wrap items-center gap-2">
                            <span class="font-bold text-[#292929]">
                                {{ $data['mentioned_by_name'] ?? __('notifications.someone') }}
                            </span>

                            <span class="text-sm text-[#707070]">
                                {{ __('notifications.mentioned_you') }}
                            </span>

                            @if ($isUnread)
                                <span class="rounded-full bg-[#7c0221] px-2.5 py-0.5 text-[10px] font-bold text-[#f9e0b0]">
                                    {{ __('notifications.new') }}
                                </span>
                            @endif
                        </span>

                        <span dir="auto" class="mt-3 block rounded-2xl border-s-4 border-[#a8834a]/60 bg-white/80 px-4 py-3 font-quote text-lg leading-8 text-[#3b2a24]">
                            {{ $data['quote_content'] ?? __('notifications.fallback_content') }}
                        </span>

                        <span class="mt-3 block text-xs text-[#8a8a8a]">
                            {{ $notification->created_at->locale(app()->getLocale())->diffForHumans() }}
                        </span>

                    </span>

                </button>
            </form>

        @empty

            <div class="kn-panel px-6 py-16 text-center">
                <div class="brand-heading text-3xl text-[#7c0221]">
                    {{ __('notifications.empty_title') }}
                </div>
                <x-ornament class="mx-auto mt-4 w-32 text-[#a8834a]" />
                <p class="mt-4 text-sm text-[#707070]">
                    {{ __('notifications.empty_hint') }}
                </p>
            </div>

        @endforelse

    </div>

    {{-- Pagination --}}
    <div class="mt-8">
        {{ $notifications->links() }}
    </div>

</div>
@endsection
