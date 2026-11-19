@extends('layouts.app')

@section('title', 'Khedma | Find & Offer On-Demand Services')

@section('content')
<!-- Hero Section -->
<section class="relative overflow-hidden bg-gradient-to-b from-blue-50/50 via-white to-white dark:from-slate-900 dark:via-slate-950 dark:to-slate-950 py-16 sm:py-24 border-b border-slate-100 dark:border-slate-800 transition-colors duration-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto space-y-6">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-100/80 dark:bg-blue-950/60 text-blue-800 dark:text-blue-300 text-xs font-bold tracking-wide uppercase border border-blue-200/50 dark:border-blue-800/40">
                <i class="bi bi-stars text-amber-500"></i>
                <span>Service Marketplace</span>
                <span class="w-1 h-1 rounded-full bg-blue-500"></span>
                <span>Clients & Providers</span>
            </div>
            
            <h1 class="text-4xl sm:text-6xl font-black text-slate-900 dark:text-white tracking-tight leading-tight">
                Quality local services, <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 via-indigo-600 to-amber-500">at your fingertips.</span>
            </h1>

            <p class="text-lg text-slate-600 dark:text-slate-400 leading-relaxed">
                Whether you need an expert plumber to fix an emergency leak, or you are a skilled craftsperson seeking new clients — <strong>KHEDMA</strong> connects you with trusted professionals.
            </p>

            <!-- Search Bar for Guests -->
            <form action="{{ route('services.index') }}" method="GET" class="w-full max-w-3xl mx-auto p-2 sm:p-2.5 bg-white dark:bg-slate-900 rounded-3xl shadow-xl shadow-blue-500/10 border border-slate-200 dark:border-slate-800 transition text-left">
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-2">
                    <!-- Keyword input -->
                    <div class="sm:col-span-5 relative flex items-center">
                        <i class="bi bi-search absolute left-3.5 text-slate-400"></i>
                        <input type="text" name="keyword" placeholder="What service do you need? (e.g. leak, wiring, AC)..." 
                            class="w-full pl-10 pr-3 py-3 rounded-2xl bg-slate-50 dark:bg-slate-800/70 border-0 text-slate-900 dark:text-white placeholder-slate-400 focus:ring-2 focus:ring-blue-500 text-xs sm:text-sm font-medium">
                    </div>

                    <!-- Trade / Category select -->
                    <div class="sm:col-span-3 relative flex items-center">
                        <i class="bi bi-tools absolute left-3.5 text-slate-400"></i>
                        <select name="trade" class="w-full pl-9 pr-7 py-3 rounded-2xl bg-slate-50 dark:bg-slate-800/70 border-0 text-slate-800 dark:text-slate-200 text-xs sm:text-sm font-medium focus:ring-2 focus:ring-blue-500 appearance-none cursor-pointer">
                            <option value="">All Trades</option>
                            <option value="Plumbing">Plumbing</option>
                            <option value="Electricity">Electricity</option>
                            <option value="HVAC & Climate">HVAC & Climate</option>
                            <option value="Cleaning">Cleaning</option>
                            <option value="Painting">Painting</option>
                            <option value="Carpentry">Carpentry</option>
                            <option value="IT & Network">IT & Network</option>
                            <option value="Appliances">Appliances</option>
                        </select>
                        <i class="bi bi-chevron-down absolute right-3 text-[10px] text-slate-400 pointer-events-none"></i>
                    </div>

                    <!-- Location / City select -->
                    <div class="sm:col-span-2 relative flex items-center">
                        <i class="bi bi-geo-alt absolute left-3 text-slate-400"></i>
                        <select name="location" class="w-full pl-8 pr-6 py-3 rounded-2xl bg-slate-50 dark:bg-slate-800/70 border-0 text-slate-800 dark:text-slate-200 text-xs sm:text-sm font-medium focus:ring-2 focus:ring-blue-500 appearance-none cursor-pointer">
                            <option value="">All Cities</option>
                            <option value="Tunis">Tunis</option>
                            <option value="Ariana">Ariana</option>
                            <option value="Ben Arous">Ben Arous</option>
                            <option value="Sousse">Sousse</option>
                            <option value="Sfax">Sfax</option>
                            <option value="Bizerte">Bizerte</option>
                            <option value="Nabeul">Nabeul</option>
                        </select>
                        <i class="bi bi-chevron-down absolute right-2.5 text-[10px] text-slate-400 pointer-events-none"></i>
                    </div>

                    <!-- Submit Button -->
                    <div class="sm:col-span-2">
                        <button type="submit" class="w-full h-full py-3 px-3 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-extrabold text-xs shadow-md shadow-blue-500/20 transition flex items-center justify-center gap-1.5">
                            <span>Search</span>
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </form>

            <!-- Dual Role Action Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-xl mx-auto pt-4 text-left">
                <!-- Client Card -->
                <a href="/register?role=client" class="group p-5 bg-white dark:bg-slate-900 rounded-2xl border-2 border-blue-500/30 dark:border-blue-500/20 hover:border-blue-600 dark:hover:border-blue-500 shadow-lg shadow-blue-500/5 dark:shadow-none transition-all flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-950/60 group-hover:bg-blue-600 text-blue-600 group-hover:text-white transition flex items-center justify-center shrink-0">
                        <i class="bi bi-person-fill text-2xl"></i>
                    </div>
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="font-extrabold text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">I Need a Service</span>
                            <i class="bi bi-arrow-right text-xs text-blue-600 dark:text-blue-400 font-bold group-hover:translate-x-1 transition"></i>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Book certified plumbers, electricians & cleaners in minutes.</p>
                        <span class="inline-block mt-2 text-[10px] font-bold text-blue-700 dark:text-blue-400 uppercase tracking-wider">Join as Client</span>
                    </div>
                </a>

                <!-- Provider Card -->
                <a href="/register?role=provider" class="group p-5 bg-white dark:bg-slate-900 rounded-2xl border-2 border-amber-500/30 dark:border-amber-500/20 hover:border-amber-600 dark:hover:border-amber-500 shadow-lg shadow-amber-500/5 dark:shadow-none transition-all flex items-start gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-950/60 group-hover:bg-amber-600 text-amber-700 dark:text-amber-400 group-hover:text-white transition flex items-center justify-center shrink-0">
                        <i class="bi bi-tools text-2xl"></i>
                    </div>
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="font-extrabold text-slate-900 dark:text-white group-hover:text-amber-700 dark:group-hover:text-amber-400 transition">I Offer Services</span>
                            <i class="bi bi-arrow-right text-xs text-amber-700 dark:text-amber-400 font-bold group-hover:translate-x-1 transition"></i>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Expand your business and receive high-paying customer inquiries.</p>
                        <span class="inline-block mt-2 text-[10px] font-bold text-amber-800 dark:text-amber-400 uppercase tracking-wider">Join as Provider</span>
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
            <h2 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">Popular Service Categories</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Explore our most requested home and trade services across Tunisia.</p>
        </div>
        <a href="{{ route('services.index') }}" class="text-sm font-bold text-blue-600 dark:text-blue-400 hover:text-blue-700 hidden sm:flex items-center gap-1">
            <span>Browse All Services</span>
            <i class="bi bi-arrow-right"></i>
        </a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <a href="{{ route('services.index', ['trade' => 'Plumbing']) }}" class="group p-5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-blue-400 dark:hover:border-blue-500 hover:shadow-md transition">
            <i class="bi bi-wrench-adjustable text-blue-500 text-3xl block mb-2 group-hover:scale-110 transition-transform"></i>
            <h3 class="font-bold text-slate-900 dark:text-white text-sm group-hover:text-blue-600 transition">Plumbing</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Pipe repairs, drains, heaters</p>
        </a>
        <a href="{{ route('services.index', ['trade' => 'Electricity']) }}" class="group p-5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-blue-400 dark:hover:border-blue-500 hover:shadow-md transition">
            <i class="bi bi-lightning-charge-fill text-amber-500 text-3xl block mb-2 group-hover:scale-110 transition-transform"></i>
            <h3 class="font-bold text-slate-900 dark:text-white text-sm group-hover:text-amber-600 transition">Electricity</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Wiring, panels, smart lights</p>
        </a>
        <a href="{{ route('services.index', ['trade' => 'HVAC & Climate']) }}" class="group p-5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-blue-400 dark:hover:border-blue-500 hover:shadow-md transition">
            <i class="bi bi-snow text-cyan-500 text-3xl block mb-2 group-hover:scale-110 transition-transform"></i>
            <h3 class="font-bold text-slate-900 dark:text-white text-sm group-hover:text-cyan-600 transition">HVAC & Climate</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">AC repair, installation</p>
        </a>
        <a href="{{ route('services.index', ['trade' => 'Cleaning']) }}" class="group p-5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-blue-400 dark:hover:border-blue-500 hover:shadow-md transition">
            <i class="bi bi-stars text-emerald-500 text-3xl block mb-2 group-hover:scale-110 transition-transform"></i>
            <h3 class="font-bold text-slate-900 dark:text-white text-sm group-hover:text-emerald-600 transition">Cleaning</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Home, office, deep clean</p>
        </a>
        <a href="{{ route('services.index', ['trade' => 'Painting']) }}" class="group p-5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-blue-400 dark:hover:border-blue-500 hover:shadow-md transition">
            <i class="bi bi-brush-fill text-purple-500 text-3xl block mb-2 group-hover:scale-110 transition-transform"></i>
            <h3 class="font-bold text-slate-900 dark:text-white text-sm group-hover:text-purple-600 transition">Painting</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Interior & exterior walls</p>
        </a>
        <a href="{{ route('services.index', ['trade' => 'Carpentry']) }}" class="group p-5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-blue-400 dark:hover:border-blue-500 hover:shadow-md transition">
            <i class="bi bi-hammer text-orange-500 text-3xl block mb-2 group-hover:scale-110 transition-transform"></i>
            <h3 class="font-bold text-slate-900 dark:text-white text-sm group-hover:text-orange-600 transition">Carpentry</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Furniture, doors, custom work</p>
        </a>
        <a href="{{ route('services.index', ['trade' => 'IT & Network']) }}" class="group p-5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-blue-400 dark:hover:border-blue-500 hover:shadow-md transition">
            <i class="bi bi-router-fill text-indigo-500 text-3xl block mb-2 group-hover:scale-110 transition-transform"></i>
            <h3 class="font-bold text-slate-900 dark:text-white text-sm group-hover:text-indigo-600 transition">IT & Network</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Wi-Fi, computers, cameras</p>
        </a>
        <a href="{{ route('services.index', ['trade' => 'Appliances']) }}" class="group p-5 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200 dark:border-slate-800 hover:border-blue-400 dark:hover:border-blue-500 hover:shadow-md transition">
            <i class="bi bi-plug-fill text-rose-500 text-3xl block mb-2 group-hover:scale-110 transition-transform"></i>
            <h3 class="font-bold text-slate-900 dark:text-white text-sm group-hover:text-rose-600 transition">Appliances</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Fridges, washing machines</p>
        </a>
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
