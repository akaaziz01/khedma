@extends('layouts.app')

@section('title', 'Khedma | Find & Offer On-Demand Services')

@section('content')
<!-- Hero Section -->
<section class="relative overflow-hidden bg-gradient-to-b from-blue-50/50 via-white to-white py-16 sm:py-24 border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto space-y-6">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-100/80 text-blue-800 text-xs font-bold tracking-wide uppercase">
                <span>✨ Service Marketplace</span>
                <span class="w-1 h-1 rounded-full bg-blue-500"></span>
                <span>Clients & Providers</span>
            </div>
            
            <h1 class="text-4xl sm:text-6xl font-black text-slate-900 tracking-tight leading-tight">
                Quality local services, <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-indigo-600">at your fingertips.</span>
            </h1>

            <p class="text-lg text-slate-600 leading-relaxed">
                Whether you need an expert plumber to fix an emergency leak, or you are a skilled craftsperson seeking new clients — <strong>KHEDMA</strong> connects you with trusted professionals.
            </p>

            <!-- Dual Role Action Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-xl mx-auto pt-4 text-left">
                <!-- Client Card -->
                <a href="/register?role=client" class="group p-5 bg-white rounded-2xl border-2 border-blue-500/30 hover:border-blue-600 shadow-lg shadow-blue-500/5 hover:shadow-blue-500/15 transition-all flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 group-hover:bg-blue-600 text-blue-600 group-hover:text-white transition flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="font-extrabold text-slate-900 group-hover:text-blue-600 transition">I Need a Service</span>
                            <span class="text-xs text-blue-600 font-bold group-hover:translate-x-1 transition">→</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Book certified plumbers, electricians & cleaners in minutes.</p>
                        <span class="inline-block mt-2 text-[10px] font-bold text-blue-700 uppercase tracking-wider">Join as Client</span>
                    </div>
                </a>

                <!-- Provider Card -->
                <a href="/register?role=provider" class="group p-5 bg-white rounded-2xl border-2 border-amber-500/30 hover:border-amber-600 shadow-lg shadow-amber-500/5 hover:shadow-amber-500/15 transition-all flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 group-hover:bg-amber-600 text-amber-700 group-hover:text-white transition flex items-center justify-center shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="font-extrabold text-slate-900 group-hover:text-amber-700 transition">I Offer Services</span>
                            <span class="text-xs text-amber-700 font-bold group-hover:translate-x-1 transition">→</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Expand your business and receive high-paying customer inquiries.</p>
                        <span class="inline-block mt-2 text-[10px] font-bold text-amber-800 uppercase tracking-wider">Join as Provider</span>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Categories Showcase -->
<section class="py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex items-end justify-between mb-8">
        <div>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Popular Service Categories</h2>
            <p class="text-sm text-slate-500 mt-1">Explore our most requested home and trade services.</p>
        </div>
        <a href="/register?role=client" class="text-sm font-bold text-blue-600 hover:text-blue-700 hidden sm:block">
            View All Categories →
        </a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-5 bg-white rounded-2xl border border-slate-200 hover:border-blue-400 hover:shadow-md transition">
            <span class="text-3xl block mb-2">🔧</span>
            <h3 class="font-bold text-slate-900 text-sm">Plumbing</h3>
            <p class="text-xs text-slate-500 mt-1">Pipe repairs, drains, heaters</p>
        </div>
        <div class="p-5 bg-white rounded-2xl border border-slate-200 hover:border-blue-400 hover:shadow-md transition">
            <span class="text-3xl block mb-2">⚡</span>
            <h3 class="font-bold text-slate-900 text-sm">Electricity</h3>
            <p class="text-xs text-slate-500 mt-1">Wiring, panels, smart lights</p>
        </div>
        <div class="p-5 bg-white rounded-2xl border border-slate-200 hover:border-blue-400 hover:shadow-md transition">
            <span class="text-3xl block mb-2">❄️</span>
            <h3 class="font-bold text-slate-900 text-sm">HVAC & Climate</h3>
            <p class="text-xs text-slate-500 mt-1">AC repair, installation</p>
        </div>
        <div class="p-5 bg-white rounded-2xl border border-slate-200 hover:border-blue-400 hover:shadow-md transition">
            <span class="text-3xl block mb-2">🧹</span>
            <h3 class="font-bold text-slate-900 text-sm">Cleaning</h3>
            <p class="text-xs text-slate-500 mt-1">Home, office, deep clean</p>
        </div>
        <div class="p-5 bg-white rounded-2xl border border-slate-200 hover:border-blue-400 hover:shadow-md transition">
            <span class="text-3xl block mb-2">🎨</span>
            <h3 class="font-bold text-slate-900 text-sm">Painting</h3>
            <p class="text-xs text-slate-500 mt-1">Interior & exterior walls</p>
        </div>
        <div class="p-5 bg-white rounded-2xl border border-slate-200 hover:border-blue-400 hover:shadow-md transition">
            <span class="text-3xl block mb-2">🪚</span>
            <h3 class="font-bold text-slate-900 text-sm">Carpentry</h3>
            <p class="text-xs text-slate-500 mt-1">Furniture, doors, custom work</p>
        </div>
        <div class="p-5 bg-white rounded-2xl border border-slate-200 hover:border-blue-400 hover:shadow-md transition">
            <span class="text-3xl block mb-2">📱</span>
            <h3 class="font-bold text-slate-900 text-sm">IT & Network</h3>
            <p class="text-xs text-slate-500 mt-1">Wi-Fi, computers, cameras</p>
        </div>
        <div class="p-5 bg-white rounded-2xl border border-slate-200 hover:border-blue-400 hover:shadow-md transition">
            <span class="text-3xl block mb-2">🧺</span>
            <h3 class="font-bold text-slate-900 text-sm">Appliances</h3>
            <p class="text-xs text-slate-500 mt-1">Fridges, washing machines</p>
        </div>
    </div>
</section>

<!-- Call to action -->
<section class="bg-slate-900 text-white py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
        <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Ready to get started?</h2>
        <p class="text-slate-400 text-base max-w-xl mx-auto">
            Create your account today as a client to find trusted help, or as a provider to start earning.
        </p>
        <div class="flex items-center justify-center gap-4 flex-wrap">
            <a href="/register?role=client" class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-md transition">
                Register as Client
            </a>
            <a href="/register?role=provider" class="px-6 py-3 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-sm shadow-md transition">
                Register as Provider
            </a>
        </div>
    </div>
</section>
@endsection
