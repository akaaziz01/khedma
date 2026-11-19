@extends('layouts.app')

@section('title', $service->title . ' | Khedma Services')

@section('content')
<div class="bg-slate-50 dark:bg-slate-950 min-h-screen py-10 transition-colors duration-200">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        
        <!-- Breadcrumb Navigation -->
        <nav class="flex items-center gap-2 text-xs font-semibold text-slate-500 dark:text-slate-400">
            <a href="/" class="hover:text-blue-600 dark:hover:text-blue-400">Home</a>
            <i class="bi bi-chevron-right text-[10px]"></i>
            <a href="{{ route('services.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400">Services</a>
            <i class="bi bi-chevron-right text-[10px]"></i>
            <span class="text-slate-900 dark:text-white truncate max-w-xs">{{ $service->title }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left / Main Column: Service Description & Overview -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200 dark:border-slate-800 shadow-sm space-y-6">
                    <div class="flex items-start justify-between gap-4 flex-wrap">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-blue-50 dark:bg-blue-950/70 text-blue-700 dark:text-blue-300 border border-blue-100 dark:border-blue-900/40">
                            <i class="bi {{ $service->serviceCategory?->icon ?? 'bi-tools' }}"></i>
                            <span>{{ $service->serviceCategory?->name ?? $service->category ?? 'General' }}</span>
                        </span>
                        
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-950/50 px-2.5 py-1 rounded-full border border-emerald-200 dark:border-emerald-800/40">
                                <i class="bi bi-check-circle-fill"></i>
                                <span>Verified & Approved Service</span>
                            </span>
                        </div>
                    </div>

                    <div>
                        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                            {{ $service->title }}
                        </h1>
                        <p class="text-xs text-slate-400 mt-1">
                            Listed on {{ $service->created_at->format('M d, Y') }}
                        </p>
                    </div>

                    <div class="border-t border-slate-100 dark:border-slate-800 pt-6">
                        <h2 class="text-sm font-extrabold text-slate-900 dark:text-white uppercase tracking-wider mb-3">
                            Service Description
                        </h2>
                        <div class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-line">
                            {{ $service->description }}
                        </div>
                    </div>

                    <!-- Key Service Specs -->
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 pt-4 border-t border-slate-100 dark:border-slate-800">
                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/60">
                            <span class="text-xs text-slate-400 font-semibold block">Fixed Rate</span>
                            <span class="text-lg font-black text-slate-900 dark:text-white mt-0.5 block">
                                {{ number_format($service->price, 2) }} <span class="text-xs font-bold text-blue-600">TND</span>
                            </span>
                        </div>
                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/60">
                            <span class="text-xs text-slate-400 font-semibold block">Est. Duration</span>
                            <span class="text-lg font-black text-slate-900 dark:text-white mt-0.5 block">
                                {{ $service->duration_hours }} hr{{ $service->duration_hours > 1 ? 's' : '' }}
                            </span>
                        </div>
                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700/60 col-span-2 sm:col-span-1">
                            <span class="text-xs text-slate-400 font-semibold block">Service Area</span>
                            <span class="text-lg font-black text-slate-900 dark:text-white mt-0.5 block truncate">
                                {{ $service->provider?->providerProfile?->city ?? 'Tunisia' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Provider Profile Card & Booking CTA -->
            <div class="space-y-6">
                <!-- Provider Box -->
                @php
                    $provider = $service->provider;
                    $profile = $provider?->providerProfile;
                @endphp
                <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 border border-slate-200 dark:border-slate-800 shadow-sm space-y-5">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-extrabold text-slate-400 uppercase tracking-wider block">
                            Service Provider
                        </span>
                        @if($provider)
                            <div class="flex items-center gap-2">
                                <button type="button" 
                                        onclick="toggleQuickFavorite({{ $provider->id }})" 
                                        id="quickFavBtn" 
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-rose-500 hover:bg-slate-100 dark:hover:bg-slate-800 transition" 
                                        title="Save provider to favorites">
                                    <i id="quickFavIcon" class="bi bi-heart text-sm"></i>
                                </button>
                                <a href="{{ route('providers.show', $provider->id) }}" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline">
                                    View Profile
                                </a>
                            </div>
                        @endif
                    </div>

                    <a href="{{ $provider ? route('providers.show', $provider->id) : '#' }}" class="group/pro flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-extrabold text-xl flex items-center justify-center shrink-0 shadow-md group-hover/pro:scale-105 transition-transform">
                            {{ strtoupper(substr($provider?->name ?? 'P', 0, 1)) }}
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <h3 class="font-extrabold text-base text-slate-900 dark:text-white truncate group-hover/pro:text-blue-600 transition">
                                    {{ $provider?->name }}
                                </h3>
                            </div>
                            @if($provider?->is_verified)
                                <div class="inline-flex items-center gap-1 mt-0.5 px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-300">
                                    <i class="bi bi-patch-check-fill text-emerald-600"></i>
                                    <span>Verified Professional</span>
                                </div>
                            @endif
                            @if($profile?->business_name)
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-semibold">
                                    {{ $profile->business_name }}
                                </p>
                            @endif
                        </div>
                    </a>

                    <div class="space-y-2.5 text-xs text-slate-600 dark:text-slate-300 pt-2 border-t border-slate-100 dark:border-slate-800">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Location:</span>
                            <span class="font-bold text-slate-900 dark:text-white flex items-center gap-1">
                                <i class="bi bi-geo-alt-fill text-rose-500"></i>
                                {{ $profile?->city ?? 'Tunisia' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Hourly Rate:</span>
                            <span class="font-bold text-slate-900 dark:text-white">
                                {{ $profile?->hourly_rate ? number_format($profile->hourly_rate, 2) . ' TND/hr' : 'Flexible' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400">Availability:</span>
                            <span class="font-bold {{ $profile?->is_available ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400' }}">
                                {{ $profile?->is_available ? 'Available Now' : 'Schedule in Advance' }}
                            </span>
                        </div>
                    </div>

                    @if(!empty($profile?->skills))
                        <div class="pt-3 border-t border-slate-100 dark:border-slate-800">
                            <span class="text-[11px] font-bold text-slate-400 block mb-2">Skills & Specializations:</span>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($profile->skills as $skill)
                                    <span class="px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-[10px] font-semibold">
                                        {{ $skill }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($profile?->bio)
                        <div class="pt-3 border-t border-slate-100 dark:border-slate-800 text-xs text-slate-500 dark:text-slate-400 leading-relaxed italic">
                            "{{ $profile->bio }}"
                        </div>
                    @endif
                </div>

                <!-- Booking Action Box -->
                <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-3xl p-6 text-white shadow-xl shadow-blue-500/20 space-y-4">
                    <div>
                        <span class="text-xs text-blue-200 font-bold uppercase tracking-wider block">Service Price</span>
                        <div class="text-3xl font-black mt-1">
                            {{ number_format($service->price, 2) }} <span class="text-base font-bold text-blue-200">TND</span>
                        </div>
                    </div>

                    <p class="text-xs text-blue-100 leading-relaxed">
                        Secure your booking request directly with {{ $provider?->name }}. No upfront payment required to request an appointment.
                    </p>

                    <div class="pt-2 space-y-2">
                        <a href="/login?redirect={{ urlencode(route('services.show', $service->id)) }}" class="w-full py-3 px-4 rounded-2xl bg-white text-blue-700 hover:bg-blue-50 font-extrabold text-xs text-center shadow-sm transition block">
                            Sign In to Book This Service
                        </a>
                        <a href="/register?role=client" class="w-full py-2.5 px-4 rounded-2xl bg-blue-700/60 hover:bg-blue-700 text-white font-bold text-xs text-center border border-blue-500/50 transition block">
                            New Client? Create Account
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    let isQuickFav = false;

    document.addEventListener('DOMContentLoaded', async () => {
        const token = localStorage.getItem('khedma_token');
        const providerId = {{ $provider?->id ?? 'null' }};
        if (token && providerId) {
            try {
                const res = await fetch(`/api/v1/client/favorites/${providerId}/status`, {
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });
                if (res.ok) {
                    const data = await res.json();
                    setQuickFavState(data.is_favorited);
                }
            } catch(e) {}
        }
    });

    function setQuickFavState(favorited) {
        isQuickFav = favorited;
        const icon = document.getElementById('quickFavIcon');
        const btn = document.getElementById('quickFavBtn');
        if (!icon || !btn) return;

        if (favorited) {
            icon.className = 'bi bi-heart-fill text-rose-500 text-sm';
            btn.title = 'Remove from favorites';
        } else {
            icon.className = 'bi bi-heart text-sm';
            btn.title = 'Save provider to favorites';
        }
    }

    async function toggleQuickFavorite(providerId) {
        const token = localStorage.getItem('khedma_token');
        if (!token) {
            if (confirm('Please log in to save providers to your favorites list. Go to login?')) {
                window.location.href = '/login';
            }
            return;
        }

        try {
            const method = isQuickFav ? 'DELETE' : 'POST';
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
                setQuickFavState(data.is_favorited);
            } else {
                alert(data.message || 'Action failed.');
            }
        } catch(e) {
            console.error(e);
        }
    }
</script>
@endsection
