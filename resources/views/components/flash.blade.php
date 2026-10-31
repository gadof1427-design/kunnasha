{{-- Success / validation messages (the composer and the edit panels show their own errors). --}}
@if (session('success'))
    <div role="status"
         class="kn-rise mb-6 flex items-start gap-3 rounded-2xl border border-green-200 bg-green-50/95 px-5 py-3 text-sm font-semibold text-green-800">
        <svg class="mt-0.5 h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12 5 5L20 7"/></svg>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if ($errors->any() && ! old('_form'))
    <div role="alert"
         class="mb-6 rounded-2xl border border-red-200 bg-red-50/95 px-5 py-4 text-sm text-red-700">
        <ul class="list-inside list-disc space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
