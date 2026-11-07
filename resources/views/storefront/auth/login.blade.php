@extends('layouts.storefront')

@section('meta_title', 'Sign In — BanglaPuzzle')
@section('meta_description', 'Sign in to your BanglaPuzzle account.')

@section('content')
<div class="max-w-md mx-auto px-4 sm:px-0 py-12">
    <div class="bg-white rounded-2xl shadow-lg p-8">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-slate-900">Sign In</h1>
            <p class="text-sm text-slate-500 mt-1">Welcome back</p>
        </div>

        {{-- Validation errors summary --}}
        @if ($errors->any())
            <div class="mb-4">
                <x-alert type="error" :message="$errors->first()" />
            </div>
        @endif

        <form method="POST" action="{{ route('user.login.post') }}" novalidate>
            @csrf

            {{-- Email --}}
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1">
                    Email address
                </label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                    autofocus
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('email') border-red-400 @enderror"
                >
                @error('email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-slate-700 mb-1">
                    Password
                </label>
                <input
                    id="password"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('password') border-red-400 @enderror"
                >
                @error('password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember me --}}
            <div class="mb-6 flex items-center">
                <input
                    id="remember"
                    type="checkbox"
                    name="remember"
                    class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                >
                <label for="remember" class="ml-2 block text-sm text-slate-700">
                    Remember me
                </label>
            </div>

            <button
                type="submit"
                class="w-full min-h-[48px] rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
            >
                Sign In
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-500">
            Don't have an account?
            <a href="{{ route('user.register') }}" class="font-semibold text-blue-600 hover:text-blue-700">Register</a>
        </p>
    </div>
</div>
@endsection
