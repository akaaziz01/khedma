@extends('layouts.app')

@section('title', 'Find Verified Services & Providers | Khedma')

@section('content')
<div class="bg-slate-50 dark:bg-slate-950 min-h-screen py-10 transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Header Banner & Search Controls -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200 dark:border-slate-800">
            <div class="max-w-3xl mb-6">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 text-xs font-bold uppercase tracking-wider mb-3">
                    <i class="bi bi-search"></i>
                    <span>Find Verified Professionals</span>
                </div>
                <h1 class="text-3xl sm:text-4xl font-black text-slate-900 dark:text-white tracking-tight">
                    Search On-Demand Services
                </h1>
                <p class="text-sm sm:text-base text-slate-500 dark:text-slate-400 mt-2">
                    Browse trusted local providers across Tunisia. Filter by keyword, trade, city, and price.
                </p>
            </div>

            <!-- Main Search & Filter Form -->
            <form action="{{ route('services.index') }}" method="GET" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3">
                    <!-- Keyword / Service Name Input -->
                    <div class="md:col-span-4 relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="bi bi-search text-base"></i>
                        </div>
                        <input type="text" 
                               name="keyword" 
                               value="{{ request('keyword', request('q')) }}" 
                               placeholder="Service, skill, or provider (e.g. leak, electrician)..." 
                               class="w-full pl-10 pr-4 py-3 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-white text-sm placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>

                    <!-- Trade / Category Select -->
                    <div class="md:col-span-3 relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="bi bi-tools text-base"></i>
                        </div>
                        <select name="trade" class="w-full pl-10 pr-8 py-3 rounded-2xl bg-slate-50 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none cursor-pointer transition">
                            <option value="">All Trades & Categories</option>
                            @php
                                $currentTrade = request('trade', request('category', request('category_id')));
                            @endphp
                            @foreach($categories as $cat)
                                <option value="{{ $cat->slug }}" {{ ($currentTrade == $cat->slug || $currentTrade == $cat->name || $currentTrade == $cat->id) ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                            <!-- Standard Fallbacks if categories empty -->
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
                            @foreach($allCities as $cityOption)
                                <option value="{{ $cityOption }}" {{ strcasecmp($currentLoc ?? '', $cityOption) === 0 ? 'selected' : '' }}>
                                    {{ $cityOption }}
                                </option>
                            @endforeach
                        </select>
                        <i class="bi bi-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400 pointer-events-none"></i>
                    </div>

                    <!-- Action Buttons -->
                    <div class="md:col-span-2 flex gap-2">
                        <button type="submit" class="flex-1 py-3 px-4 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-sm shadow-md shadow-blue-500/20 transition flex items-center justify-center gap-2">
                            <i class="bi bi-search"></i>
                            <span>Search</span>
                        </button>
                        @if(request()->hasAny(['keyword', 'q', 'trade', 'category', 'location', 'city', 'min_price', 'max_price', 'sort']))
                            <a href="{{ route('services.index') }}" title="Reset All Filters" class="py-3 px-3.5 rounded-2xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 text-sm font-bold flex items-center justify-center transition">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Secondary Filter Row: Price and Sort -->
                <div class="pt-3 border-t border-slate-100 dark:border-slate-800/80 flex flex-wrap items-center justify-between gap-4 text-xs">
                    <div class="flex items-center gap-3 flex-wrap">
                        <span class="font-bold text-slate-600 dark:text-slate-400">Price (TND):</span>
                        <div class="flex items-center gap-1.5">
                            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Min" class="w-20 px-2.5 py-1.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs">
                            <span class="text-slate-400">—</span>
                            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Max" class="w-20 px-2.5 py-1.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs">
                        </div>

                        <!-- Availability filter for services -->
                        <label class="flex items-center gap-2 cursor-pointer bg-slate-50 dark:bg-slate-800 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                            <input type="checkbox" 
                                   name="available_only" 
                                   value="1" 
                                   onchange="this.form.submit()"
                                   {{ request()->boolean('available_only') ? 'checked' : '' }}
                                   class="rounded text-emerald-600 focus:ring-emerald-500 w-3.5 h-3.5">
                            <span class="flex items-center gap-1.5 font-bold text-slate-700 dark:text-slate-200">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                Available Pro Now
                            </span>
                        </label>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="font-bold text-slate-600 dark:text-slate-400">Sort by:</span>
                        <select name="sort" onchange="this.form.submit()" class="px-3 py-1.5 rounded-xl bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-200 font-semibold cursor-pointer">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest Listed</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="duration_asc" {{ request('sort') == 'duration_asc' ? 'selected' : '' }}>Fastest Duration</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <!-- Filter Tags & Results Counter -->
        <div class="flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-2 flex-wrap text-xs">
                <span class="font-bold text-slate-700 dark:text-slate-300">
                    Showing <strong class="text-blue-600 dark:text-blue-400 font-black">{{ $services->total() }}</strong> verified services
                </span>
                
                @if(request('keyword') || request('q'))
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-blue-100 dark:bg-blue-950/60 text-blue-800 dark:text-blue-300 font-medium">
                        Keyword: "{{ request('keyword', request('q')) }}"
                    </span>
                @endif
                @if(request('trade') || request('category'))
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-indigo-100 dark:bg-indigo-950/60 text-indigo-800 dark:text-indigo-300 font-medium">
                        Trade: {{ request('trade', request('category')) }}
                    </span>
                @endif
                @if(request('location') || request('city'))
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 font-medium">
                        <i class="bi bi-geo-alt-fill text-xs"></i>
                        {{ request('location', request('city')) }}
                    </span>
                @endif
                @if(request()->boolean('available_only'))
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 font-medium">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Available Now
                    </span>
                @endif
            </div>

            @if(request()->hasAny(['keyword', 'q', 'trade', 'category', 'location', 'city', 'min_price', 'max_price', 'available_only']))
                <a href="{{ route('services.index') }}" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
                    Clear all filters
                </a>
            @endif
        </div>

        <!-- Results Grid -->
        @if($services->isEmpty())
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-12 text-center border border-slate-200 dark:border-slate-800 space-y-4 max-w-xl mx-auto shadow-sm">
                <div class="w-16 h-16 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center mx-auto text-3xl">
                    <i class="bi bi-search"></i>
                </div>
                <h3 class="text-xl font-black text-slate-900 dark:text-white">No services found</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                    We could not find any active verified services matching your search parameters. Try widening your filters or searching another Tunisian city.
                </p>
                <div class="pt-2">
                    <a href="{{ route('services.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md transition">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        <span>Reset All Filters</span>
                    </a>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($services as $service)
                    @php
                        $provider = $service->provider;
                        $profile = $provider?->providerProfile;
                    @endphp
                    <div class="group bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 hover:border-blue-400 dark:hover:border-blue-500 shadow-md shadow-slate-200/40 dark:shadow-none hover:shadow-xl transition-all duration-200 flex flex-col justify-between overflow-hidden">
                        <div class="p-6 space-y-4">
                            <!-- Card Top Bar: Category & Rate -->
                            <div class="flex items-start justify-between gap-2">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-blue-50 dark:bg-blue-950/70 text-blue-700 dark:text-blue-300 border border-blue-100 dark:border-blue-900/40">
                                    <i class="bi {{ $service->serviceCategory?->icon ?? 'bi-tools' }}"></i>
                                    <span>{{ $service->serviceCategory?->name ?? $service->category ?? 'General' }}</span>
                                </span>
                                
                                <div class="text-right">
                                    <span class="text-xl font-black text-slate-900 dark:text-white">
                                        {{ number_format($service->price, 2) }} <span class="text-xs font-extrabold text-blue-600 dark:text-blue-400">TND</span>
                                    </span>
                                </div>
                            </div>

                            <!-- Service Title & Description -->
                            <div>
                                <h2 class="text-lg font-black text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition leading-snug">
                                    <a href="{{ route('services.show', $service->id) }}">
                                        {{ $service->title }}
                                    </a>
                                </h2>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 line-clamp-3 leading-relaxed">
                                    {{ $service->description }}
                                </p>
                            </div>

                            <!-- Duration & Attributes -->
                            <div class="flex items-center gap-3 text-xs text-slate-500 dark:text-slate-400 pt-1">
                                <span class="inline-flex items-center gap-1">
                                    <i class="bi bi-clock text-blue-500"></i>
                                    <span>{{ $service->duration_hours }} hr{{ $service->duration_hours > 1 ? 's' : '' }}</span>
                                </span>
                                <span class="w-1 h-1 rounded-full bg-slate-300 dark:bg-slate-700"></span>
                                <span class="inline-flex items-center gap-1">
                                    <i class="bi bi-shield-check text-emerald-500"></i>
                                    <span>Approved</span>
                                </span>
                            </div>

                            <!-- Provider Profile Info Box -->
                            <div class="pt-4 border-t border-slate-100 dark:border-slate-800/80 flex items-center justify-between gap-3">
                                <a href="{{ $provider ? route('providers.show', $provider->id) : '#' }}" class="group/pro flex items-center gap-3 min-w-0">
                                    <!-- Avatar -->
                                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-extrabold text-sm flex items-center justify-center shrink-0 shadow-xs group-hover/pro:scale-105 transition-transform">
                                        {{ strtoupper(substr($provider?->name ?? 'P', 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <span class="text-xs font-extrabold text-slate-900 dark:text-white truncate group-hover/pro:text-blue-600 transition">
                                                {{ $provider?->name ?? 'Service Provider' }}
                                            </span>
                                            @if($provider?->is_verified)
                                                <span class="inline-flex items-center gap-0.5 px-1.5 py-0.2 rounded-full text-[9px] font-black bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300 border border-emerald-300/60 dark:border-emerald-700/60" title="Verified Professional Account">
                                                    <i class="bi bi-patch-check-fill text-emerald-600"></i>
                                                    Verified
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-1.5 text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                            @if($profile?->city)
                                                <span class="inline-flex items-center gap-0.5 text-slate-600 dark:text-slate-300 font-semibold">
                                                    <i class="bi bi-geo-alt-fill text-rose-500 text-[10px]"></i>
                                                    {{ $profile->city }}
                                                </span>
                                            @else
                                                <span class="text-slate-400">Tunisia</span>
                                            @endif
                                            @if($profile?->business_name)
                                                <span class="text-slate-400">•</span>
                                                <span class="truncate text-slate-400">{{ $profile->business_name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </a>

                                @if($profile?->is_available)
                                    <span class="shrink-0 w-2 h-2 rounded-full bg-emerald-500 ring-4 ring-emerald-100 dark:ring-emerald-950" title="Available for Bookings"></span>
                                @endif
                            </div>
                        </div>

                        <!-- Card Footer / Actions -->
                        <div class="p-4 bg-slate-50/70 dark:bg-slate-800/40 border-t border-slate-100 dark:border-slate-800 flex items-center gap-2">
                            <a href="{{ route('services.show', $service->id) }}" class="flex-1 py-2 px-3 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-200 text-xs font-bold text-center transition">
                                Details
                            </a>
                            <button type="button" 
                                    data-service-book-btn="{{ $service->id }}"
                                    data-provider-id="{{ $service->provider_id }}"
                                    onclick="openBookingModal('{{ $service->id }}', '{{ addslashes($service->title) }}', '{{ number_format($service->price, 2) }}', '{{ addslashes($provider?->name ?? 'Provider') }}', {{ $provider?->is_verified ? 'true' : 'false' }}, {{ (int) $service->provider_id }})" 
                                    class="service-book-cta flex-1 py-2 px-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-extrabold text-center shadow-xs transition flex items-center justify-center gap-1">
                                <span>Book Now</span>
                                <i class="bi bi-calendar-plus text-xs"></i>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination Links -->
            <div class="pt-6">
                {{ $services->links() }}
            </div>
        @endif

    </div>
</div>

<!-- ======================================================== -->
<!-- GUEST BOOKING MODAL                                      -->
<!-- ======================================================== -->
<div id="guestBookingModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-6 transform transition-all">
        <!-- Modal Header -->
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-blue-100 dark:bg-blue-950/70 text-blue-600 dark:text-blue-400 flex items-center justify-center text-2xl shrink-0">
                    <i class="bi bi-calendar-check-fill"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white leading-tight">Book This Service</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Direct booking with provider</p>
                </div>
            </div>
            <button type="button" onclick="closeBookingModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
        </div>

        <!-- Service Selected Card -->
        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60 space-y-2">
            <div class="flex items-start justify-between gap-2">
                <span id="modalServiceTitle" class="font-extrabold text-slate-900 dark:text-white text-sm">Service Title</span>
                <span id="modalServicePrice" class="font-black text-blue-600 dark:text-blue-400 text-sm whitespace-nowrap">0.00 TND</span>
            </div>
            <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400">
                <i class="bi bi-person-fill text-slate-400"></i>
                <span>Offered by: <strong id="modalProviderName" class="text-slate-800 dark:text-slate-200 font-bold">Provider</strong></span>
                <span id="modalVerifiedBadge" class="hidden inline-flex items-center gap-0.5 text-[10px] font-bold text-emerald-600 dark:text-emerald-400">
                    <i class="bi bi-patch-check-fill"></i> Verified
                </span>
            </div>
        </div>

        <!-- Guest Notice -->
        <div class="space-y-3 text-center sm:text-left">
            <h4 class="text-sm font-extrabold text-slate-900 dark:text-white">
                Ready to schedule your appointment?
            </h4>
            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                Sign in or register a free client account to book instantly, communicate securely with the provider, and track your request.
            </p>
        </div>

        <!-- Actions -->
        <div class="space-y-2 pt-2">
            <a id="modalSignInLink" href="/login" class="w-full py-3 px-4 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs text-center shadow-md shadow-blue-500/20 transition block">
                Sign In to Complete Booking
            </a>
            <a href="/register?role=client" class="w-full py-3 px-4 rounded-2xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold text-xs text-center transition block">
                New to Khedma? Create Client Account
            </a>
        </div>
    </div>
</div>

<script>
    function openBookingModal(serviceId, title, price, providerName, isVerified, providerId) {
        try {
            const user = JSON.parse(localStorage.getItem('khedma_user') || '{}');
            if (providerId && user.id === parseInt(providerId, 10)) {
                alert('As a provider, you cannot book your own service.');
                return;
            }
            if (user.role === 'provider') {
                alert('You are logged in as a Provider. To book services, please use a Client account.');
                return;
            }
        } catch(e) {}

        document.getElementById('modalServiceTitle').textContent = title;
        document.getElementById('modalServicePrice').textContent = price + ' TND';
        document.getElementById('modalProviderName').textContent = providerName;
        
        const verifiedBadge = document.getElementById('modalVerifiedBadge');
        if (isVerified) {
            verifiedBadge.classList.remove('hidden');
        } else {
            verifiedBadge.classList.add('hidden');
        }

        document.getElementById('modalSignInLink').href = '/login?redirect=' + encodeURIComponent('/services/' + serviceId);
        document.getElementById('guestBookingModal').classList.remove('hidden');
    }

    function closeBookingModal() {
        document.getElementById('guestBookingModal').classList.add('hidden');
    }

    // Close on backdrop click
    document.getElementById('guestBookingModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeBookingModal();
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        try {
            const user = JSON.parse(localStorage.getItem('khedma_user') || '{}');
            if (user.id) {
                document.querySelectorAll('[data-service-book-btn]').forEach(btn => {
                    const proId = parseInt(btn.getAttribute('data-provider-id'), 10);
                    if (user.id === proId) {
                        btn.outerHTML = `
                            <a href="/provider/services" class="flex-1 py-2 px-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-black text-center shadow-xs transition flex items-center justify-center gap-1">
                                <span>Your Service</span>
                            </a>
                        `;
                    }
                });
            }
        } catch(e) {}
    });
</script>
@endsection
