@extends('Layout.AuthImg')

@section('title', __('messages.signup'))

@section('form')

<form action="{{ route('register') }}" method="POST" class="w-full">

    @csrf

    <h2 class="mb-6 text-center text-3xl font-bold text-[#7c0221]">
        {{ __('messages.signup') }}
    </h2>

    @if ($errors->any())
        <div class="mb-5 rounded-xl bg-red-50 p-3 text-sm text-red-600">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- Name --}}
    <div class="mb-4">
        <label for="name" class="label">
            {{ __('messages.name') }}
        </label>

        <input
            id="name"
            name="name"
            type="text"
            value="{{ old('name') }}"
            class="input"
            required
            minlength="2"
            maxlength="50"
            autocomplete="name"
            aria-describedby="name-hint"
        >

        <p id="name-hint" class="mt-1 text-xs text-gray-500">
            {{ __('messages.name_hint') }}
        </p>
    </div>

    {{-- Email --}}
    <div class="mb-4">
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
            {{ __('messages.email_hint') }}
        </p>
    </div>

    {{-- Username --}}
    <div class="mb-4">
        <label for="username" class="label">
            {{ __('messages.username') }}
        </label>

        <input
            id="username"
            name="username"
            type="text"
            value="{{ old('username') }}"
            minlength="3"
            maxlength="30"
            pattern="[A-Za-z0-9_]+"
            required
            autocomplete="username"
            placeholder="kunasha_user"
            class="input"
            aria-describedby="username-hint"
        >

        <p id="username-hint" class="mt-1 text-xs text-gray-500">
            {{ __('messages.username_hint') }}
        </p>
    </div>

    {{-- Password --}}
    <div class="mb-5">
        <label for="password" class="label">
            {{ __('messages.password') }}
        </label>

        <input
            id="password"
            name="password"
            type="password"
            class="input"
            required
            minlength="8"
            autocomplete="new-password"
            aria-describedby="password-hint"
        >

        <p id="password-hint" class="mt-1 text-xs text-gray-500">
            {{ __('messages.password_hint') }}
        </p>
    </div>

    <p class="mb-5 text-sm text-gray-600">
        {{ __('messages.login_prompt') }}

        <a href="{{ route('login') }}" class="font-bold text-[#7c0221] hover:underline">
            {{ __('messages.login') }}
        </a>
    </p>

    <button type="submit" class="btn-primary w-full">
        {{ __('messages.signup_button') }}
    </button>

</form>

@endsection
