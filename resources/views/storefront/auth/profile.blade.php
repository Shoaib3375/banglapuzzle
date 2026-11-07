@extends('layouts.storefront')

@section('meta_title', 'My Profile — BanglaPuzzle')
@section('meta_description', 'Manage your BanglaPuzzle account and delivery address.')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-2xl font-bold text-slate-900 mb-6">My Profile</h1>

    {{-- Personal Information + Delivery Address: one form, two cards --}}
    <form action="{{ route('storefront.profile.update') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Left: Personal Information --}}
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
                <h2 class="text-base font-semibold text-slate-900 mb-4">Personal Information</h2>

                {{-- Name --}}
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-1.5">Name</label>
                    <input type="text" id="name" name="name" autocomplete="name"
                           value="{{ old('name', $user->name ?? '') }}"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('name') border-red-400 @enderror">
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                    <input type="email" id="email" name="email" autocomplete="email"
                           value="{{ old('email', $user->email ?? '') }}"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('email') border-red-400 @enderror">
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Phone --}}
                <div class="mb-1">
                    <label for="phone" class="block text-sm font-medium text-slate-700 mb-1.5">Phone</label>
                    <input type="tel" id="phone" name="phone" autocomplete="tel"
                           value="{{ old('phone', $user->phone ?? '') }}"
                           placeholder="01XXXXXXXXX"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('phone') border-red-400 @enderror">
                    @error('phone')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        class="mt-5 w-full rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    Save Changes
                </button>
            </div>

            {{-- Right: Delivery Address --}}
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
                <h2 class="text-base font-semibold text-slate-900 mb-4">Delivery Address</h2>

                {{-- Address --}}
                <div class="mb-4">
                    <label for="address" class="block text-sm font-medium text-slate-700 mb-1.5">Address</label>
                    <input type="text" id="address" name="address" autocomplete="street-address"
                           value="{{ old('address', $user->address ?? '') }}"
                           placeholder="House 12, Road 5, Block C"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('address') border-red-400 @enderror">
                    @error('address')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- City --}}
                <div class="mb-4">
                    <label for="city" class="block text-sm font-medium text-slate-700 mb-1.5">City</label>
                    <input type="text" id="city" name="city" autocomplete="address-level2"
                           value="{{ old('city', $user->city ?? '') }}"
                           placeholder="Mirpur, Dhaka"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('city') border-red-400 @enderror">
                    @error('city')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- District --}}
                <div class="mb-4">
                    <label for="district" class="block text-sm font-medium text-slate-700 mb-1.5">District</label>
                    <select id="district" name="district"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 bg-white @error('district') border-red-400 @enderror">
                        <option value="">Select District</option>
                        @foreach(['Dhaka','Chittagong','Sylhet','Rajshahi','Khulna','Barisal','Rangpur','Mymensingh','Gazipur','Narayanganj','Comilla','Jessore','Bogra','Dinajpur','Tangail','Faridpur','Noakhali','Cox\'s Bazar'] as $d)
                            <option value="{{ $d }}" @selected($d === old('district', $user->district ?? ''))>{{ $d }}</option>
                        @endforeach
                    </select>
                    @error('district')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Postcode --}}
                <div class="mb-1">
                    <label for="postcode" class="block text-sm font-medium text-slate-700 mb-1.5">Postcode</label>
                    <input type="text" id="postcode" name="postcode" autocomplete="postal-code"
                           value="{{ old('postcode', $user->postcode ?? '') }}"
                           placeholder="1216"
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('postcode') border-red-400 @enderror">
                    @error('postcode')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        class="mt-5 w-full rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    Save Address
                </button>
            </div>
        </div>
    </form>

    {{-- Change Password: separate form, full width --}}
    <div class="mt-6 bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <h2 class="text-base font-semibold text-slate-900 mb-4">Change Password</h2>

        <form action="{{ route('storefront.profile.password') }}" method="POST" class="max-w-md">
            @csrf

            {{-- Current password --}}
            <div class="mb-4">
                <label for="current_password" class="block text-sm font-medium text-slate-700 mb-1.5">Current Password</label>
                <input type="password" id="current_password" name="current_password" autocomplete="current-password"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('current_password') border-red-400 @enderror">
                @error('current_password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- New password --}}
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-slate-700 mb-1.5">New Password</label>
                <input type="password" id="password" name="password" autocomplete="new-password"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('password') border-red-400 @enderror">
                @error('password')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirm new password --}}
            <div class="mb-1">
                <label for="password_confirmation" class="block text-sm font-medium text-slate-700 mb-1.5">Confirm New Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('password_confirmation') border-red-400 @enderror">
                @error('password_confirmation')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                    class="mt-5 w-full rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2 transition-colors">
                Change Password
            </button>
        </form>
    </div>

    {{-- My Orders --}}
    <div class="mt-6 bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div>
                <h2 class="text-base font-semibold text-slate-900">My Orders</h2>
                <p class="text-sm text-slate-500 mt-1">
                    You have placed
                    <span class="font-medium text-slate-900">{{ $user->orders()->count() }}</span>
                    {{ Str::plural('order', $user->orders()->count()) }}.
                </p>
            </div>
            <a href="{{ route('orders.index') }}"
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                View My Orders
            </a>
        </div>
    </div>
</div>
@endsection
