@extends('layouts.app')

@section('title', 'FAQ | Khedma')

@section('content')
@php
    $faqGroups = [
        [
            'title' => 'General',
            'icon' => 'bi-info-circle-fill',
            'items' => [
                ['q' => 'What is Khedma?', 'a' => 'Khedma is a service marketplace for Tunisia. It connects clients who need a service with providers such as plumbers, electricians, cleaners and more.'],
                ['q' => 'Do I need an account to browse?', 'a' => 'No. You can search services and view provider profiles as a guest. An account is needed to book, save favorites or manage your activity.'],
                ['q' => 'Which services can I find?', 'a' => 'Plumbing, electricity, HVAC and climate, cleaning, painting, carpentry, IT and network, appliances and more.'],
            ],
        ],
        [
            'title' => 'For clients',
            'icon' => 'bi-person-fill',
            'items' => [
                ['q' => 'How do I find a provider?', 'a' => 'Use the search by keyword, trade or city, then filter results by price, rating and availability.'],
                ['q' => 'What can I see on a provider profile?', 'a' => 'Each public profile shows the provider trade, skills, services, rates, portfolio and client reviews.'],
                ['q' => 'Can I compare providers?', 'a' => 'Yes. Add up to 4 providers to the comparison dock and open the Compare page to see rates, experience and reviews side by side.'],
                ['q' => 'How do I save a provider to my favorites?', 'a' => 'Sign in as a client and use the favorite button on the provider. Your saved providers are listed in your dashboard.'],
                ['q' => 'How do I book a provider?', 'a' => 'Open the service or provider you like and send a booking request. The provider reviews it and accepts or declines.'],
                ['q' => 'How do I leave a review?', 'a' => 'After the service is completed, you can rate your provider and add a comment to help other clients.'],
            ],
        ],
        [
            'title' => 'For providers',
            'icon' => 'bi-tools',
            'items' => [
                ['q' => 'How do I become a provider?', 'a' => 'Create an account and choose the provider role, then complete your profile and publish your services.'],
                ['q' => 'How do I manage booking requests?', 'a' => 'Open Bookings & Leads from the menu to see incoming requests and handle them.'],
                ['q' => 'How do I update my services?', 'a' => 'Go to My Services from the menu to add or edit what you offer.'],
            ],
        ],
    ];
@endphp

<!-- Header -->
<section class="bg-gradient-to-b from-blue-50/50 via-white to-white dark:from-slate-900 dark:via-slate-950 dark:to-slate-950 py-14 sm:py-20 border-b border-slate-100 dark:border-slate-800 transition-colors duration-200">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-4">
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-100/80 dark:bg-blue-950/60 text-blue-800 dark:text-blue-300 text-xs font-bold tracking-wide uppercase border border-blue-200/50 dark:border-blue-800/40">
            <i class="bi bi-question-circle-fill text-amber-500"></i>
            <span>FAQ</span>
        </div>
        <h1 class="text-3xl sm:text-5xl font-black text-slate-900 dark:text-white tracking-tight">
            Frequently asked <span class="text-blue-600 dark:text-blue-400">questions</span>
        </h1>
        <p class="text-base text-slate-600 dark:text-slate-400 leading-relaxed">
            Quick answers about using KHEDMA as a client or as a provider.
        </p>
    </div>
</section>

<!-- Questions -->
<section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-14 space-y-10">
    @foreach($faqGroups as $group)
        <div>
            <h2 class="flex items-center gap-2 text-lg font-black text-slate-900 dark:text-white mb-4">
                <i class="bi {{ $group['icon'] }} text-blue-500"></i>
                {{ $group['title'] }}
            </h2>

            <div class="space-y-3">
                @foreach($group['items'] as $item)
                    <details class="group bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
                        <summary class="flex items-center justify-between gap-3 cursor-pointer list-none px-5 py-4 text-sm font-bold text-slate-900 dark:text-white">
                            <span>{{ $item['q'] }}</span>
                            <i class="bi bi-chevron-down text-xs text-slate-400 transition-transform group-open:rotate-180"></i>
                        </summary>
                        <p class="px-5 pb-4 text-xs sm:text-sm text-slate-600 dark:text-slate-400 leading-relaxed">
                            {{ $item['a'] }}
                        </p>
                    </details>
                @endforeach
            </div>
        </div>
    @endforeach
</section>

<!-- Link to How it works -->
<section class="bg-slate-900 text-white py-14">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-5">
        <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Want the big picture?</h2>
        <p class="text-slate-400 text-sm">See step by step how KHEDMA works for clients and providers.</p>
        <a href="{{ route('how-it-works') }}" class="inline-block px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md transition">
            How It Works
        </a>
    </div>
</section>
@endsection