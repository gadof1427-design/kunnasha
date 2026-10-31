{{--
    "Add quote" button + modal. Included once in the layout for signed-in users,
    so a quote can be written from any page.
--}}
@php
    $fromComposer = old('_form') === 'composer';
    $oldContent = $fromComposer ? old('content', '') : '';
    $oldAuthor = $fromComposer ? old('author', '') : '';
    $oldColor = $fromComposer ? old('color', '#FFFFFF') : '#FFFFFF';
    $reopen = $fromComposer && $errors->any();
@endphp

{{-- Floating add button --}}
<div class="fixed bottom-6 left-6 z-40 sm:bottom-8 sm:left-8">
    <button
        type="button"
        data-open-composer
        class="group flex h-14 items-center gap-2 rounded-full bg-[#7c0221] px-4 text-white shadow-[0_14px_30px_-12px_rgb(88_0_22/0.8)] ring-4 ring-[#fff8cf]/80 transition hover:bg-[#580016] focus:outline-none focus-visible:ring-[#f9e0b0] sm:px-5"
        aria-label="{{ __('layout.composer_button_label') }}"
        title="{{ __('layout.composer_button') }}">
        <svg class="h-6 w-6 shrink-0 transition group-hover:rotate-90" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
        <span class="hidden text-sm font-semibold sm:inline">{{ __('layout.composer_button') }}</span>
    </button>
</div>

{{-- Modal --}}
<div
    data-composer-modal
    data-max="{{ \App\Models\Quote::MAX_LENGTH }}"
    class="{{ $reopen ? 'flex' : 'hidden' }} fixed inset-0 z-50 items-start justify-center overflow-y-auto bg-[#1f0a10]/60 px-4 py-8 backdrop-blur-[3px] sm:items-center"
    role="dialog"
    aria-modal="true"
    aria-labelledby="composer-title">

    <div class="kn-frame my-auto w-full max-w-xl rounded-3xl border border-[#a8834a]/30 bg-[#fbf7ee] p-6 shadow-2xl sm:p-9"
         style="--kn-accent: #a8834a;">

        <div class="mb-6 flex items-start justify-between gap-4">
            <div>
                <h3 id="composer-title" class="brand-heading text-3xl font-bold text-[#7c0221] sm:text-4xl">{{ __('quotes.composer_title') }}</h3>
                <x-accent key="quotes.composer_subtitle" class="mt-1 text-sm text-[#a8834a]" />
            </div>

            <button type="button" data-close-composer
                    class="-m-2 rounded-full p-2 text-[#707070] transition hover:bg-[#7c0221]/5 hover:text-[#7c0221] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#7c0221]"
                    aria-label="{{ __('quotes.close') }}">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
            </button>
        </div>

        <x-ornament class="mb-6 w-full text-[#a8834a]/60" />

        @if ($reopen)
            <div role="alert" class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('quotes.store') }}" method="POST" data-once data-preview-root class="space-y-7">
            @csrf
            <input type="hidden" name="_form" value="composer">

            <x-quote-fields prefix="composer" :content="$oldContent" :author="$oldAuthor" :color="$oldColor" />

            <button type="submit" class="btn-primary w-full text-sm disabled:cursor-not-allowed disabled:opacity-60">
                {{ __('quotes.composer_submit') }}
            </button>
        </form>
    </div>
</div>
