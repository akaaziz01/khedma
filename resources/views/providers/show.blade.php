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
                                onclick="openGuestBookingModal('{{ addslashes($provider->name) }}', '{{ $profile?->hourly_rate ? number_format($profile->hourly_rate, 2) . ' TND/hr' : 'Custom Quote' }}', {{ $provider->is_verified ? 'true' : 'false' }})" 
                                class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-md shadow-blue-500/20 transition flex items-center justify-center gap-2">
                            <i class="bi bi-calendar-plus"></i>
                            <span>Request Booking</span>
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
                                                    onclick="openGuestBookingModal('{{ addslashes($provider->name) }}', '{{ number_format($service->price, 2) }} TND ({{ addslashes($service->title) }})', {{ $provider->is_verified ? 'true' : 'false' }})" 
                                                    class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-extrabold transition">
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
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-images text-blue-600 text-lg"></i>
                            <h2 class="text-lg font-black text-slate-900 dark:text-white">Portfolio & Past Projects</h2>
                        </div>
                        <span class="text-xs font-bold text-slate-400">
                            {{ count($portfolio) }} {{ count($portfolio) === 1 ? 'photo' : 'photos' }}
                        </span>
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
                
                <div class="sticky top-24 bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-7 border border-slate-200 dark:border-slate-800 shadow-xl shadow-slate-200/40 dark:shadow-none space-y-6">
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
                            <span class="text-slate-500 dark:text-slate-400">Hourly Rate</span>
                            <span class="font-black text-slate-900 dark:text-white">
                                {{ $profile?->hourly_rate ? number_format($profile->hourly_rate, 2) . ' TND' : 'Custom' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500 dark:text-slate-400">Booking Fee</span>
                            <span class="font-black text-emerald-600 dark:text-emerald-400">0.00 TND (Free)</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500 dark:text-slate-400">Verified Craft</span>
                            <span class="font-bold text-slate-700 dark:text-slate-300">
                                {{ $provider->is_verified ? 'Yes (Verified)' : 'Standard' }}
                            </span>
                        </div>
                    </div>

                    <!-- Direct Action Buttons -->
                    <div class="space-y-2.5">
                        <button type="button" 
                                onclick="openGuestBookingModal('{{ addslashes($provider->name) }}', '{{ $profile?->hourly_rate ? number_format($profile->hourly_rate, 2) . ' TND/hr' : 'Custom Quote' }}', {{ $provider->is_verified ? 'true' : 'false' }})" 
                                class="w-full py-3.5 px-4 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs text-center shadow-md shadow-blue-500/20 transition flex items-center justify-center gap-2">
                            <i class="bi bi-calendar-check text-sm"></i>
                            <span>Schedule Appointment</span>
                        </button>

                        <a href="/register?role=client" class="w-full py-3 px-4 rounded-2xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 font-bold text-xs text-center transition block">
                            Create Client Account to Hire
                        </a>
                    </div>

                    <!-- Trust Checklist -->
                    <div class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-2 text-xs text-slate-500 dark:text-slate-400">
                        <div class="flex items-center gap-2">
                            <i class="bi bi-shield-check text-emerald-500"></i>
                            <span>No advance payment needed to inquire</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="bi bi-clock-history text-blue-500"></i>
                            <span>Direct response from professional</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="bi bi-geo-alt text-rose-500"></i>
                            <span>Local service area: {{ $profile?->city ?? 'Tunisia' }}</span>
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
    function openGuestBookingModal(name, rate, isVerified) {
        document.getElementById('bookingModalProviderName').textContent = name;
        document.getElementById('bookingModalRate').textContent = rate;
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
</script>
@endsection
