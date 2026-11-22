@extends('layouts.app')

@section('title', ($provider->name ?? 'Provider Profile') . ' - Verified Professional | Khedma')

@section('content')
@php
    $profile = $provider->providerProfile;
    $skills = $profile?->skills ?? [];
    $portfolio = $profile?->portfolio_photos ?? [];
@endphp

<div class="bg-slate-50 dark:bg-slate-950 min-h-screen py-10 transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
            <a href="/" class="hover:text-blue-600 dark:hover:text-blue-400">Home</a>
            <i class="bi bi-chevron-right text-[10px]"></i>
            <a href="{{ route('services.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400">Services</a>
            <i class="bi bi-chevron-right text-[10px]"></i>
            <span class="text-slate-900 dark:text-white truncate max-w-xs">{{ $provider->name }}</span>
        </nav>

        <!-- ======================================================== -->
        <!-- PROVIDER HERO HEADER BANNER                              -->
        <!-- ======================================================== -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200 dark:border-slate-800 relative overflow-hidden">
            <!-- Decorative Background Accent -->
            <div class="absolute -right-16 -top-16 w-64 h-64 bg-gradient-to-br from-blue-500/10 via-indigo-500/10 to-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 relative z-10">
                <div class="flex items-start sm:items-center gap-5 flex-col sm:flex-row">
                    <!-- Provider Avatar -->
                    <div class="relative">
                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-3xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-amber-500 text-white font-black text-3xl sm:text-4xl flex items-center justify-center shadow-lg shadow-blue-500/20 shrink-0">
                            {{ strtoupper(substr($provider->name, 0, 1)) }}
                        </div>
                        @if($profile?->is_available)
                            <span class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-emerald-500 border-2 border-white dark:border-slate-900 ring-2 ring-emerald-200 dark:ring-emerald-950" title="Available for new bookings"></span>
                        @endif
                    </div>

                    <!-- Provider Info -->
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                                {{ $provider->name }}
                            </h1>
                            @if($provider->is_verified)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-black bg-emerald-100 dark:bg-emerald-950/80 text-emerald-800 dark:text-emerald-300 border border-emerald-300/60 dark:border-emerald-700/60 shadow-xs" title="Identity & Craft Verified by Khedma">
                                    <i class="bi bi-patch-check-fill text-emerald-600 dark:text-emerald-400"></i>
                                    <span>Verified Pro</span>
                                </span>
                            @endif
                        </div>

                        @if($profile?->business_name)
                            <p class="text-sm font-bold text-slate-600 dark:text-slate-300">
                                {{ $profile->business_name }}
                            </p>
                        @endif

                        <!-- Meta Chips: Trade, Location, Rating, Joined -->
                        <div class="flex items-center gap-3 flex-wrap text-xs text-slate-500 dark:text-slate-400 pt-1">
                            <!-- Trade -->
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 font-extrabold border border-blue-100 dark:border-blue-900/40">
                                <i class="bi bi-tools text-blue-500"></i>
                                <span>{{ $profile?->category ?? 'General Contractor' }}</span>
                            </span>

                            <!-- City / Location -->
                            @if($profile?->city)
                                <span class="inline-flex items-center gap-1 font-semibold text-slate-700 dark:text-slate-300">
                                    <i class="bi bi-geo-alt-fill text-rose-500"></i>
                                    <span>{{ $profile->city }}, Tunisia</span>
                                </span>
                            @endif

                            <!-- Rating Score -->
                            <div class="inline-flex items-center gap-1.5 font-bold text-slate-900 dark:text-white bg-amber-50 dark:bg-amber-950/50 px-2.5 py-1 rounded-xl border border-amber-200/60 dark:border-amber-800/40">
                                <i class="bi bi-star-fill text-amber-500"></i>
                                <span>{{ number_format($averageRating, 1) }}</span>
                                <span class="text-slate-400 font-normal">({{ $reviewsCount }} {{ $reviewsCount === 1 ? 'review' : 'reviews' }})</span>
                            </div>

                            <!-- Member Since -->
                            <span class="inline-flex items-center gap-1">
                                <i class="bi bi-calendar-check text-slate-400"></i>
                                <span>Joined {{ $provider->created_at?->format('M Y') }}</span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Rate & Direct CTA in Hero -->
                <div class="flex flex-col sm:items-end gap-2 shrink-0 w-full sm:w-auto border-t sm:border-t-0 pt-4 sm:pt-0 border-slate-100 dark:border-slate-800">
                    <div class="text-left sm:text-right">
                        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider block">Standard Rate</span>
                        <div class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">
                            @if($profile?->hourly_rate)
                                {{ number_format($profile->hourly_rate, 2) }} <span class="text-sm font-extrabold text-blue-600 dark:text-blue-400">TND / hr</span>
                            @else
                                <span class="text-lg font-bold text-slate-600 dark:text-slate-300">Custom Quote</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <button type="button" 
                                id="heroBookingBtn"
                                onclick="openGuestBookingModal('{{ addslashes($provider->name) }}', '{{ $profile?->hourly_rate ? number_format($profile->hourly_rate, 2) . ' TND/hr' : 'Custom Quote' }}', {{ $provider->is_verified ? 'true' : 'false' }})" 
                                class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-md shadow-blue-500/20 transition flex items-center justify-center gap-2">
                            <i class="bi bi-calendar-plus"></i>
                            <span>Request Booking</span>
                        </button>

                        <!-- Provider Owner Actions (Shown when viewing own profile) -->
                        <a href="/profile" id="ownerHeroEditBtn" class="hidden w-full sm:w-auto px-5 py-3 rounded-2xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs transition flex items-center justify-center gap-1.5 shadow-md">
                            <i class="bi bi-pencil-square"></i>
                            <span>Edit Your Profile</span>
                        </a>
                        <a href="/provider/services" id="ownerHeroServicesBtn" class="hidden w-full sm:w-auto px-4 py-3 rounded-2xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 font-bold text-xs hover:bg-slate-200 dark:hover:bg-slate-700 transition flex items-center justify-center gap-1.5 border border-slate-200 dark:border-slate-700">
                            <i class="bi bi-tools"></i>
                            <span>Manage Services</span>
                        </a>

                        <a href="{{ route('providers.compare', ['ids' => $provider->id, 'category' => $profile?->category]) }}" 
                           id="profileCompareBtn"
                           class="px-4 py-3 rounded-2xl border border-amber-200 dark:border-amber-900/60 bg-amber-50 dark:bg-amber-950/40 hover:bg-amber-100 dark:hover:bg-amber-900/60 text-amber-900 dark:text-amber-300 font-extrabold text-xs shadow-sm transition flex items-center justify-center gap-1.5 shrink-0"
                           title="Compare {{ $provider->name }} with other {{ $profile?->category ?? 'service' }} professionals">
                            <i class="bi bi-layout-split text-sm"></i>
                            <span class="hidden sm:inline">Compare</span>
                        </a>
                        <button type="button"
                                id="favoriteBtn"
                                onclick="toggleFavorite({{ $provider->id }})"
                                class="px-4 py-3 rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700/80 text-slate-700 dark:text-slate-200 font-extrabold text-xs shadow-sm transition flex items-center justify-center gap-2 shrink-0"
                                title="{{ ($isFavorited ?? false) ? 'Remove from favorites' : 'Save provider to favorites' }}">
                            <i id="favoriteIcon" class="bi {{ ($isFavorited ?? false) ? 'bi-heart-fill text-rose-500' : 'bi-heart' }} text-sm transition-transform duration-200"></i>
                            <span id="favoriteLabel" class="hidden sm:inline">{{ ($isFavorited ?? false) ? 'Saved' : 'Save' }}</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Quick Metrics Strip -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-slate-100 dark:border-slate-800">
                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-700/50">
                    <span class="text-[11px] font-bold text-slate-400 block uppercase">Completed Jobs</span>
                    <span class="text-xl font-black text-slate-900 dark:text-white mt-0.5 block">{{ $completedJobs }}</span>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-700/50">
                    <span class="text-[11px] font-bold text-slate-400 block uppercase">Client Satisfaction</span>
                    <span class="text-xl font-black text-amber-500 mt-0.5 block">{{ number_format($averageRating, 1) }} / 5.0</span>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-700/50">
                    <span class="text-[11px] font-bold text-slate-400 block uppercase">Active Services</span>
                    <span class="text-xl font-black text-slate-900 dark:text-white mt-0.5 block">{{ $provider->services->count() }} listed</span>
                </div>
                <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/60 dark:border-slate-700/50">
                    <span class="text-[11px] font-bold text-slate-400 block uppercase">Availability</span>
                    <span class="text-sm font-extrabold {{ $profile?->is_available ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400' }} mt-1 block">
                        {{ $profile?->is_available ? 'Available Now' : 'By Appointment' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- MAIN CONTENT LAYOUT: 2-COLUMN (MAIN CONTENT + SIDEBAR)  -->
        <!-- ======================================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- LEFT 2 COLUMNS: ABOUT, SKILLS, SERVICES, PORTFOLIO, REVIEWS -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- 1. ABOUT & BIO -->
                <section class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-slate-800 shadow-sm space-y-4">
                    <div class="flex items-center gap-2">
                        <i class="bi bi-person-lines-fill text-blue-600 text-lg"></i>
                        <h2 class="text-lg font-black text-slate-900 dark:text-white">About the Professional</h2>
                    </div>
                    
                    @if($profile?->bio)
                        <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-line">
                            {{ $profile->bio }}
                        </p>
                    @else
                        <p class="text-sm text-slate-500 dark:text-slate-400 italic">
                            {{ $provider->name }} is an experienced {{ $profile?->category ?? 'professional' }} providing trusted services for homes and businesses across {{ $profile?->city ?? 'Tunisia' }}.
                        </p>
                    @endif

                    <!-- Skills & Specializations -->
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-2">
                        <span class="text-xs font-extrabold text-slate-400 uppercase tracking-wider block">
                            Skills & Trade Expertise
                        </span>
                        @if(!empty($skills) && is_array($skills))
                            <div class="flex flex-wrap gap-2 pt-1">
                                @foreach($skills as $skill)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 text-xs font-bold border border-slate-200/60 dark:border-slate-700/60">
                                        <i class="bi bi-check2 text-emerald-500 font-black"></i>
                                        <span>{{ $skill }}</span>
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <div class="flex flex-wrap gap-2 pt-1">
                                <span class="px-3 py-1 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-semibold">
                                    {{ $profile?->category ?? 'General Trade' }}
                                </span>
                                <span class="px-3 py-1 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-semibold">
                                    On-Demand Repairs
                                </span>
                                <span class="px-3 py-1 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 text-xs font-semibold">
                                    Installation & Maintenance
                                </span>
                            </div>
                        @endif
                    </div>
                </section>

                <!-- 2. OFFERED SERVICES CATALOG -->
                <section class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-slate-800 shadow-sm space-y-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-tools text-blue-600 text-lg"></i>
                            <h2 class="text-lg font-black text-slate-900 dark:text-white">Available Services</h2>
                        </div>
                        <span class="text-xs font-bold text-slate-400">
                            {{ $provider->services->count() }} active {{ $provider->services->count() === 1 ? 'service' : 'services' }}
                        </span>
                    </div>

                    @if($provider->services->isEmpty())
                        <div class="p-8 rounded-2xl bg-slate-50 dark:bg-slate-800/50 text-center border border-slate-200/60 dark:border-slate-700/50 space-y-2">
                            <i class="bi bi-inbox text-3xl text-slate-400"></i>
                            <p class="text-sm font-bold text-slate-700 dark:text-slate-300">No standalone package listed yet</p>
                            <p class="text-xs text-slate-400">This provider accepts custom bookings and hourly-rate requests.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($provider->services as $service)
                                <div class="p-5 rounded-2xl bg-slate-50/80 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 hover:border-blue-400 dark:hover:border-blue-500 transition-all flex flex-col justify-between space-y-4">
                                    <div class="space-y-2">
                                        <div class="flex items-start justify-between gap-2">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100/80 dark:bg-blue-950/70 text-blue-700 dark:text-blue-300">
                                                {{ $service->serviceCategory?->name ?? $service->category ?? 'Service' }}
                                            </span>
                                            <span class="text-base font-black text-slate-900 dark:text-white">
                                                {{ number_format($service->price, 2) }} <span class="text-xs font-bold text-blue-600">TND</span>
                                            </span>
                                        </div>

                                        <h3 class="font-bold text-slate-900 dark:text-white text-sm">
                                            <a href="{{ route('services.show', $service->id) }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition">
                                                {{ $service->title }}
                                            </a>
                                        </h3>

                                        <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">
                                            {{ $service->description }}
                                        </p>
                                    </div>

                                    <div class="pt-2 border-t border-slate-200/60 dark:border-slate-700/50 flex items-center justify-between text-xs">
                                        <span class="text-slate-400 flex items-center gap-1 text-[11px]">
                                            <i class="bi bi-clock"></i>
                                            <span>{{ $service->duration_hours }} hr{{ $service->duration_hours > 1 ? 's' : '' }}</span>
                                        </span>

                                        <div class="flex items-center gap-1.5">
                                            <a href="{{ route('services.show', $service->id) }}" class="px-2.5 py-1.5 rounded-lg bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 text-xs font-bold hover:bg-slate-100 transition">
                                                View
                                            </a>
                                            <button type="button" 
                                                    data-service-book-btn
                                                    onclick="openGuestBookingModal('{{ addslashes($provider->name) }}', '{{ number_format($service->price, 2) }} TND ({{ addslashes($service->title) }})', {{ $provider->is_verified ? 'true' : 'false' }}, {{ (int) $service->id }}, '{{ addslashes($service->title) }}', {{ (float) $service->price }}, '{{ addslashes($service->category ?? '') }}')" 
                                                    class="pro-service-book-btn px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-extrabold transition">
                                                Book
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>

                <!-- 3. PORTFOLIO PHOTOS & PAST PROJECTS GALLERY -->
                <section class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-slate-800 shadow-sm space-y-6">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-images text-blue-600 text-lg"></i>
                            <h2 class="text-lg font-black text-slate-900 dark:text-white">Portfolio & Past Projects</h2>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-xs font-bold text-slate-400">
                                {{ count($portfolio) }} {{ count($portfolio) === 1 ? 'photo' : 'photos' }}
                            </span>
                            <a href="/profile" id="ownerPortfolioUploadBtn" style="display: none;" class="px-3 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-black transition flex items-center gap-1 shadow-xs">
                                <i class="bi bi-cloud-arrow-up-fill text-xs"></i>
                                <span>Upload Photos</span>
                            </a>
                        </div>
                    </div>

                    @if(empty($portfolio))
                        <!-- Empty Portfolio State with Craft Quality Assurance Notice -->
                        <div class="p-8 rounded-2xl bg-slate-50 dark:bg-slate-800/50 text-center border border-slate-200/60 dark:border-slate-700/50 space-y-3">
                            <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center mx-auto text-xl">
                                <i class="bi bi-camera"></i>
                            </div>
                            <p class="text-sm font-bold text-slate-700 dark:text-slate-300">No project photos uploaded yet</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-md mx-auto">
                                {{ $provider->name }} is verified on the Khedma platform. You can check the customer reviews below to see verified client feedback.
                            </p>
                            <div id="ownerEmptyPortfolioNotice" style="display: none;" class="pt-2">
                                <a href="/profile" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs transition shadow-sm">
                                    <i class="bi bi-cloud-arrow-up-fill"></i>
                                    <span>Upload Photos of Your Work</span>
                                </a>
                            </div>
                        </div>
                    @else
                        <!-- Gallery Grid -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            @foreach($portfolio as $index => $photo)
                                @php
                                    $photoUrl = is_array($photo) ? ($photo['url'] ?? '') : $photo;
                                    $photoTitle = is_array($photo) ? ($photo['title'] ?? 'Completed Project') : 'Completed Work';
                                    $photoCaption = is_array($photo) ? ($photo['caption'] ?? '') : '';
                                @endphp
                                <div class="group relative rounded-2xl overflow-hidden aspect-square bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 cursor-pointer shadow-xs hover:shadow-lg transition-all"
                                     onclick="openLightbox('{{ addslashes($photoUrl) }}', '{{ addslashes($photoTitle) }}', '{{ addslashes($photoCaption) }}')">
                                    <img src="{{ $photoUrl }}" 
                                         alt="{{ $photoTitle }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                                         onerror="this.src='https://placehold.co/600x600/3b82f6/white?text=' + encodeURIComponent('{{ addslashes($photoTitle) }}')">
                                    
                                    <!-- Overlay Info on Hover -->
                                    <div class="absolute inset-0 bg-gradient-to-t from-slate-950/80 via-slate-950/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity p-3 flex flex-col justify-end text-white">
                                        <span class="text-xs font-bold leading-tight truncate">{{ $photoTitle }}</span>
                                        @if($photoCaption)
                                            <span class="text-[10px] text-slate-300 truncate mt-0.5">{{ $photoCaption }}</span>
                                        @endif
                                        <span class="text-[9px] text-blue-300 flex items-center gap-1 mt-1 font-semibold">
                                            <i class="bi bi-zoom-in"></i> Click to enlarge
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>

                <!-- 4. CUSTOMER REVIEWS & RATINGS -->
                <section class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-slate-800 shadow-sm space-y-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-star-fill text-amber-500 text-lg"></i>
                            <h2 class="text-lg font-black text-slate-900 dark:text-white">Client Reviews & Ratings</h2>
                        </div>
                        <span class="text-xs font-bold text-slate-400">
                            {{ $reviewsCount }} {{ $reviewsCount === 1 ? 'review' : 'reviews' }}
                        </span>
                    </div>

                    <!-- Rating Scorecard Banner -->
                    <div class="p-6 rounded-2xl bg-gradient-to-br from-amber-50/70 via-white to-orange-50/50 dark:from-slate-800/80 dark:via-slate-800/50 dark:to-slate-800/20 border border-amber-200/60 dark:border-slate-700 flex flex-col sm:flex-row items-center gap-6">
                        <!-- Left: Big Number -->
                        <div class="text-center sm:text-left shrink-0">
                            <span class="text-4xl sm:text-5xl font-black text-slate-900 dark:text-white">
                                {{ number_format($averageRating, 1) }}
                            </span>
                            <div class="flex items-center justify-center sm:justify-start gap-1 text-amber-500 text-sm mt-1">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= round($averageRating))
                                        <i class="bi bi-star-fill"></i>
                                    @else
                                        <i class="bi bi-star text-slate-300 dark:text-slate-600"></i>
                                    @endif
                                @endfor
                            </div>
                            <span class="text-xs font-bold text-slate-400 mt-1 block">
                                Based on {{ $reviewsCount }} {{ $reviewsCount === 1 ? 'verified review' : 'verified reviews' }}
                            </span>
                        </div>

                        <!-- Right: Star Breakdown Bars -->
                        <div class="flex-1 w-full space-y-1.5 text-xs">
                            @for($star = 5; $star >= 1; $star--)
                                @php
                                    $count = $ratingBreakdown[$star] ?? 0;
                                    $percent = $reviewsCount > 0 ? round(($count / $reviewsCount) * 100) : 0;
                                @endphp
                                <div class="flex items-center gap-2">
                                    <span class="w-12 text-slate-500 font-semibold flex items-center gap-0.5 shrink-0">
                                        <span>{{ $star }}</span>
                                        <i class="bi bi-star-fill text-[10px] text-amber-400"></i>
                                    </span>
                                    <div class="flex-1 h-2 rounded-full bg-slate-200 dark:bg-slate-700 overflow-hidden">
                                        <div class="h-full bg-amber-400 rounded-full" style="width: {{ $percent }}%"></div>
                                    </div>
                                    <span class="w-8 text-right text-slate-400 font-mono text-[11px]">{{ $count }}</span>
                                </div>
                            @endfor
                        </div>
                    </div>

                    <!-- Reviews List -->
                    @if($reviews->isEmpty())
                        <div class="p-8 rounded-2xl bg-slate-50 dark:bg-slate-800/50 text-center border border-slate-200/60 dark:border-slate-700/50 space-y-2">
                            <i class="bi bi-chat-heart text-3xl text-slate-400"></i>
                            <p class="text-sm font-bold text-slate-700 dark:text-slate-300">No reviews yet</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto">
                                Be one of the first clients to book {{ $provider->name }} and leave a verified review!
                            </p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($reviews as $rev)
                                <div class="p-5 rounded-2xl bg-slate-50/70 dark:bg-slate-800/50 border border-slate-200/80 dark:border-slate-800 space-y-2.5">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-xs flex items-center justify-center">
                                                {{ strtoupper(substr($rev->client?->name ?? 'C', 0, 1)) }}
                                            </div>
                                            <div>
                                                <span class="font-extrabold text-slate-900 dark:text-white text-xs block">
                                                    {{ $rev->client?->name ?? 'Verified Client' }}
                                                </span>
                                                <span class="text-[10px] text-slate-400">
                                                    {{ $rev->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-0.5 text-amber-500 text-xs">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $rev->rating)
                                                    <i class="bi bi-star-fill"></i>
                                                @else
                                                    <i class="bi bi-star text-slate-300 dark:text-slate-600"></i>
                                                @endif
                                            @endfor
                                        </div>
                                    </div>

                                    @if($rev->comment)
                                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed pl-10">
                                            "{{ $rev->comment }}"
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </section>

            </div>

            <!-- RIGHT 1 COLUMN: STICKY HIRE / BOOKING SIDEBAR CARD -->
            <div class="space-y-6">
                
                <!-- Provider Owner Sidebar View (When viewing own profile) -->
                <div id="ownerProfileSidebar" class="hidden sticky top-24 bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-7 border-2 border-amber-500/30 dark:border-amber-500/20 shadow-xl space-y-6">
                    <div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 text-xs font-black uppercase tracking-wider">
                            <i class="bi bi-shield-check"></i>
                            <span>Your Public Profile</span>
                        </span>
                        <h3 class="text-xl font-black text-slate-900 dark:text-white mt-2">
                            {{ $provider->name }} (You)
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            This preview displays exactly how clients see your profile, rates, and reviews.
                        </p>
                    </div>

                    <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200/60 dark:border-amber-900/40 text-xs space-y-1.5">
                        <div class="font-bold text-amber-900 dark:text-amber-300 flex items-center gap-1.5">
                            <i class="bi bi-info-circle-fill"></i>
                            <span>Self-Booking Disabled</span>
                        </div>
                        <p class="text-[11px] text-amber-800 dark:text-amber-400 leading-relaxed">
                            As a provider, you cannot book your own services. Clients browse and request appointments directly from this page.
                        </p>
                    </div>

                    <div class="space-y-2.5">
                        <a href="/profile" class="w-full py-3.5 px-4 rounded-2xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs text-center shadow-md transition flex items-center justify-center gap-2">
                            <i class="bi bi-pencil-square"></i>
                            <span>Edit Profile & Rates</span>
                        </a>
                        <a href="/provider/services" class="w-full py-3 px-4 rounded-2xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold text-xs text-center transition block">
                            Manage Listed Services
                        </a>
                        <a href="/dashboard" class="w-full py-2.5 px-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 hover:bg-slate-100 text-slate-600 dark:text-slate-300 font-bold text-xs text-center transition block">
                            Go to Dashboard
                        </a>
                    </div>
                </div>

                <!-- Client / Guest Booking Box -->
                <div id="clientBookingBox" class="sticky top-24 bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-7 border border-slate-200 dark:border-slate-800 shadow-xl shadow-slate-200/40 dark:shadow-none space-y-6">
                    <div>
                        <span class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider block">
                            Direct Booking Request
                        </span>
                        <h3 class="text-xl font-black text-slate-900 dark:text-white mt-1">
                            Hire {{ $provider->name }}
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            Connect directly for fast on-site service in {{ $profile?->city ?? 'Tunisia' }}.
                        </p>
                    </div>

                    <!-- Rate Breakdown -->
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/80 dark:border-slate-700/60 space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500 dark:text-slate-400">Tarif horaire</span>
                            <span class="font-black text-slate-900 dark:text-white">
                                {{ $profile?->hourly_rate ? number_format($profile->hourly_rate, 2) . ' TND' : 'Sur devis' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500 dark:text-slate-400">Commission Plateforme</span>
                            <span class="font-black text-blue-600 dark:text-blue-400">10% (Inclus)</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500 dark:text-slate-400">Moyen de paiement</span>
                            <span class="font-bold text-slate-700 dark:text-slate-300 flex items-center gap-1">
                                <i class="bi bi-credit-card-2-front text-blue-600"></i> Carte Bancaire (Stripe)
                            </span>
                        </div>
                    </div>

                    <!-- Direct Action Buttons -->
                    <div class="space-y-2.5">
                        <button type="button" 
                                onclick="openGuestBookingModal('{{ addslashes($provider->name) }}', '{{ $profile?->hourly_rate ? number_format($profile->hourly_rate, 2) . ' TND/hr' : 'Custom Quote' }}', {{ $provider->is_verified ? 'true' : 'false' }})" 
                                class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-extrabold text-xs text-center shadow-md shadow-blue-500/20 transition flex items-center justify-center gap-2">
                            <i class="bi bi-calendar-check text-sm"></i>
                            <span>Schedule Appointment</span>
                        </button>

                        <a href="/register?role=client" id="sidebarRegisterClientBtn" class="w-full py-3 px-4 rounded-2xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold text-xs text-center transition block">
                            Create Client Account to Hire
                        </a>
                    </div>

                    <!-- Trust Checklist -->
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-2 text-xs text-slate-500 dark:text-slate-400">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-shield-check text-emerald-500"></i>
                            <span>Paiement en ligne sécurisé 100% garanti via Stripe</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="bi bi-clock-history text-blue-500"></i>
                            <span>Confirmation immédiate après paiement</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="bi bi-geo-alt text-rose-500"></i>
                            <span>Zone d'intervention : {{ $profile?->city ?? 'Tunisia' }}</span>
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>

<!-- ======================================================== -->
<!-- LIGHTBOX MODAL FOR PORTFOLIO PHOTOS                      -->
<!-- ======================================================== -->
<div id="portfolioLightboxModal" class="hidden fixed inset-0 z-50 bg-slate-950/90 backdrop-blur-md flex items-center justify-center p-4">
    <div class="relative max-w-4xl w-full bg-slate-900 rounded-3xl overflow-hidden shadow-2xl border border-slate-800 text-white space-y-4 p-4 sm:p-6">
        <div class="flex items-center justify-between pb-2 border-b border-slate-800">
            <div>
                <h4 id="lightboxTitle" class="font-extrabold text-base text-white">Project Photo</h4>
                <p id="lightboxCaption" class="text-xs text-slate-400 mt-0.5"></p>
            </div>
            <button type="button" onclick="closeLightbox()" class="p-2 text-slate-400 hover:text-white rounded-xl bg-slate-800/80 transition">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>
        <div class="max-h-[75vh] flex items-center justify-center overflow-hidden rounded-2xl bg-black/40">
            <img id="lightboxImage" src="" alt="Enlarged Project Photo" class="max-h-[70vh] w-auto max-w-full object-contain rounded-xl">
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- GUEST BOOKING MODAL                                      -->
<!-- ======================================================== -->
<div id="providerBookingModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-6">
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-blue-100 dark:bg-blue-950/70 text-blue-600 dark:text-blue-400 flex items-center justify-center text-2xl shrink-0">
                    <i class="bi bi-calendar-check-fill"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-900 dark:text-white">Book This Provider</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Direct booking with {{ $provider->name }}</p>
                </div>
            </div>
            <button type="button" onclick="closeGuestBookingModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1">
                <i class="bi bi-x-lg text-lg"></i>
            </button>
        </div>

        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60 space-y-1.5 text-xs">
            <div class="flex items-center justify-between">
                <span class="text-slate-500 dark:text-slate-400">Professional:</span>
                <span id="bookingModalProviderName" class="font-extrabold text-slate-900 dark:text-white">Provider</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-slate-500 dark:text-slate-400">Rate:</span>
                <span id="bookingModalRate" class="font-black text-blue-600 dark:text-blue-400">0.00 TND</span>
            </div>
            <div class="flex items-center justify-between">
                <span class="text-slate-500 dark:text-slate-400">Location:</span>
                <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $profile?->city ?? 'Tunisia' }}</span>
            </div>
        </div>

        <!-- GUEST VIEW: Sign in / Create account prompts -->
        <div id="bookingModalGuestSection" class="space-y-4">
            <div class="space-y-2 text-center sm:text-left">
                <h4 class="text-sm font-extrabold text-slate-900 dark:text-white">
                    Sign in or create an account to book
                </h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    Connect directly with {{ $provider->name }} to schedule an appointment, share your address details, and receive updates.
                </p>
            </div>

            <div class="space-y-2 pt-1">
                <a href="/login?redirect={{ urlencode(route('providers.show', $provider->id)) }}" class="w-full py-3 px-4 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs text-center shadow-md shadow-blue-500/20 transition block">
                    Sign In to Hire
                </a>
                <a href="/register?role=client" class="w-full py-3 px-4 rounded-2xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold text-xs text-center transition block">
                    New to Khedma? Create Free Account
                </a>
            </div>
        </div>

        <!-- LOGGED-IN CLIENT VIEW: Actual Booking Form -->
        <form id="bookingModalClientSection" onsubmit="handleClientBookingSubmit(event)" class="hidden space-y-4">
            <input type="hidden" id="bookInputServiceId" value="">
            <input type="hidden" id="bookInputServiceTitle" value="Direct Consultation">
            <input type="hidden" id="bookInputPrice" value="{{ $profile?->hourly_rate ?? 50.00 }}">
            <input type="hidden" id="bookInputCategory" value="{{ $profile?->primary_category ?? 'General' }}">

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                    <i class="bi bi-clock-fill text-blue-600 mr-1"></i> Date & Heure du rendez-vous *
                </label>
                <input type="datetime-local" id="bookInputScheduledAt" required
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-xs bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                        <i class="bi bi-geo-alt-fill text-rose-500 mr-1"></i> Adresse d'intervention *
                    </label>
                    <input type="text" id="bookInputAddress" required placeholder="Ex: 12 Rue Habib Bourguiba"
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-xs bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Ville *</label>
                    <select id="bookInputCity" required
                        class="w-full px-3 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-xs bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600">
                        <option value="Tunis" selected>Tunis</option>
                        <option value="Ariana">Ariana</option>
                        <option value="Ben Arous">Ben Arous</option>
                        <option value="Manouba">Manouba</option>
                        <option value="Sousse">Sousse</option>
                        <option value="Sfax">Sfax</option>
                        <option value="Bizerte">Bizerte</option>
                        <option value="Nabeul">Nabeul</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">
                    <i class="bi bi-chat-left-text-fill text-slate-400 mr-1"></i> Remarques / Instructions (optionnel)
                </label>
                <textarea id="bookInputNotes" rows="2" placeholder="Décrivez votre besoin ou consignes d'accès..."
                    class="w-full px-3.5 py-2 rounded-xl border border-slate-300 dark:border-slate-700 text-xs bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 resize-none"></textarea>
            </div>

            <!-- Price & Platform Commission Breakdown (10%) -->
            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/60 space-y-1.5 text-xs">
                <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                    <span>Prestation :</span>
                    <span id="bookingModalBreakdownBasePrice" class="font-bold text-slate-800 dark:text-slate-200">50.00 TND</span>
                </div>
                <div class="flex items-center justify-between text-slate-600 dark:text-slate-400">
                    <span class="flex items-center gap-1">
                        <i class="bi bi-shield-check text-blue-600"></i> Commission Plateforme (10%) :
                    </span>
                    <span id="bookingModalBreakdownCommission" class="font-bold text-blue-600 dark:text-blue-400">5.00 TND</span>
                </div>
                <div class="flex items-center justify-between border-t border-slate-200/80 dark:border-slate-700/80 pt-1.5 font-black text-slate-900 dark:text-white">
                    <span>Total à régler par Stripe :</span>
                    <span id="bookingModalBreakdownTotal" class="text-sm font-black text-indigo-600 dark:text-indigo-400">50.00 TND</span>
                </div>
            </div>

            <div class="space-y-2 pt-1">
                <button type="submit" id="btnBookAndPayStripe"
                    class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-black text-xs text-center shadow-lg shadow-blue-500/25 transition flex items-center justify-center gap-2">
                    <i class="bi bi-shield-lock-fill text-sm"></i>
                    <span>Payer & Réserver avec Stripe</span>
                </button>
                <p class="text-[11px] text-center text-slate-400 dark:text-slate-500 flex items-center justify-center gap-1.5">
                    <i class="bi bi-lock-fill text-xs text-emerald-500"></i>
                    <span>Paiement en ligne sécurisé obligatoire (Stripe)</span>
                </p>
            </div>
        </form>
    </div>
</div>

<script>
    // Portfolio Lightbox
    function openLightbox(url, title, caption) {
        document.getElementById('lightboxImage').src = url;
        document.getElementById('lightboxTitle').textContent = title || 'Project Photo';
        document.getElementById('lightboxCaption').textContent = caption || '';
        document.getElementById('portfolioLightboxModal').classList.remove('hidden');
    }

    function closeLightbox() {
        document.getElementById('portfolioLightboxModal').classList.add('hidden');
    }

    document.getElementById('portfolioLightboxModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeLightbox();
        }
    });

    // Booking Modal
    function openGuestBookingModal(name, rate, isVerified, serviceId = null, serviceTitle = '', servicePrice = 0, category = '') {
        const user = JSON.parse(localStorage.getItem('khedma_user') || '{}');
        const token = localStorage.getItem('khedma_token');

        if (user && user.id === {{ (int) $provider->id }}) {
            alert('You cannot book your own service.');
            return;
        }
        if (user && user.role === 'provider') {
            alert('You are logged in as a Provider. To book services, please use a Client account.');
            return;
        }

        document.getElementById('bookingModalProviderName').textContent = name;
        document.getElementById('bookingModalRate').textContent = rate;

        const guestSection = document.getElementById('bookingModalGuestSection');
        const clientSection = document.getElementById('bookingModalClientSection');

        if (token && user && user.role === 'client') {
            guestSection?.classList.add('hidden');
            clientSection?.classList.remove('hidden');

            // Setup fields
            document.getElementById('bookInputServiceId').value = serviceId || '';
            document.getElementById('bookInputServiceTitle').value = serviceTitle || (rate.includes('(') ? rate.split('(')[1].replace(')', '') : 'Direct Consultation');
            
            let priceNum = servicePrice;
            if (!priceNum) {
                const parsed = parseFloat(rate);
                priceNum = !isNaN(parsed) && parsed > 0 ? parsed : {{ (float) ($profile?->hourly_rate ?? 50.00) }};
            }
            document.getElementById('bookInputPrice').value = priceNum;
            document.getElementById('bookInputCategory').value = category || '{{ addslashes($profile?->primary_category ?? "General Maintenance") }}';

            // Price & 10% platform commission breakdown
            const basePrice = priceNum;
            const comm = (basePrice * 0.10).toFixed(2);
            document.getElementById('bookingModalBreakdownBasePrice').textContent = basePrice.toFixed(2) + ' TND';
            document.getElementById('bookingModalBreakdownCommission').textContent = comm + ' TND';
            document.getElementById('bookingModalBreakdownTotal').textContent = basePrice.toFixed(2) + ' TND';

            // Pre-fill address
            const addrInput = document.getElementById('bookInputAddress');
            if (addrInput && !addrInput.value) {
                addrInput.value = user.client_profile?.address || user.address || '';
            }

            // Min date
            const dateInput = document.getElementById('bookInputScheduledAt');
            if (dateInput && !dateInput.value) {
                const now = new Date();
                now.setHours(now.getHours() + 2);
                now.setMinutes(0);
                dateInput.min = new Date().toISOString().slice(0, 16);
                dateInput.value = now.toISOString().slice(0, 16);
            }
        } else {
            guestSection?.classList.remove('hidden');
            clientSection?.classList.add('hidden');
        }

        document.getElementById('providerBookingModal').classList.remove('hidden');
    }

    function closeGuestBookingModal() {
        document.getElementById('providerBookingModal').classList.add('hidden');
    }

    document.getElementById('providerBookingModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeGuestBookingModal();
        }
    });

    async function handleClientBookingSubmit(e) {
        e.preventDefault();
        
        const token = localStorage.getItem('khedma_token');
        if (!token) {
            window.location.href = '/login?redirect=' + encodeURIComponent(window.location.href);
            return;
        }

        const btnStripe = document.getElementById('btnBookAndPayStripe');
        const origStripeText = btnStripe.innerHTML;

        btnStripe.disabled = true;
        btnStripe.innerHTML = '<span class="inline-block animate-spin mr-1.5"><i class="bi bi-arrow-repeat"></i></span> Préparation du paiement Stripe...';

        const scheduledAt = document.getElementById('bookInputScheduledAt').value;
        const address = document.getElementById('bookInputAddress').value;
        const city = document.getElementById('bookInputCity').value;
        const notes = document.getElementById('bookInputNotes').value;
        const serviceId = document.getElementById('bookInputServiceId').value;
        const serviceTitle = document.getElementById('bookInputServiceTitle').value;
        const price = document.getElementById('bookInputPrice').value;
        const category = document.getElementById('bookInputCategory').value;

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const res = await fetch('/api/v1/client/bookings', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || ''
                },
                body: JSON.stringify({
                    provider_id: {{ (int) $provider->id }},
                    service_id: serviceId ? parseInt(serviceId) : null,
                    service_title: serviceTitle,
                    category: category,
                    total_price: parseFloat(price) || 50,
                    scheduled_at: scheduledAt,
                    address: address,
                    city: city,
                    client_notes: notes || null
                })
            });

            const data = await res.json();
            if (!res.ok) {
                alert(data.message || 'Impossible de créer la réservation.');
                btnStripe.disabled = false;
                btnStripe.innerHTML = origStripeText;
                return;
            }

            const booking = data.data;

            // Direct online Stripe payment (no cash on delivery / no payment on site)
            const stripeRes = await fetch(`/api/v1/client/bookings/${booking.id}/stripe-checkout`, {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || ''
                }
            });
            const stripeData = await stripeRes.json();
            if (stripeRes.ok && stripeData.checkout_url) {
                window.location.href = stripeData.checkout_url;
                return;
            } else {
                alert(stripeData.message || 'La réservation a été créée. Veuillez procéder au paiement depuis votre tableau de bord.');
                window.location.href = '/dashboard';
            }
        } catch(err) {
            console.error(err);
            alert('Une erreur est survenue lors de l\'initialisation du paiement Stripe.');
            btnStripe.disabled = false;
            btnStripe.innerHTML = origStripeText;
        }
    }

    // Favorite / Bookmark Provider
    let isCurrentlyFavorited = {{ ($isFavorited ?? false) ? 'true' : 'false' }};

    document.addEventListener('DOMContentLoaded', async () => {
        try {
            const user = JSON.parse(localStorage.getItem('khedma_user') || '{}');
            const targetProviderId = {{ (int) $provider->id }};

            if (user.id === targetProviderId) {
                // Owner provider viewing their own profile
                const heroBooking = document.getElementById('heroBookingBtn');
                if (heroBooking) heroBooking.classList.add('hidden');

                const ownerEdit = document.getElementById('ownerHeroEditBtn');
                if (ownerEdit) ownerEdit.classList.remove('hidden');

                const ownerServices = document.getElementById('ownerHeroServicesBtn');
                if (ownerServices) ownerServices.classList.remove('hidden');

                const clientBox = document.getElementById('clientBookingBox');
                if (clientBox) clientBox.classList.add('hidden');

                const ownerSidebar = document.getElementById('ownerProfileSidebar');
                if (ownerSidebar) ownerSidebar.classList.remove('hidden');

                const ownerPortfolioBtn = document.getElementById('ownerPortfolioUploadBtn');
                if (ownerPortfolioBtn) ownerPortfolioBtn.style.display = 'flex';

                const ownerEmptyNotice = document.getElementById('ownerEmptyPortfolioNotice');
                if (ownerEmptyNotice) ownerEmptyNotice.style.display = 'block';

                const favBtn = document.getElementById('favoriteBtn');
                if (favBtn) favBtn.classList.add('hidden');

                // Hide individual booking buttons on their services
                document.querySelectorAll('.pro-service-book-btn').forEach(b => b.classList.add('hidden'));
            } else if (user.role === 'provider') {
                // Another provider is viewing
                const favBtn = document.getElementById('favoriteBtn');
                if (favBtn) favBtn.classList.add('hidden');
                document.querySelectorAll('.pro-service-book-btn').forEach(b => b.classList.add('hidden'));
            }

            if (user && user.role === 'client') {
                const sidebarRegisterBtn = document.getElementById('sidebarRegisterClientBtn');
                if (sidebarRegisterBtn) sidebarRegisterBtn.classList.add('hidden');
            }

            if (user && user.role === 'provider') {
                const compareBtn = document.getElementById('profileCompareBtn');
                if (compareBtn) compareBtn.classList.add('hidden');
            }
        } catch(e) {
            console.error(e);
        }

        const token = localStorage.getItem('khedma_token');
        if (token) {
            try {
                const res = await fetch('/api/v1/client/favorites/{{ $provider->id }}/status', {
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });
                if (res.ok) {
                    const data = await res.json();
                    setFavoriteButtonState(data.is_favorited);
                }
            } catch(e) {}
        }
    });

    function setFavoriteButtonState(favorited) {
        isCurrentlyFavorited = favorited;
        const icon = document.getElementById('favoriteIcon');
        const label = document.getElementById('favoriteLabel');
        const btn = document.getElementById('favoriteBtn');

        if (!icon || !btn) return;

        if (favorited) {
            icon.className = 'bi bi-heart-fill text-rose-500 text-sm scale-110';
            if (label) label.textContent = 'Saved';
            btn.title = 'Remove from favorites';
        } else {
            icon.className = 'bi bi-heart text-sm';
            if (label) label.textContent = 'Save';
            btn.title = 'Save provider to favorites';
        }
    }

    async function toggleFavorite(providerId) {
        const token = localStorage.getItem('khedma_token');
        if (!token) {
            if (confirm('Please log in to save providers to your favorites list. Go to login?')) {
                window.location.href = '/login';
            }
            return;
        }

        const icon = document.getElementById('favoriteIcon');
        if (icon) icon.classList.add('scale-125');

        try {
            const method = isCurrentlyFavorited ? 'DELETE' : 'POST';
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
                setFavoriteButtonState(data.is_favorited);
            } else {
                alert(data.message || 'Action could not be performed.');
            }
        } catch(e) {
            console.error('Error toggling favorite:', e);
        } finally {
            if (icon) icon.classList.remove('scale-125');
        }
    }
</script>
@endsection
