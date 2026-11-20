@extends('layouts.app')

@section('title', 'Compare Providers Side by Side | Khedma')

@section('content')
<div class="bg-slate-50 dark:bg-slate-950 min-h-screen py-10 transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
            <a href="/" class="hover:text-blue-600 dark:hover:text-blue-400">Home</a>
            <i class="bi bi-chevron-right text-[10px]"></i>
            <a href="{{ route('services.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400">Services</a>
            <i class="bi bi-chevron-right text-[10px]"></i>
            <span class="text-slate-900 dark:text-white">Compare Providers</span>
        </nav>

        <!-- Header Banner & Trade Filters -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200 dark:border-slate-800 space-y-6">
            <div class="max-w-3xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 text-xs font-bold uppercase tracking-wider mb-3">
                    <i class="bi bi-layout-split"></i>
                    <span>Side-by-Side Comparison Tool</span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight">
                    Compare Providers with Confidence
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                    Evaluate rates, years of experience, and verified client reviews side-by-side to make the best choice for your home and business.
                </p>
            </div>

            <!-- Trade Category Filter Pills -->
            <div class="flex items-center gap-2 flex-wrap pt-2 border-t border-slate-100 dark:border-slate-800">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 mr-1">Filter by Trade:</span>
                <a href="{{ route('providers.compare') }}" @class([
                    'px-3 py-1.5 rounded-xl text-xs font-bold transition',
                    'bg-amber-600 text-white shadow-sm' => empty($category),
                    'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' => !empty($category),
                ])>
                    All Trades
                </a>
                @foreach($categories as $cat)
                    @php
                        $isSelectedCat = (strtolower((string)$category) === strtolower((string)$cat->slug) || strtolower((string)$category) === strtolower((string)$cat->name));
                    @endphp
                    <a href="{{ route('providers.compare', ['category' => $cat->slug]) }}" @class([
                        'px-3 py-1.5 rounded-xl text-xs font-bold transition',
                        'bg-amber-600 text-white shadow-sm' => $isSelectedCat,
                        'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700' => !$isSelectedCat,
                    ])>
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>

            <!-- Provider Quick Selector Bar -->
            <div class="bg-slate-50 dark:bg-slate-800/60 rounded-2xl p-4 border border-slate-200/80 dark:border-slate-700 space-y-3">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1.5">
                        <i class="bi bi-people-fill text-amber-500"></i>
                        <span>Select professionals to compare (up to 4):</span>
                    </span>
                    @if($providers->count() >= 2)
                        <button type="button" onclick="window.print()" class="text-xs font-bold text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white flex items-center gap-1">
                            <i class="bi bi-printer"></i>
                            <span>Print / Save Table</span>
                        </button>
                    @endif
                </div>

                <!-- Add/Swap Provider Controls -->
                <form action="{{ route('providers.compare') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                    @if(!empty($category))
                        <input type="hidden" name="category" value="{{ $category }}">
                    @endif

                    @for($col = 0; $col < 4; $col++)
                        @php
                            $currentSelectedId = $providers->values()->get($col)?->id ?? ($ids[$col] ?? null);
                        @endphp
                        <div class="relative">
                            <label class="text-[10px] font-bold text-slate-400 block mb-1">Provider Slot {{ $col + 1 }}</label>
                            <select name="ids[]" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-xs font-semibold text-slate-800 dark:text-slate-200 focus:ring-2 focus:ring-amber-500 cursor-pointer">
                                <option value="">+ Choose Provider...</option>
                                @foreach($availableProviders as $avail)
                                    <option value="{{ $avail->id }}" {{ $currentSelectedId == $avail->id ? 'selected' : '' }}>
                                        {{ $avail->name }} ({{ $avail->providerProfile?->category ?? 'Pro' }} - {{ $avail->providerProfile?->city ?? 'Tunisia' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endfor
                </form>
            </div>
        </div>

        @if($providers->count() < 2)
            <!-- Insufficient Providers Selected Warning & Presets -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-8 border border-slate-200 dark:border-slate-800 space-y-6 max-w-3xl mx-auto shadow-sm">
                @if($providers->count() === 1)
                    @php $firstPro = $providers->first(); @endphp
                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/50">
                        <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-amber-500 to-orange-600 text-white font-black text-lg flex items-center justify-center shrink-0 shadow-sm">
                            {{ strtoupper(substr($firstPro->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="text-[10px] font-black uppercase tracking-wider text-amber-700 dark:text-amber-400 block">1 Provider Selected</span>
                            <h4 class="font-black text-sm text-slate-900 dark:text-white truncate">{{ $firstPro->name }}</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $firstPro->providerProfile?->category ?? 'Contractor' }} &bull; {{ $firstPro->providerProfile?->hourly_rate ? number_format($firstPro->providerProfile->hourly_rate, 0) . ' TND/hr' : 'Custom Quote' }} &bull; {{ $firstPro->providerProfile?->city ?? 'Tunisia' }}</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <h4 class="text-xs font-black text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="bi bi-plus-circle-fill text-amber-500"></i>
                            <span>Choose a professional below to compare against {{ $firstPro->name }}:</span>
                        </h4>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($availableProviders->where('id', '!=', $firstPro->id)->take(4) as $avail)
                                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700 flex items-center justify-between gap-3 hover:border-amber-400 transition">
                                    <div class="min-w-0">
                                        <div class="font-black text-xs text-slate-900 dark:text-white truncate">{{ $avail->name }}</div>
                                        <div class="text-[11px] text-slate-400 truncate">{{ $avail->providerProfile?->category ?? 'Pro' }} &bull; {{ $avail->providerProfile?->city ?? 'Tunisia' }}</div>
                                        <div class="text-[11px] font-bold text-amber-600 dark:text-amber-400 mt-0.5">
                                            {{ $avail->providerProfile?->hourly_rate ? number_format($avail->providerProfile->hourly_rate, 0) . ' TND/hr' : 'Quote' }}
                                        </div>
                                    </div>
                                    <a href="{{ route('providers.compare', ['ids' => $firstPro->id . ',' . $avail->id, 'category' => $category]) }}" 
                                       class="px-3 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs transition shrink-0 flex items-center gap-1 shadow-sm">
                                        <i class="bi bi-plus-lg"></i>
                                        <span>Compare</span>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="w-16 h-16 rounded-3xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center mx-auto text-3xl">
                        <i class="bi bi-columns-gap"></i>
                    </div>
                    <div class="space-y-1 text-center">
                        <h3 class="text-xl font-black text-slate-900 dark:text-white">Select at least 2 providers to compare</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 max-w-md mx-auto leading-relaxed">
                            Use the selectors above or choose one of our verified contractor presets below to view rates, experience, and client feedback side-by-side.
                        </p>
                    </div>

                    @if($availableProviders->count() >= 2)
                        <div class="pt-2 text-center">
                            @php
                                $presetIds = $availableProviders->take(3)->pluck('id')->implode(',');
                            @endphp
                            <a href="{{ route('providers.compare', ['ids' => $presetIds]) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-xs shadow-md shadow-amber-500/20 transition">
                                <i class="bi bi-lightning-charge-fill"></i>
                                <span>Compare Top Available Professionals</span>
                            </a>
                        </div>
                    @else
                        <div class="pt-2 text-center">
                            <a href="{{ route('services.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-md transition">
                                <i class="bi bi-search"></i>
                                <span>Browse Services Directory</span>
                            </a>
                        </div>
                    @endif
                @endif
            </div>
        @else
            <!-- ======================================================== -->
            <!-- SIDE BY SIDE COMPARISON MATRIX                           -->
            <!-- ======================================================== -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-xl shadow-slate-200/40 dark:shadow-none overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse min-w-[700px]">
                        <thead>
                            <tr class="bg-slate-50/80 dark:bg-slate-800/40 border-b border-slate-200 dark:border-slate-800">
                                <th class="p-6 w-1/4 min-w-[200px] text-xs font-black uppercase tracking-wider text-slate-400 align-top">
                                    <div class="space-y-1">
                                        <span class="text-slate-900 dark:text-white block font-black text-sm">Comparison Criteria</span>
                                        <span class="text-[11px] font-normal text-slate-400">Comparing {{ $providers->count() }} Professionals</span>
                                    </div>
                                </th>
                                @foreach($providers as $provider)
                                    @php
                                        $profile = $provider->providerProfile;
                                        $reviews = $provider->providerReviews;
                                        $avgRating = $reviews->count() > 0 ? round($reviews->avg('rating'), 1) : 5.0;
                                        $hourly = $profile?->hourly_rate ? (float) $profile->hourly_rate : null;
                                        $expYears = $profile?->experience_years ?? 1;

                                        // Highlights
                                        $isBestPrice = ($minRate && $hourly === $minRate);
                                        $isTopRated = ($maxRating && $avgRating === $maxRating);
                                        $isMostExperienced = ($maxExp && $expYears === $maxExp);
                                    @endphp
                                    <th class="p-6 w-1/4 min-w-[240px] border-l border-slate-200/60 dark:border-slate-800/60 align-top">
                                        <div class="space-y-3">
                                            <!-- Provider Avatar & Name -->
                                            <div class="flex items-start justify-between gap-2">
                                                <div class="flex items-center gap-3">
                                                    <div class="relative">
                                                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-amber-500 text-white font-extrabold text-base flex items-center justify-center shadow-md">
                                                            {{ strtoupper(substr($provider->name, 0, 1)) }}
                                                        </div>
                                                        @if($profile?->is_available)
                                                            <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-emerald-500 border-2 border-white dark:border-slate-900" title="Available for jobs"></span>
                                                        @endif
                                                    </div>
                                                    <div>
                                                        <a href="{{ route('providers.show', $provider->id) }}" class="font-black text-sm text-slate-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition block truncate max-w-[140px]">
                                                            {{ $provider->name }}
                                                        </a>
                                                        <span class="text-[11px] font-bold text-slate-400 block truncate max-w-[140px]">
                                                            {{ $profile?->business_name ?? $profile?->category ?? 'Contractor' }}
                                                        </span>
                                                    </div>
                                                </div>

                                                <!-- Remove button -->
                                                @php
                                                    $remainingIds = $providers->pluck('id')->reject(fn($id) => $id === $provider->id)->implode(',');
                                                @endphp
                                                <a href="{{ route('providers.compare', array_filter(['ids' => $remainingIds ?: null, 'category' => $category ?: null])) }}" title="Remove from comparison" class="p-1 rounded-lg text-slate-300 hover:text-red-500 transition">
                                                    <i class="bi bi-x-circle text-base"></i>
                                                </a>
                                            </div>

                                            <!-- Advantage Badges -->
                                            <div class="flex flex-wrap gap-1">
                                                @if($isBestPrice)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 border border-emerald-300/60 dark:border-emerald-700/60">
                                                        <i class="bi bi-tag-fill"></i> Best Price
                                                    </span>
                                                @endif
                                                @if($isTopRated)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300 border border-amber-300/60 dark:border-amber-700/60">
                                                        <i class="bi bi-trophy-fill"></i> Top Rated
                                                    </span>
                                                @endif
                                                @if($isMostExperienced && !$isBestPrice)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black bg-blue-100 dark:bg-blue-950/80 text-blue-800 dark:text-blue-300 border border-blue-300/60 dark:border-blue-700/60">
                                                        <i class="bi bi-award-fill"></i> Most Experienced
                                                    </span>
                                                @endif
                                            </div>

                                            <!-- Direct Booking CTA -->
                                            <div class="pt-1">
                                                <button type="button" 
                                                        data-provider-name="{{ $provider->name }}"
                                                        data-rate="{{ $profile?->hourly_rate ? number_format($profile->hourly_rate, 2) . ' TND/hr' : 'Custom Quote' }}"
                                                        data-provider-id="{{ $provider->id }}"
                                                        onclick="handleCompareBooking(this)" 
                                                        class="w-full py-2 px-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-sm transition flex items-center justify-center gap-1.5">
                                                    <span>Book Pro</span>
                                                    <i class="bi bi-calendar-check text-xs"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 text-xs">

                            <!-- ============================================== -->
                            <!-- SECTION: RATES & PRICING                       -->
                            <!-- ============================================== -->
                            <tr class="bg-slate-50/50 dark:bg-slate-800/20">
                                <td colspan="{{ $providers->count() + 1 }}" class="py-2.5 px-6 font-extrabold text-slate-400 uppercase tracking-wider text-[10px]">
                                    <i class="bi bi-cash-stack text-amber-500 mr-1"></i> Rates & Pricing Comparison
                                </td>
                            </tr>
                            
                            <!-- Hourly Rate -->
                            <tr>
                                <td class="py-4 px-6 font-bold text-slate-700 dark:text-slate-300">
                                    Standard Hourly Rate
                                    <span class="block text-[11px] font-normal text-slate-400 mt-0.5">Base labor charge per hour</span>
                                </td>
                                @foreach($providers as $provider)
                                    @php
                                        $profile = $provider->providerProfile;
                                        $hourly = $profile?->hourly_rate ? (float) $profile->hourly_rate : null;
                                        $isBest = ($minRate && $hourly === $minRate);
                                    @endphp
                                    <td @class([
                                        'py-4 px-6 border-l border-slate-200/60 dark:border-slate-800/60',
                                        'bg-emerald-50/30 dark:bg-emerald-950/20' => $isBest,
                                    ])>
                                        @if($hourly)
                                            <span class="text-base font-black text-slate-900 dark:text-white">
                                                {{ number_format($hourly, 2) }} <span class="text-xs font-bold text-blue-600 dark:text-blue-400">TND / hr</span>
                                            </span>
                                            @if($isBest)
                                                <span class="block text-[10px] font-bold text-emerald-600 dark:text-emerald-400 mt-0.5">Lowest Rate</span>
                                            @endif
                                        @else
                                            <span class="text-slate-400 italic">Custom Quote</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Starting Service Price -->
                            <tr>
                                <td class="py-4 px-6 font-bold text-slate-700 dark:text-slate-300">
                                    Starting Package Price
                                    <span class="block text-[11px] font-normal text-slate-400 mt-0.5">Lowest published service fee</span>
                                </td>
                                @foreach($providers as $provider)
                                    @php
                                        $minSvc = $provider->services->min('price');
                                    @endphp
                                    <td class="py-4 px-6 border-l border-slate-200/60 dark:border-slate-800/60">
                                        @if($minSvc)
                                            <span class="font-extrabold text-slate-900 dark:text-white">
                                                From {{ number_format($minSvc, 2) }} TND
                                            </span>
                                            <span class="block text-[10px] text-slate-400 mt-0.5">{{ $provider->services->count() }} packages listed</span>
                                        @else
                                            <span class="text-slate-400 italic">By request</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Booking Deposit -->
                            <tr>
                                <td class="py-4 px-6 font-bold text-slate-700 dark:text-slate-300">
                                    Platform Booking Deposit
                                </td>
                                @foreach($providers as $provider)
                                    <td class="py-4 px-6 border-l border-slate-200/60 dark:border-slate-800/60">
                                        <span class="font-extrabold text-emerald-600 dark:text-emerald-400">0.00 TND (Free)</span>
                                        <span class="block text-[10px] text-slate-400 mt-0.5">Pay only after service</span>
                                    </td>
                                @endforeach
                            </tr>

                            <!-- ============================================== -->
                            <!-- SECTION: EXPERIENCE & RELIABILITY              -->
                            <!-- ============================================== -->
                            <tr class="bg-slate-50/50 dark:bg-slate-800/20">
                                <td colspan="{{ $providers->count() + 1 }}" class="py-2.5 px-6 font-extrabold text-slate-400 uppercase tracking-wider text-[10px]">
                                    <i class="bi bi-award text-blue-500 mr-1"></i> Experience & Reliability
                                </td>
                            </tr>

                            <!-- Years of Experience -->
                            <tr>
                                <td class="py-4 px-6 font-bold text-slate-700 dark:text-slate-300">
                                    Experience in Craft
                                    <span class="block text-[11px] font-normal text-slate-400 mt-0.5">Years of practical field work</span>
                                </td>
                                @foreach($providers as $provider)
                                    @php
                                        $expYears = $provider->providerProfile?->experience_years ?? 1;
                                        $isExp = ($maxExp && $expYears === $maxExp && $maxExp > 1);
                                    @endphp
                                    <td @class([
                                        'py-4 px-6 border-l border-slate-200/60 dark:border-slate-800/60',
                                        'bg-blue-50/30 dark:bg-blue-950/20' => $isExp,
                                    ])>
                                        <span class="font-black text-slate-900 dark:text-white text-sm">
                                            {{ $expYears }} Year{{ $expYears > 1 ? 's' : '' }}
                                        </span>
                                        @if($isExp)
                                            <span class="block text-[10px] font-bold text-blue-600 dark:text-blue-400 mt-0.5">Most Experienced</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Completed Jobs Count -->
                            <tr>
                                <td class="py-4 px-6 font-bold text-slate-700 dark:text-slate-300">
                                    Completed Jobs
                                    <span class="block text-[11px] font-normal text-slate-400 mt-0.5">Finished through platform</span>
                                </td>
                                @foreach($providers as $provider)
                                    @php
                                        $jobs = $provider->providerBookings()->where('status', 'completed')->count();
                                    @endphp
                                    <td class="py-4 px-6 border-l border-slate-200/60 dark:border-slate-800/60">
                                        <span class="font-extrabold text-slate-900 dark:text-white">
                                            {{ $jobs }} Job{{ $jobs === 1 ? '' : 's' }}
                                        </span>
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Verified Pro Status -->
                            <tr>
                                <td class="py-4 px-6 font-bold text-slate-700 dark:text-slate-300">
                                    Verification & Trust
                                </td>
                                @foreach($providers as $provider)
                                    <td class="py-4 px-6 border-l border-slate-200/60 dark:border-slate-800/60">
                                        @if($provider->is_verified)
                                            <span class="inline-flex items-center gap-1 font-bold text-emerald-600 dark:text-emerald-400">
                                                <i class="bi bi-patch-check-fill text-sm"></i>
                                                <span>Verified Pro</span>
                                            </span>
                                            <span class="block text-[10px] text-slate-400 mt-0.5">Identity & trade checked</span>
                                        @else
                                            <span class="text-slate-400 font-semibold">Standard Account</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Member Since -->
                            <tr>
                                <td class="py-4 px-6 font-bold text-slate-700 dark:text-slate-300">
                                    Platform Seniority
                                </td>
                                @foreach($providers as $provider)
                                    <td class="py-4 px-6 border-l border-slate-200/60 dark:border-slate-800/60 text-slate-600 dark:text-slate-300 font-semibold">
                                        Member since {{ $provider->created_at ? $provider->created_at->format('M Y') : 'Recent' }}
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Professional Bio Snippet -->
                            <tr>
                                <td class="py-4 px-6 font-bold text-slate-700 dark:text-slate-300">
                                    Professional Overview
                                </td>
                                @foreach($providers as $provider)
                                    @php
                                        $bio = $provider->providerProfile?->bio;
                                    @endphp
                                    <td class="py-4 px-6 border-l border-slate-200/60 dark:border-slate-800/60">
                                        <p class="text-[11px] text-slate-600 dark:text-slate-400 line-clamp-3 leading-relaxed">
                                            {{ $bio ?: 'Experienced professional providing verified trade services across Tunisia.' }}
                                        </p>
                                    </td>
                                @endforeach
                            </tr>

                            <!-- ============================================== -->
                            <!-- SECTION: CLIENT REVIEWS & SATISFACTION         -->
                            <!-- ============================================== -->
                            <tr class="bg-slate-50/50 dark:bg-slate-800/20">
                                <td colspan="{{ $providers->count() + 1 }}" class="py-2.5 px-6 font-extrabold text-slate-400 uppercase tracking-wider text-[10px]">
                                    <i class="bi bi-star-fill text-amber-500 mr-1"></i> Client Reviews & Satisfaction
                                </td>
                            </tr>

                            <!-- Average Star Rating -->
                            <tr>
                                <td class="py-4 px-6 font-bold text-slate-700 dark:text-slate-300">
                                    Overall Rating
                                    <span class="block text-[11px] font-normal text-slate-400 mt-0.5">Average score out of 5</span>
                                </td>
                                @foreach($providers as $provider)
                                    @php
                                        $reviews = $provider->providerReviews;
                                        $avgRating = $reviews->count() > 0 ? round($reviews->avg('rating'), 1) : 5.0;
                                        $isTop = ($maxRating && $avgRating === $maxRating);
                                    @endphp
                                    <td @class([
                                        'py-4 px-6 border-l border-slate-200/60 dark:border-slate-800/60',
                                        'bg-amber-50/30 dark:bg-amber-950/20' => $isTop,
                                    ])>
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-base font-black text-slate-900 dark:text-white">{{ number_format($avgRating, 1) }}</span>
                                            <div class="flex items-center gap-0.5 text-amber-500 text-xs">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= round($avgRating))
                                                        <i class="bi bi-star-fill"></i>
                                                    @else
                                                        <i class="bi bi-star text-slate-300 dark:text-slate-600"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                        </div>
                                        <span class="block text-[10px] text-slate-400 mt-0.5">
                                            {{ $reviews->count() }} verified {{ $reviews->count() === 1 ? 'review' : 'reviews' }}
                                        </span>
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Latest Review Testimonial -->
                            <tr>
                                <td class="py-4 px-6 font-bold text-slate-700 dark:text-slate-300">
                                    Featured Client Quote
                                    <span class="block text-[11px] font-normal text-slate-400 mt-0.5">Feedback from real booking</span>
                                </td>
                                @foreach($providers as $provider)
                                    @php
                                        $latestRev = $provider->providerReviews->first();
                                    @endphp
                                    <td class="py-4 px-6 border-l border-slate-200/60 dark:border-slate-800/60">
                                        @if($latestRev)
                                            <blockquote class="italic text-[11px] text-slate-600 dark:text-slate-300 leading-relaxed border-l-2 border-amber-400 pl-2">
                                                "{{ $latestRev->comment }}"
                                            </blockquote>
                                            <span class="block text-[10px] text-slate-400 mt-1 font-semibold">
                                                — {{ $latestRev->client?->name ?? 'Verified Client' }} ({{ $latestRev->rating }}★)
                                            </span>
                                        @else
                                            <span class="text-slate-400 italic text-[11px]">No client reviews yet</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>

                            <!-- ============================================== -->
                            <!-- SECTION: TRADE, SKILLS & AVAILABILITY          -->
                            <!-- ============================================== -->
                            <tr class="bg-slate-50/50 dark:bg-slate-800/20">
                                <td colspan="{{ $providers->count() + 1 }}" class="py-2.5 px-6 font-extrabold text-slate-400 uppercase tracking-wider text-[10px]">
                                    <i class="bi bi-tools text-indigo-500 mr-1"></i> Trade, Skills & Location
                                </td>
                            </tr>

                            <!-- Trade & City -->
                            <tr>
                                <td class="py-4 px-6 font-bold text-slate-700 dark:text-slate-300">
                                    Primary Trade & City
                                </td>
                                @foreach($providers as $provider)
                                    @php
                                        $profile = $provider->providerProfile;
                                    @endphp
                                    <td class="py-4 px-6 border-l border-slate-200/60 dark:border-slate-800/60">
                                        <span class="font-extrabold text-slate-900 dark:text-white block">
                                            {{ $profile?->category ?? 'General Contractor' }}
                                        </span>
                                        <span class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1 mt-0.5">
                                            <i class="bi bi-geo-alt-fill text-rose-500 text-[10px]"></i>
                                            {{ $profile?->city ?? 'Tunisia' }}
                                        </span>
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Skills Tags -->
                            <tr>
                                <td class="py-4 px-6 font-bold text-slate-700 dark:text-slate-300">
                                    Specializations & Skills
                                </td>
                                @foreach($providers as $provider)
                                    @php
                                        $skills = $provider->providerProfile?->skills ?? [];
                                    @endphp
                                    <td class="py-4 px-6 border-l border-slate-200/60 dark:border-slate-800/60">
                                        @if(!empty($skills) && is_array($skills))
                                            <div class="flex flex-wrap gap-1">
                                                @foreach(array_slice($skills, 0, 4) as $skill)
                                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-[10px] font-semibold">
                                                        {{ $skill }}
                                                    </span>
                                                @endforeach
                                                @if(count($skills) > 4)
                                                    <span class="text-[10px] text-slate-400 font-semibold self-center">+{{ count($skills) - 4 }} more</span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-slate-400 italic">General repairs</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Availability -->
                            <tr>
                                <td class="py-4 px-6 font-bold text-slate-700 dark:text-slate-300">
                                    Current Availability
                                </td>
                                @foreach($providers as $provider)
                                    @php
                                        $isAvail = $provider->providerProfile?->is_available ?? true;
                                    @endphp
                                    <td class="py-4 px-6 border-l border-slate-200/60 dark:border-slate-800/60">
                                        <span @class([
                                            'inline-flex items-center gap-1.5 font-bold',
                                            'text-emerald-600 dark:text-emerald-400' => $isAvail,
                                            'text-slate-400' => !$isAvail,
                                        ])>
                                            <span @class([
                                                'w-2 h-2 rounded-full',
                                                'bg-emerald-500 animate-pulse' => $isAvail,
                                                'bg-slate-300' => !$isAvail,
                                            ])></span>
                                            <span>{{ $isAvail ? 'Available Now' : 'Schedule Ahead' }}</span>
                                        </span>
                                    </td>
                                @endforeach
                            </tr>

                            <!-- Portfolio Photos Count -->
                            <tr>
                                <td class="py-4 px-6 font-bold text-slate-700 dark:text-slate-300">
                                    Portfolio Projects
                                </td>
                                @foreach($providers as $provider)
                                    @php
                                        $photos = $provider->providerProfile?->portfolio_photos ?? [];
                                        $count = is_array($photos) ? count($photos) : 0;
                                    @endphp
                                    <td class="py-4 px-6 border-l border-slate-200/60 dark:border-slate-800/60">
                                        <span class="font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-1">
                                            <i class="bi bi-images text-blue-500"></i>
                                            <span>{{ $count }} photo{{ $count === 1 ? '' : 's' }}</span>
                                        </span>
                                    </td>
                                @endforeach
                            </tr>

                            <!-- ============================================== -->
                            <!-- FOOTER / FINAL ACTIONS                         -->
                            <!-- ============================================== -->
                            <tr class="bg-slate-50/80 dark:bg-slate-800/40">
                                <td class="py-6 px-6 font-bold text-slate-700 dark:text-slate-300">
                                    Next Step
                                </td>
                                @foreach($providers as $provider)
                                    @php
                                        $profile = $provider->providerProfile;
                                    @endphp
                                    <td class="py-6 px-6 border-l border-slate-200/60 dark:border-slate-800/60 space-y-2">
                                        <button type="button" 
                                                data-provider-name="{{ $provider->name }}"
                                                data-rate="{{ $profile?->hourly_rate ? number_format($profile->hourly_rate, 2) . ' TND/hr' : 'Custom Quote' }}"
                                                data-provider-id="{{ $provider->id }}"
                                                onclick="handleCompareBooking(this)" 
                                                class="w-full py-2.5 px-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-sm transition flex items-center justify-center gap-1.5">
                                            <span>Choose & Book</span>
                                            <i class="bi bi-arrow-right"></i>
                                        </button>
                                        <a href="{{ route('providers.show', $provider->id) }}" class="w-full py-1.5 px-3 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs text-center block transition">
                                            Full Profile
                                        </a>
                                    </td>
                                @endforeach
                            </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>
</div>

<!-- ======================================================== -->
<!-- BOOKING MODAL FOR COMPARISON PAGE                        -->
<!-- ======================================================== -->
<div id="compareBookingModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-6">
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-blue-100 dark:bg-blue-950/70 text-blue-600 dark:text-blue-400 flex items-center justify-center text-2xl shrink-0">
                    <i class="bi bi-calendar-check-fill"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white">Selected Provider</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Ready to book an appointment</p>
                </div>
            </div>
            <button type="button" onclick="closeCompareBookingModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
        </div>

        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60 space-y-2 text-xs">
            <div class="flex items-center justify-between">
                <span class="text-slate-500">Selected Pro:</span>
                <span id="compareModalProviderName" class="font-black text-slate-900 dark:text-white">Provider</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-slate-500">Estimated Rate:</span>
                <span id="compareModalRate" class="font-black text-blue-600 dark:text-blue-400">45.00 TND</span>
            </div>
        </div>

        <div class="space-y-2 text-center sm:text-left">
            <h4 class="text-sm font-extrabold text-slate-900 dark:text-white">
                Book with total confidence
            </h4>
            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                Connect directly with this verified professional. Sign in or register your free client account to finalize your date and address.
            </p>
        </div>

        <div class="space-y-2 pt-1">
            <a id="compareModalSignInLink" href="/login" class="w-full py-3 px-4 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs text-center shadow-md shadow-blue-500/20 transition block">
                Sign In to Schedule
            </a>
            <a href="/register?role=client" class="w-full py-3 px-4 rounded-2xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold text-xs text-center transition block">
                New to Khedma? Create Free Account
            </a>
        </div>
    </div>
</div>

<script>
    function handleCompareBooking(button) {
        const name = button.getAttribute('data-provider-name') || 'Provider';
        const rate = button.getAttribute('data-rate') || 'Custom Quote';
        const providerId = button.getAttribute('data-provider-id') || '';
        openCompareBookingModal(name, rate, providerId);
    }

    function openCompareBookingModal(name, rate, providerId) {
        document.getElementById('compareModalProviderName').textContent = name;
        document.getElementById('compareModalRate').textContent = rate;
        document.getElementById('compareModalSignInLink').href = '/login?redirect=' + encodeURIComponent('/providers/' + providerId);
        document.getElementById('compareBookingModal').classList.remove('hidden');
    }

    function closeCompareBookingModal() {
        document.getElementById('compareBookingModal').classList.add('hidden');
    }

    document.getElementById('compareBookingModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeCompareBookingModal();
        }
    });
</script>
@endsection
