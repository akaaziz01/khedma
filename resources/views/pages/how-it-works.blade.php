@extends('layouts.app')

@section('title', 'How It Works | Khedma')

@section('content')
@php
    $clientSteps = [
        ['icon' => 'bi-search', 'title' => 'Search for a service', 'text' => 'Look up a service by keyword, trade or city. No account is needed to browse.'],
        ['icon' => 'bi-people-fill', 'title' => 'Explore and compare providers', 'text' => 'Open public profiles to see trade, skills, rates and reviews. Compare up to 4 providers side by side and save your favorites.'],
        ['icon' => 'bi-calendar-check-fill', 'title' => 'Book your provider', 'text' => 'Send a booking request to the provider you picked and follow its status from your dashboard.'],
        ['icon' => 'bi-star-fill', 'title' => 'Leave a review', 'text' => 'Once the service is done, rate your provider to help other clients choose.'],
    ];

    $providerSteps = [
        ['icon' => 'bi-person-plus-fill', 'title' => 'Create your provider account', 'text' => 'Sign up as a provider in a few clicks.'],
        ['icon' => 'bi-person-badge-fill', 'title' => 'Build your public profile', 'text' => 'Add your trade, skills, services, rates and portfolio so clients can trust you.'],
        ['icon' => 'bi-tools', 'title' => 'Publish your services', 'text' => 'List what you offer so clients can find you through search.'],
        ['icon' => 'bi-inbox-fill', 'title' => 'Manage your requests', 'text' => 'Receive booking requests in Bookings & Leads and accept or decline them.'],
    ];
@endphp

<!-- Header -->
<section class="bg-gradient-to-b from-blue-50/50 via-white to-white dark:from-slate-900 dark:via-slate-950 dark:to-slate-950 py-14 sm:py-20 border-b border-slate-100 dark:border-slate-800 transition-colors duration-200">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-4">
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-100/80 dark:bg-blue-950/60 text-blue-800 dark:text-blue-300 text-xs font-bold tracking-wide uppercase border border-blue-200/50 dark:border-blue-800/40">
            <i class="bi bi-lightbulb text-amber-500"></i>
            <span>How It Works</span>
        </div>
        <h1 class="text-3xl sm:text-5xl font-black text-slate-900 dark:text-white tracking-tight">
            Finding or offering a service, <span class="text-blue-600 dark:text-blue-400">made simple.</span>
        </h1>
        <p class="text-base text-slate-600 dark:text-slate-400 leading-relaxed">
            KHEDMA connects clients with trusted service providers across Tunisia. Here is how it works for both sides.
        </p>
    </div>
</section>

<!-- Client steps -->
<section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <div class="flex items-center gap-3 mb-8">
        <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-950/60 text-blue-600 flex items-center justify-center">
            <i class="bi bi-person-fill text-xl"></i>
        </div>
        <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">For clients</h2>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @foreach($clientSteps as $index => $step)
            <div class="p-5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex gap-4">
                <div class="w-11 h-11 shrink-0 rounded-xl bg-blue-600 text-white font-extrabold flex items-center justify-center">
                    {{ $index + 1 }}
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-900 dark:text-white text-sm flex items-center gap-2">
                        <i class="bi {{ $step['icon'] }} text-blue-500"></i>
                        {{ $step['title'] }}
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">{{ $step['text'] }}</p>
                </div>
            </div>
        @endforeach
    </div>
</section>

<!-- Provider steps -->
<section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-14">
    <div class="flex items-center gap-3 mb-8">
        <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 flex items-center justify-center">
            <i class="bi bi-tools text-xl"></i>
        </div>
        <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">For providers</h2>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @foreach($providerSteps as $index => $step)
            <div class="p-5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex gap-4">
                <div class="w-11 h-11 shrink-0 rounded-xl bg-amber-500 text-white font-extrabold flex items-center justify-center">
                    {{ $index + 1 }}
                </div>
                <div>
                    <h3 class="font-extrabold text-slate-900 dark:text-white text-sm flex items-center gap-2">
                        <i class="bi {{ $step['icon'] }} text-amber-500"></i>
                        {{ $step['title'] }}
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1.5 leading-relaxed">{{ $step['text'] }}</p>
                </div>
            </div>
        @endforeach
    </div>
</section>

<!-- Call to action -->
<section class="bg-slate-900 text-white py-14">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-5">
        <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Still have questions?</h2>
        <p class="text-slate-400 text-sm">Check our FAQ or start exploring services right away.</p>
        <div class="flex items-center justify-center gap-3 flex-wrap">
            <a href="{{ route('faq') }}" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md transition">
                Read the FAQ
            </a>
            <a href="{{ route('services.index') }}" class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-sm shadow-md transition">
                Find a Service
            </a>
        </div>
    </div>
</section>
@endsection