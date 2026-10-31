
@extends('Layout.AuthImg')

@section('title', __('messages.login'))

@section('form')

<form action="{{ route('login.submit') }}" method="POST" class="w-full">

    @csrf

    <h2 class="mb-7 text-center text-3xl font-bold text-[#7c0221]">
        {{ __('messages.login') }}
    </h2>

    @if ($errors->any())
        <div class="mb-5 rounded-xl bg-red-50 p-3 text-sm text-red-600">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="mb-5">
        <label for="email" class="label">
            {{ __('messages.email') }}
        </label>

        <input
            id="email"
            name="email"
            type="email"
            value="{{ old('email') }}"
            class="input"
            required
            autocomplete="email"
            aria-describedby="email-hint"
        >

        <p id="email-hint" class="mt-1 text-xs text-gray-500">
            {{ __('messages.login_email_hint') }}
        </p>
    </div>

    <div class="mb-6">
        <label for="password" class="label">
            {{ __('messages.password') }}
        </label>

        <input
            id="password"
            name="password"
            type="password"
            class="input"
            required
            autocomplete="current-password"
            aria-describedby="password-hint"
        >

        <p id="password-hint" class="mt-1 text-xs text-gray-500">
            {{ __('messages.login_password_hint') }}
        </p>
    </div>

    <button type="submit" class="btn-primary w-full">
        {{ __('messages.login_button') }}
    </button>

    <p class="mt-6 text-center text-sm text-gray-600">
        {{ __('messages.signup_prompt') }}

        <a href="{{ route('signup') }}" class="font-bold text-[#7c0221] hover:underline">
            {{ __('messages.signup') }}
        </a>
    </p>

</form>

@endsection
