@extends('layouts.app')

@section('title', 'Find Verified Specialists & Providers | Khedma')

@section('content')
<div class="bg-slate-50 dark:bg-slate-950 min-h-screen py-10 transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Header Banner & Search Controls -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200 dark:border-slate-800">
            <div class="max-w-3xl mb-6">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 text-xs font-bold uppercase tracking-wider mb-3">
                    <i class="bi bi-people-fill"></i>
                    <span>Verified Craft Professionals</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight">
                    Find Skilled Pros in Tunisia
                </h1>
                <p class="text-sm sm:text-base text-slate-500 dark:text-slate-400 mt-2">
                    Filter by trade, hourly rate, client rating, real-time availability, and operating city.
                </p>
            </div>

            <!-- Main Search & Multi-Criteria Filter Form -->
            <form action="{{ route('providers.index') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                    <!-- Keyword / Name / Skill Input -->
                    <div class="md:col-span-4 relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="bi bi-search text-base"></i>
                        </div>
                        <input type="text" 
                               name="keyword" 
                               value="{{ request('keyword', request('q')) }}" 
                               placeholder="Provider name, skill, or trade (e.g. Youssef, AC, piping)..." 
                               class="w-full pl-10 pr-4 py-3 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>

                    <!-- Trade / Category Select -->
                    <div class="md:col-span-3 relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="bi bi-tools text-base"></i>
                        </div>
                        <select name="trade" class="w-full pl-10 pr-8 py-3 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none cursor-pointer transition">
                            <option value="">All Trades & Crafts</option>
                            @php
                                $currentTrade = request('trade', request('category'));
                            @endphp
                            @foreach($categories as $cat)
                                <option value="{{ $cat->name }}" {{ ($currentTrade == $cat->slug || $currentTrade == $cat->name) ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                            @if($categories->isEmpty())
                                <option value="Plumbing" {{ $currentTrade == 'Plumbing' ? 'selected' : '' }}>Plumbing</option>
                                <option value="Electricity" {{ $currentTrade == 'Electricity' ? 'selected' : '' }}>Electricity</option>
                                <option value="HVAC & Climate" {{ $currentTrade == 'HVAC & Climate' ? 'selected' : '' }}>HVAC & Climate</option>
                                <option value="Cleaning" {{ $currentTrade == 'Cleaning' ? 'selected' : '' }}>Cleaning</option>
                                <option value="Painting" {{ $currentTrade == 'Painting' ? 'selected' : '' }}>Painting</option>
                                <option value="Carpentry" {{ $currentTrade == 'Carpentry' ? 'selected' : '' }}>Carpentry</option>
                            @endif
                        </select>
                        <i class="bi bi-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none"></i>
                    </div>

                    <!-- Location / City Select -->
                    <div class="md:col-span-3 relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="bi bi-geo-alt text-base"></i>
                        </div>
                        <select name="location" class="w-full pl-10 pr-8 py-3 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none cursor-pointer transition">
                            <option value="">All Tunisia (Any City)</option>
                            @php
                                $currentLoc = request('location', request('city'));
                                $allCities = collect(['Tunis', 'Ariana', 'Ben Arous', 'Manouba', 'Sousse', 'Sfax', 'Bizerte', 'Nabeul', 'Monastir', 'Kairouan', 'Gabes'])
                                    ->merge($cities ?? [])
                                    ->unique()
                                    ->sort();
                            @endphp
                            @foreach($allCities as $c)
                                <option value="{{ $c }}" {{ $currentLoc == $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                        <i class="bi bi-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none"></i>
                    </div>

                    <!-- Submit Search Button -->
                    <div class="md:col-span-2">
                        <button type="submit" class="w-full h-full min-h-[48px] px-6 py-3 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-sm shadow-md shadow-blue-500/20 transition flex items-center justify-center gap-2">
                            <i class="bi bi-search"></i>
                            <span>Search</span>
                        </button>
                    </div>
                </div>

                <!-- Secondary Filter Bar: Price, Min Rating, Availability, Sort -->
                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex flex-wrap items-center justify-between gap-4 text-xs">
                    
                    <!-- Left: Criteria Filters -->
                    <div class="flex flex-wrap items-center gap-4">
                        
                        <!-- Rate Range (TND / hr) -->
                        <div class="flex items-center gap-1.5 bg-slate-50 dark:bg-slate-800/70 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700">
                            <span class="font-bold text-slate-500 dark:text-slate-400">Rate:</span>
                            <input type="number" 
                                   name="min_price" 
                                   value="{{ request('min_price', request('min_rate')) }}" 
                                   placeholder="Min" 
                                   min="0"
                                   step="5"
                                   class="w-16 px-2 py-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-center text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <span class="text-slate-400">-</span>
                            <input type="number" 
                                   name="max_price" 
                                   value="{{ request('max_price', request('max_rate')) }}" 
                                   placeholder="Max" 
                                   min="0"
                                   step="5"
                                   class="w-16 px-2 py-1 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-lg text-center text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <span class="text-slate-400 font-semibold">TND/hr</span>
                        </div>

                        <!-- Minimum Rating Filter -->
                        <div class="flex items-center gap-1.5 bg-slate-50 dark:bg-slate-800/70 p-1 rounded-xl border border-slate-200 dark:border-slate-700">
                            <span class="font-bold text-slate-500 dark:text-slate-400 pl-2">Rating:</span>
                            @php $minR = request('min_rating'); @endphp
                            <label class="cursor-pointer">
                                <input type="radio" name="min_rating" value="" class="hidden" onchange="this.form.submit()" {{ empty($minR) ? 'checked' : '' }}>
                                <span class="px-2.5 py-1 rounded-lg font-bold text-xs inline-block transition {{ empty($minR) ? 'bg-white dark:bg-slate-900 text-slate-900 dark:text-white shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900' }}">All</span>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="min_rating" value="4.0" class="hidden" onchange="this.form.submit()" {{ $minR == '4.0' || $minR == '4' ? 'checked' : '' }}>
                                <span class="px-2.5 py-1 rounded-lg font-bold text-xs inline-block transition {{ $minR == '4.0' || $minR == '4' ? 'bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300 shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900' }}">4.0+ ★</span>
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" name="min_rating" value="4.5" class="hidden" onchange="this.form.submit()" {{ $minR == '4.5' ? 'checked' : '' }}>
                                <span class="px-2.5 py-1 rounded-lg font-bold text-xs inline-block transition {{ $minR == '4.5' ? 'bg-amber-100 dark:bg-amber-950/80 text-amber-800 dark:text-amber-300 shadow-xs' : 'text-slate-600 dark:text-slate-400 hover:text-slate-900' }}">4.5+ ★</span>
                            </label>
                        </div>

                        <!-- Available Now Checkbox -->
                        <label class="flex items-center gap-2 cursor-pointer bg-slate-50 dark:bg-slate-800/70 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                            <input type="checkbox" 
                                   name="available_only" 
                                   value="1" 
                                   onchange="this.form.submit()"
                                   {{ request()->boolean('available_only') ? 'checked' : '' }}
                                   class="rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4">
                            <span class="flex items-center gap-1.5 font-bold text-slate-800 dark:text-slate-200">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                Available Now
                            </span>
                        </label>

                        <!-- Verified Pro Checkbox -->
                        <label class="flex items-center gap-2 cursor-pointer bg-slate-50 dark:bg-slate-800/70 px-3 py-2 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                            <input type="checkbox" 
                                   name="is_verified" 
                                   value="1" 
                                   onchange="this.form.submit()"
                                   {{ request()->boolean('is_verified') ? 'checked' : '' }}
                                   class="rounded text-blue-600 focus:ring-blue-500 w-4 h-4">
                            <span class="flex items-center gap-1.5 font-bold text-slate-800 dark:text-slate-200">
                                <i class="bi bi-patch-check-fill text-emerald-500"></i>
                                Verified Only
                            </span>
                        </label>
                    </div>

                    <!-- Right: Sorting & Clear Filters -->
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-2">
                            <span class="text-slate-500 dark:text-slate-400 font-bold">Sort:</span>
                            <select name="sort" onchange="this.form.submit()" class="px-3 py-1.5 rounded-xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 font-bold cursor-pointer focus:outline-none">
                                <option value="rating_desc" {{ request('sort') == 'rating_desc' ? 'selected' : '' }}>Top Rated</option>
                                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Lowest Rate</option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Highest Rate</option>
                                <option value="reviews_desc" {{ request('sort') == 'reviews_desc' ? 'selected' : '' }}>Most Reviews</option>
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest Specialists</option>
                            </select>
                        </div>

                        @if(request()->hasAny(['keyword', 'q', 'trade', 'category', 'location', 'city', 'min_price', 'max_price', 'min_rating', 'available_only', 'is_verified', 'sort']))
                            <a href="{{ route('providers.index') }}" class="px-3 py-1.5 rounded-xl text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 font-bold transition flex items-center gap-1">
                                <i class="bi bi-x-circle"></i> Clear
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Results Header Strip -->
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="space-y-1.5">
                <h2 class="text-lg font-black text-slate-900 dark:text-white tracking-tight">
                    {{ $providers->total() }} {{ $providers->total() === 1 ? 'Specialist' : 'Specialists' }} Found
                </h2>
                <div class="flex items-center gap-2 flex-wrap text-xs">
                    @if(request('keyword') || request('q'))
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-blue-100 dark:bg-blue-950/60 text-blue-800 dark:text-blue-300 font-medium">
                            Keyword: "{{ request('keyword', request('q')) }}"
                        </span>
                    @endif
                    @if(request('trade') || request('category'))
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-indigo-100 dark:bg-indigo-950/60 text-indigo-800 dark:text-indigo-300 font-medium">
                            Trade: {{ request('trade', request('category')) }}
                        </span>
                    @endif
                    @if(request('location') || request('city'))
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 font-medium">
                            <i class="bi bi-geo-alt-fill text-rose-500 text-[10px]"></i> {{ request('location', request('city')) }}
                        </span>
                    @endif
                    @if(request()->boolean('available_only'))
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 font-bold border border-emerald-300/60 dark:border-emerald-800/60 shadow-2xs">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Available Now Only
                        </span>
                    @endif
                    @if(request('min_rating'))
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 font-medium">
                            ★ {{ request('min_rating') }}+
                        </span>
                    @endif
                    @if(request('min_price') || request('max_price'))
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 font-medium">
                            {{ request('min_price', '0') }} - {{ request('max_price', '∞') }} TND/hr
                        </span>
                    @endif
                </div>
            </div>
            
            <a href="{{ route('services.index') }}" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                <span>Looking for specific service packages?</span>
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <!-- ======================================================== -->
        <!-- PROVIDER CARDS GRID                                      -->
        <!-- ======================================================== -->
        @if($providers->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($providers as $pro)
                    @php
                        $profile = $pro->providerProfile;
                        $rating = $pro->provider_reviews_avg_rating ? round($pro->provider_reviews_avg_rating, 1) : 5.0;
                        $reviewsCount = $pro->provider_reviews_count ?? $pro->providerReviews->count();
                        $completed = $pro->completed_jobs_count ?? $pro->providerBookings->where('status', 'completed')->count();
                        $skills = is_array($profile?->skills) ? $profile->skills : (is_string($profile?->skills) ? json_decode($profile->skills, true) : []);
                    @endphp

                    <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-sm hover:shadow-xl hover:border-blue-300 dark:hover:border-blue-900/60 transition-all duration-200 flex flex-col justify-between space-y-5 group">
                        
                        <div class="space-y-4">
                            <!-- Card Top: Avatar, Badges & Favorite Heart Toggle -->
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex items-center gap-3.5">
                                    <div class="relative">
                                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-amber-500 text-white font-black text-xl flex items-center justify-center shadow-md group-hover:scale-105 transition-transform shrink-0">
                                            {{ strtoupper(substr($pro->name, 0, 1)) }}
                                        </div>
                                        @if($profile?->is_available)
                                            <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-emerald-500 border-2 border-white dark:border-slate-900 ring-2 ring-emerald-200 dark:ring-emerald-950" title="Available for new jobs"></span>
                                        @endif
                                    </div>

                                    <div class="min-w-0">
                                        <a href="{{ route('providers.show', $pro->id) }}" class="font-black text-base text-slate-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition truncate block">
                                            {{ $pro->name }}
                                        </a>
                                        @if($pro->is_verified)
                                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 dark:text-emerald-400">
                                                <i class="bi bi-patch-check-fill text-emerald-600"></i>
                                                <span>Verified Pro</span>
                                            </span>
                                        @endif
                                        @if($profile?->business_name)
                                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium truncate">
                                                {{ $profile->business_name }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Favorite Heart Action Button -->
                                <button type="button" 
                                        onclick="toggleQuickFavorite({{ $pro->id }}, this)" 
                                        class="p-2 rounded-xl text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition shrink-0" 
                                        title="Bookmark Provider">
                                    <i class="bi bi-heart text-base"></i>
                                </button>
                            </div>

                            <!-- Trade & City Chips -->
                            <div class="flex items-center gap-2 flex-wrap text-xs">
                                <span class="px-2.5 py-1 rounded-xl bg-blue-50 dark:bg-blue-950/70 text-blue-700 dark:text-blue-300 font-extrabold border border-blue-100 dark:border-blue-900/40">
                                    <i class="bi bi-tools mr-1"></i>{{ $profile?->category ?? 'General Contractor' }}
                                </span>
                                <span class="px-2.5 py-1 rounded-xl bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-semibold border border-slate-200 dark:border-slate-700">
                                    <i class="bi bi-geo-alt-fill text-rose-500 mr-1"></i>{{ $profile?->city ?? 'Tunis' }}
                                </span>
                            </div>

                            <!-- Rating, Jobs, & Hourly Rate Strip -->
                            <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-100 dark:border-slate-800 text-xs">
                                <div>
                                    <span class="text-[11px] text-slate-400 font-semibold block">Client Rating</span>
                                    <div class="flex items-center gap-1 font-bold text-slate-900 dark:text-white mt-0.5">
                                        <i class="bi bi-star-fill text-amber-500"></i>
                                        <span>{{ number_format($rating, 1) }}</span>
                                        <span class="text-slate-400 font-normal">({{ $reviewsCount }})</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-[11px] text-slate-400 font-semibold block">Standard Rate</span>
                                    <div class="font-black text-slate-900 dark:text-white mt-0.5">
                                        @if($profile?->hourly_rate)
                                            {{ number_format($profile->hourly_rate, 0) }} <span class="text-[10px] text-blue-600 dark:text-blue-400">TND/hr</span>
                                        @else
                                            <span class="text-xs text-slate-500">Custom Quote</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Skills Tags -->
                            @if(!empty($skills))
                                <div class="pt-2 border-t border-slate-100 dark:border-slate-800/80">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Specializations</span>
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach(array_slice($skills, 0, 3) as $sk)
                                            <span class="px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-[11px] font-medium">
                                                {{ $sk }}
                                            </span>
                                        @endforeach
                                        @if(count($skills) > 3)
                                            <span class="px-1.5 py-0.5 rounded-lg text-slate-400 text-[10px] font-bold">
                                                +{{ count($skills) - 3 }} more
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Card Action Buttons -->
                        <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center gap-2">
                            <button type="button" 
                                    data-compare-id="{{ $pro->id }}"
                                    onclick="toggleCompareProvider({{ $pro->id }}, '{{ addslashes($pro->name) }}', '{{ strtoupper(substr($pro->name, 0, 1)) }}', '{{ addslashes($profile?->category ?? 'Provider') }}', '{{ $profile?->hourly_rate ? number_format($profile->hourly_rate, 0) . ' TND/hr' : '' }}')" 
                                    class="compare-card-btn px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-amber-400 dark:hover:border-amber-500 text-slate-700 dark:text-slate-300 font-bold text-xs transition flex items-center gap-1.5 shrink-0"
                                    title="Add to side-by-side comparison">
                                <i class="bi bi-layout-split compare-card-icon"></i>
                                <span class="compare-card-text">Compare</span>
                            </button>
                            <a href="{{ route('chat', ['provider_id' => $pro->id]) }}" 
                               class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-blue-500 hover:text-blue-600 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs transition flex items-center justify-center shrink-0"
                               title="Envoyer un message au prestataire">
                                <i class="bi bi-chat-dots-fill text-sm"></i>
                            </a>
                            <a href="{{ route('providers.show', $pro->id) }}" class="flex-1 py-2.5 text-center rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-extrabold text-xs transition">
                                View Profile
                            </a>
                            <a href="{{ route('providers.show', $pro->id) }}#booking" data-pro-card-hire="{{ $pro->id }}" class="pro-hire-btn px-4 py-2.5 text-center rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-sm transition">
                                Hire
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="pt-8">
                {{ $providers->links() }}
            </div>

        @else
            <!-- Empty State -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-12 text-center max-w-md mx-auto space-y-4 border border-slate-200 dark:border-slate-800 shadow-sm">
                <div class="w-16 h-16 rounded-3xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 flex items-center justify-center mx-auto text-3xl shadow-inner">
                    <i class="bi bi-person-x"></i>
                </div>
                <h3 class="text-lg font-black text-slate-900 dark:text-white">No Specialists Match Your Criteria</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    Try broadening your filters, choosing a different city, or removing the minimum rating/price constraints.
                </p>
                <div class="pt-2">
                    <a href="{{ route('providers.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition shadow-sm">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        <span>Reset All Filters</span>
                    </a>
                </div>
            </div>
        @endif

    </div>
</div>

<script>
    async function toggleQuickFavorite(providerId, btn) {
        const token = localStorage.getItem('khedma_token');
        if (!token) {
            if (confirm('Please sign in to save providers to your favorites list. Go to login?')) {
                window.location.href = '/login';
            }
            return;
        }

        const icon = btn.querySelector('i');
        const isFav = icon.classList.contains('bi-heart-fill');

        try {
            const method = isFav ? 'DELETE' : 'POST';
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            const res = await fetch(`/api/v1/client/favorites/${providerId}`, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || ''
                }
            });

            const data = await res.json();
            if (res.ok) {
                if (data.is_favorited) {
                    icon.className = 'bi bi-heart-fill text-rose-500 text-base scale-110';
                    btn.title = 'Remove from favorites';
                } else {
                    icon.className = 'bi bi-heart text-base';
                    btn.title = 'Save provider to favorites';
                }
            } else {
                alert(data.message || 'Action could not be performed.');
            }
        } catch(e) {
            console.error(e);
        }
    }

    function updateCardCompareButtons() {
        try {
            const user = JSON.parse(localStorage.getItem('khedma_user') || '{}');
            if (user && user.role === 'provider') {
                document.querySelectorAll('.compare-card-btn').forEach(btn => btn.style.display = 'none');
                return;
            }
        } catch(e) {}

        if (typeof getCompareList !== 'function') return;
        const list = getCompareList();
        const activeIds = list.map(p => parseInt(p.id, 10));

        document.querySelectorAll('[data-compare-id]').forEach(btn => {
            const id = parseInt(btn.getAttribute('data-compare-id'), 10);
            const isSelected = activeIds.includes(id);
            const icon = btn.querySelector('.compare-card-icon');
            const text = btn.querySelector('.compare-card-text');

            if (isSelected) {
                btn.className = 'compare-card-btn px-3 py-2.5 rounded-xl border border-amber-500 bg-amber-500 text-slate-950 font-black text-xs shadow-sm transition flex items-center gap-1.5 shrink-0';
                if (icon) icon.className = 'bi bi-check-circle-fill compare-card-icon text-slate-950';
                if (text) text.textContent = 'Comparing';
                btn.title = 'Remove from comparison';
            } else {
                btn.className = 'compare-card-btn px-3 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-amber-400 dark:hover:border-amber-500 text-slate-700 dark:text-slate-300 font-bold text-xs transition flex items-center gap-1.5 shrink-0';
                if (icon) icon.className = 'bi bi-layout-split compare-card-icon';
                if (text) text.textContent = 'Compare';
                btn.title = 'Add to side-by-side comparison';
            }
        });
    }

    document.addEventListener('DOMContentLoaded', () => {
        updateCardCompareButtons();

        try {
            const user = JSON.parse(localStorage.getItem('khedma_user') || '{}');
            if (user && user.role === 'provider') {
                document.querySelectorAll('[data-pro-card-hire]').forEach(btn => {
                    const id = parseInt(btn.getAttribute('data-pro-card-hire'), 10);
                    if (user.id === id) {
                        btn.outerHTML = `
                            <a href="/profile" class="px-3.5 py-2.5 text-center rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs shadow-sm transition">
                                Your Profile
                            </a>
                        `;
                    } else {
                        btn.classList.add('hidden');
                    }
                });
            }
        } catch(e) {}
    });
</script>
@endsection
