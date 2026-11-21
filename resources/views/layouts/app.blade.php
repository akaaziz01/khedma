<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Khedma | Service Marketplace')</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                        },
                        amberbrand: {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                        }
                    }
                }
            }
        }
    </script>
    <script>
        // Apply theme immediately on head load to prevent theme flash
        if (localStorage.getItem('khedma_theme') === 'dark' || (!('khedma_theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .nav-active-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 2px;
            width: 100%;
            transform: translateY(1px);
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 flex flex-col min-h-screen antialiased transition-colors duration-200">

    <!-- ======================================================== -->
    <!-- SPOTLIGHT FIXED APP HEADER (CLIENT & PROVIDER ONLY)       -->
    <!-- ======================================================== -->
    @if(!request()->is('admin*'))
    <header class="fixed top-0 left-0 right-0 z-50 border-b border-slate-200/80 dark:border-slate-800 bg-white/85 dark:bg-slate-900/85 backdrop-blur-md shadow-sm transition-colors duration-200">
        <div class="mx-auto flex h-16 items-center px-4 md:max-w-7xl justify-between">

            <!-- Left: Mobile Menu Trigger + Brand Logo -->
            <div class="flex items-center gap-3">
                <!-- Mobile Drawer Trigger Button -->
                <button type="button" onclick="toggleMobileMenu()" class="lg:hidden p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition relative">
                    <i class="bi bi-list text-xl"></i>
                    <!-- Notification dot on mobile hamburger -->
                    <span id="mobileUnreadDot" class="hidden absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-red-500"></span>
                </button>

                <!-- Brand Logo (Spotlight Style) -->
                <a href="/" class="flex items-center gap-2.5 group">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-amber-500 flex items-center justify-center text-white font-extrabold text-lg shadow-md shadow-blue-500/15 group-hover:scale-105 transition">
                        K
                    </div>
                    <div class="flex flex-col">
                        <span class="text-lg font-black tracking-tight text-slate-900 dark:text-white leading-none">KHEDMA</span>
                        <span class="text-[9px] font-bold tracking-widest text-blue-600 dark:text-blue-400 uppercase mt-0.5">خدمة • PROS</span>
                    </div>
                </a>
            </div>

            <!-- Middle: Spotlight Navigation Menu with Active Underline Bar -->
            <nav class="hidden lg:flex items-center h-full space-x-1 ml-6">
                <!-- Discovery / Home (Hidden for Logged In Users) -->
                <div class="relative flex h-full items-center" id="navHomeItem">
                    <a href="/" @class([
                        'h-full flex items-center gap-2 px-3.5 text-xs font-bold transition',
                        'text-slate-900 dark:text-white font-extrabold' => request()->is('/'),
                        'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' => !request()->is('/'),
                    ])>
                        <i class="bi bi-house-door text-slate-400 dark:text-slate-500"></i>
                        Home
                    </a>
                    @if(request()->is('/'))
                        <div class="nav-active-bar bg-blue-600"></div>
                    @endif
                </div>

                <!-- Provider / Client Dynamic Tab 1: Dashboard -->
                <div class="relative flex h-full items-center" id="navDashboardItem" style="display: none;">
                    <a href="/dashboard" @class([
                        'h-full flex items-center gap-2 px-3.5 text-xs font-bold transition',
                        'text-slate-900 dark:text-white font-extrabold' => request()->is('dashboard'),
                        'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' => !request()->is('dashboard'),
                    ])>
                        <i class="bi bi-grid-fill text-slate-400 dark:text-slate-500"></i>
                        Dashboard
                    </a>
                    @if(request()->is('dashboard'))
                        <div class="nav-active-bar bg-amber-500"></div>
                    @endif
                </div>

                <!-- Find Services (Always available for guests, clients, and providers) -->
                <div class="relative flex h-full items-center" id="navFindServicesItem">
                    <a href="/services" @class([
                        'h-full flex items-center gap-2 px-3.5 text-xs font-bold transition',
                        'text-blue-600 dark:text-blue-400 font-extrabold' => request()->is('services*'),
                        'text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400' => !request()->is('services*'),
                    ])>
                        <i class="bi bi-search text-blue-500"></i>
                        Find Services
                    </a>
                    @if(request()->is('services*'))
                        <div class="nav-active-bar bg-blue-600"></div>
                    @endif
                </div>

                <!-- Find Providers (Always available for guests, clients, and providers) -->
                <div class="relative flex h-full items-center" id="navFindProvidersItem">
                    <a href="/providers" @class([
                        'h-full flex items-center gap-2 px-3.5 text-xs font-bold transition',
                        'text-blue-600 dark:text-blue-400 font-extrabold' => request()->is('providers*'),
                        'text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400' => !request()->is('providers*'),
                    ])>
                        <i class="bi bi-people-fill text-indigo-500"></i>
                        Find Providers
                    </a>
                    @if(request()->is('providers*'))
                        <div class="nav-active-bar bg-blue-600"></div>
                    @endif
                </div>

                <!-- Compare Pros Nav Tab -->
                <div class="relative flex h-full items-center" id="navCompareItem">
                    <a href="/compare" @class([
                        'h-full flex items-center gap-2 px-3.5 text-xs font-bold transition',
                        'text-amber-600 dark:text-amber-400 font-extrabold' => request()->is('compare*'),
                        'text-slate-600 dark:text-slate-300 hover:text-amber-600 dark:hover:text-amber-400' => !request()->is('compare*'),
                    ])>
                        <i class="bi bi-layout-split text-amber-500"></i>
                        <span>Compare</span>
                        <span id="navCompareBadge" class="hidden px-1.5 py-0.5 rounded-full text-[9px] font-extrabold bg-amber-500 text-white">0</span>
                    </a>
                    @if(request()->is('compare*'))
                        <div class="nav-active-bar bg-amber-500"></div>
                    @endif
                </div>

                <!-- How It Works (visible for everyone) -->
                <div class="relative hidden xl:flex h-full items-center">
                    <a href="{{ route('how-it-works') }}" @class([
                        'h-full flex items-center gap-2 px-3.5 text-xs font-bold transition',
                        'text-blue-600 dark:text-blue-400 font-extrabold' => request()->is('how-it-works'),
                        'text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400' => !request()->is('how-it-works'),
                    ])>
                        <i class="bi bi-lightbulb text-amber-500"></i>
                        How It Works
                    </a>
                    @if(request()->is('how-it-works'))
                        <div class="nav-active-bar bg-blue-600"></div>
                    @endif
                </div>

                <!-- FAQ (visible for everyone) -->
                <div class="relative hidden xl:flex h-full items-center">
                    <a href="{{ route('faq') }}" @class([
                        'h-full flex items-center gap-2 px-3.5 text-xs font-bold transition',
                        'text-blue-600 dark:text-blue-400 font-extrabold' => request()->is('faq'),
                        'text-slate-600 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400' => !request()->is('faq'),
                    ])>
                        <i class="bi bi-question-circle text-indigo-500"></i>
                        FAQ
                    </a>
                    @if(request()->is('faq'))
                        <div class="nav-active-bar bg-blue-600"></div>
                    @endif
                </div>

                <!-- Provider Dynamic Tab 2: Bookings & Leads -->
                <div class="relative flex h-full items-center" id="navBookingsItem" style="display: none;">
                    <a href="/provider/bookings" @class([
                        'h-full flex items-center gap-2 px-3.5 text-xs font-bold transition',
                        'text-slate-900 dark:text-white font-extrabold' => (request()->is('provider/bookings*') || request()->is('dashboard/bookings*')),
                        'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' => !(request()->is('provider/bookings*') || request()->is('dashboard/bookings*')),
                    ])>
                        <i class="bi bi-calendar-check-fill text-slate-400 dark:text-slate-500"></i>
                        Bookings & Leads
                        <span id="navPendingBadge" class="hidden px-1.5 py-0.5 rounded-full text-[9px] font-extrabold bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300">1</span>
                    </a>
                    @if(request()->is('provider/bookings*') || request()->is('dashboard/bookings*'))
                        <div class="nav-active-bar bg-amber-500"></div>
                    @endif
                </div>

                <!-- Provider Dynamic Tab 3: My Services -->
                <div class="relative flex h-full items-center" id="navServicesItem" style="display: none;">
                    <a href="/provider/services" @class([
                        'h-full flex items-center gap-2 px-3.5 text-xs font-bold transition',
                        'text-slate-900 dark:text-white font-extrabold' => (request()->is('provider/services*') || request()->is('dashboard/services*')),
                        'text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white' => !(request()->is('provider/services*') || request()->is('dashboard/services*')),
                    ])>
                        <i class="bi bi-tools text-slate-400 dark:text-slate-500"></i>
                        My Services
                    </a>
                    @if(request()->is('provider/services*') || request()->is('dashboard/services*'))
                        <div class="nav-active-bar bg-amber-500"></div>
                    @endif
                </div>

                <!-- Guest link: Become a Provider -->
                <div class="relative flex h-full items-center" id="navGuestBecomeProvider">
                    <a href="/register?role=provider" class="h-full flex items-center gap-2 px-3.5 text-xs font-bold text-slate-600 dark:text-slate-300 hover:text-amber-600 dark:hover:text-amber-400 transition">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Become a Provider
                    </a>
                </div>
            </nav>

            <!-- Right: Spotlight User Controls / Auth Buttons + Theme Toggle -->
            <div class="flex items-center gap-2 sm:gap-2.5">
                
                <!-- LIGHT / DARK MODE TOGGLE BUTTON -->
                <button type="button" onclick="toggleTheme()" title="Toggle Dark/Light Mode"
                    class="h-9 w-9 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800/90 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 flex items-center justify-center transition cursor-pointer shadow-sm">
                    <i class="bi bi-sun-fill text-amber-500 theme-icon-sun hidden text-sm"></i>
                    <i class="bi bi-moon-stars-fill text-indigo-400 theme-icon-moon text-sm"></i>
                </button>

                <!-- LOGGED IN USER CONTROLS -->
                <div id="authHeaderControls" class="hidden flex items-center gap-2">
                    
                    <!-- Notifications Dropdown (Spotlight Style) -->
                    <div class="relative">
                        <button type="button" onclick="toggleNotificationsMenu()"
                            class="h-9 w-9 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white flex items-center justify-center transition relative">
                            <i class="bi bi-bell-fill text-sm"></i>
                            <!-- Badge -->
                            <span class="absolute -top-1 -right-1 h-4 min-w-4 px-1 rounded-full bg-red-500 text-white font-extrabold text-[9px] flex items-center justify-center border border-white dark:border-slate-900">
                                2
                            </span>
                        </button>

                        <!-- Notification Dropdown Menu -->
                        <div id="notificationsMenu" class="hidden absolute right-0 mt-2 w-72 bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 p-3 space-y-2 text-xs z-50">
                            <div class="flex items-center justify-between pb-2 border-b border-slate-100 dark:border-slate-800">
                                <span class="font-extrabold text-slate-900 dark:text-white">Notifications</span>
                                <span class="text-[10px] text-blue-600 dark:text-blue-400 font-bold cursor-pointer hover:underline">Mark all read</span>
                            </div>
                            <div class="space-y-1.5">
                                <div class="p-2 rounded-xl bg-amber-50/70 dark:bg-amber-950/40 border border-amber-200/50 dark:border-amber-800/40">
                                    <div class="font-bold text-amber-900 dark:text-amber-300">New Booking Request</div>
                                    <p class="text-[11px] text-amber-800 dark:text-amber-400">Mariem Ben Salem requested AC Diagnostics.</p>
                                </div>
                                <div class="p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800">
                                    <div class="font-bold text-slate-800 dark:text-slate-200">5-Star Review Received</div>
                                    <p class="text-[11px] text-slate-600 dark:text-slate-400">Amina Trabelsi left feedback on Sousse plumbing.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Messages Dropdown -->
                    <div class="relative hidden sm:block">
                        <button type="button" onclick="alert('Live chat coming soon with WebSocket / Reverb!')"
                            class="h-9 w-9 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white flex items-center justify-center transition relative">
                            <i class="bi bi-chat-dots-fill text-sm"></i>
                        </button>
                    </div>

                    <!-- User Profile Dropdown Pill (Spotlight Style) -->
                    <div class="relative ml-1">
                        <button type="button" onclick="toggleUserDropdown()"
                            class="flex items-center gap-2 p-1.5 pl-2.5 rounded-full border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700/80 transition cursor-pointer">
                            <div class="flex flex-col text-left leading-tight hidden sm:block pr-1">
                                <span id="headerUserName" class="text-xs font-bold text-slate-900 dark:text-white truncate max-w-[110px]">User</span>
                                <span id="headerUserRole" class="text-[10px] font-semibold text-amber-600 dark:text-amber-400">Provider</span>
                            </div>
                            <div id="headerUserAvatar" class="w-7 h-7 rounded-full bg-gradient-to-tr from-amber-500 to-orange-600 text-white font-bold text-xs flex items-center justify-center shadow-sm">
                                Y
                            </div>
                        </button>

                        <!-- User Dropdown Menu -->
                        <div id="userDropdownMenu" class="hidden absolute right-0 mt-2 w-56 bg-white dark:bg-slate-900 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-800 p-2 text-xs z-50 space-y-1">
                            <div class="px-3 py-2 border-b border-slate-100 dark:border-slate-800">
                                <span class="font-bold text-slate-900 dark:text-white block truncate" id="dropdownUserEmail">user@khedma.tn</span>
                                <span class="text-[10px] text-slate-400">Authenticated via Sanctum</span>
                            </div>
                            <a href="/profile" class="flex items-center gap-2 px-3 py-2 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 font-bold transition">
                                <i class="bi bi-person-circle text-sm text-amber-500"></i>
                                My Profile
                            </a>
                            <a href="/dashboard" class="flex items-center gap-2 px-3 py-2 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium transition">
                                <i class="bi bi-speedometer2 text-sm text-slate-500"></i>
                                My Dashboard
                            </a>
                            <a id="dropdownPublicProfileLink" href="#" class="hidden flex items-center gap-2 px-3 py-2 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium transition">
                                <i class="bi bi-eye text-sm text-blue-500"></i>
                                Public Profile Preview
                            </a>
                            <a id="dropdownFavoritesLink" href="/dashboard#favorites" class="hidden flex items-center gap-2 px-3 py-2 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 font-medium transition">
                                <i class="bi bi-heart-fill text-sm text-rose-500"></i>
                                Favorite Providers
                            </a>
                            <div class="border-t border-slate-100 dark:border-slate-800 my-1"></div>
                            <button onclick="handleLogoutGlobal()" class="w-full flex items-center gap-2 px-3 py-2 rounded-xl text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 font-bold transition text-left">
                                <i class="bi bi-box-arrow-right text-sm"></i>
                                Sign Out
                            </button>
                        </div>
                    </div>

                    <!-- Square Logout Button (Spotlight Style) -->
                    <button type="button" onclick="handleLogoutGlobal()" title="Sign Out"
                        class="h-9 w-9 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-800 hover:bg-red-50 dark:hover:bg-red-950/30 hover:border-red-200 dark:hover:border-red-900/40 text-slate-500 dark:text-slate-400 hover:text-red-600 dark:hover:text-red-400 flex items-center justify-center transition">
                        <i class="bi bi-box-arrow-right text-sm"></i>
                    </button>
                </div>

                <!-- GUEST AUTH BUTTONS -->
                <div id="guestHeaderControls" class="flex items-center gap-2">
                    <a href="/login" class="text-xs font-bold text-slate-700 dark:text-slate-200 hover:text-blue-600 dark:hover:text-blue-400 px-3 py-2 rounded-xl transition">
                        Sign In
                    </a>
                    <a href="/register" class="text-xs font-extrabold bg-blue-600 hover:bg-blue-700 text-white px-3.5 py-2 rounded-xl shadow-sm shadow-blue-500/20 transition flex items-center gap-1.5">
                        <span>Create Account</span>
                        <i class="bi bi-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>

        </div>
    </header>

    <!-- Header Spacer to prevent layout shift under fixed header -->
    <div class="h-16"></div>
    @endif

    <!-- ======================================================== -->
    <!-- SPOTLIGHT MOBILE OFF-CANVAS SHEET DRAWER                 -->
    <!-- ======================================================== -->
    <div id="mobileDrawerBackdrop" onclick="toggleMobileMenu()" class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>
    <div id="mobileDrawer" class="fixed top-0 bottom-0 left-0 z-50 w-72 bg-white dark:bg-slate-900 shadow-2xl p-6 flex flex-col justify-between transform -translate-x-full transition-transform duration-300 ease-in-out border-r border-slate-200 dark:border-slate-800">
        <div class="space-y-6">
            <!-- Drawer Header -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800">
                <a href="/" class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-blue-600 to-amber-500 flex items-center justify-center text-white font-extrabold text-base">
                        K
                    </div>
                    <span class="font-extrabold text-base text-slate-900 dark:text-white">KHEDMA</span>
                </a>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="toggleTheme()" class="p-2 rounded-xl text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800">
                        <i class="bi bi-sun-fill text-amber-500 theme-icon-sun hidden"></i>
                        <i class="bi bi-moon-stars-fill text-indigo-400 theme-icon-moon"></i>
                    </button>
                    <button type="button" onclick="toggleMobileMenu()" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                        <i class="bi bi-x-lg text-sm"></i>
                    </button>
                </div>
            </div>

            <!-- Drawer Navigation Items -->
            <nav class="space-y-1.5 text-xs font-bold">
                <!-- Home (Shown for guest, hidden for logged in) -->
                <a href="/" id="mobileNavHome" @class([
                    'flex items-center gap-3 px-3 py-2.5 rounded-xl transition',
                    'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white' => request()->is('/'),
                    'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' => !request()->is('/'),
                ])>
                    <i class="bi bi-house-door text-slate-500"></i>
                    Home
                </a>

                <!-- Dashboard (Shown for logged in users) -->
                <a href="/dashboard" id="mobileNavDashboard" @class([
                    'hidden flex items-center justify-between px-3 py-2.5 rounded-xl transition',
                    'bg-amber-50 dark:bg-amber-950/40 text-amber-900 dark:text-amber-300 font-extrabold' => request()->is('dashboard'),
                    'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' => !request()->is('dashboard'),
                ])>
                    <div class="flex items-center gap-3">
                        <i class="bi bi-grid-fill text-slate-500"></i>
                        Dashboard
                    </div>
                </a>

                <!-- Find Services (Always available) -->
                <a href="/services" id="mobileNavServices" @class([
                    'flex items-center gap-3 px-3 py-2.5 rounded-xl transition',
                    'bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 font-extrabold' => request()->is('services*'),
                    'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' => !request()->is('services*'),
                ])>
                    <i class="bi bi-search text-blue-500"></i>
                    Find Services
                </a>

                <!-- Find Providers (Always available) -->
                <a href="/providers" id="mobileNavProviders" @class([
                    'flex items-center gap-3 px-3 py-2.5 rounded-xl transition',
                    'bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 font-extrabold' => request()->is('providers*'),
                    'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' => !request()->is('providers*'),
                ])>
                    <i class="bi bi-people-fill text-indigo-500"></i>
                    Find Providers
                </a>

                <!-- Compare Pros (Always available) -->
                <a href="/compare" id="mobileNavCompare" @class([
                    'flex items-center justify-between px-3 py-2.5 rounded-xl transition',
                    'bg-amber-50 dark:bg-amber-950/40 text-amber-900 dark:text-amber-300 font-extrabold' => request()->is('compare*'),
                    'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' => !request()->is('compare*'),
                ])>
                    <div class="flex items-center gap-3">
                        <i class="bi bi-layout-split text-amber-500"></i>
                        Compare Pros
                    </div>
                    <span id="mobileCompareBadge" class="hidden px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500 text-white">0</span>
                </a>

                <!-- How It Works (Always available) -->
                <a href="{{ route('how-it-works') }}" @class([
                    'flex items-center gap-3 px-3 py-2.5 rounded-xl transition',
                    'bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 font-extrabold' => request()->is('how-it-works'),
                    'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' => !request()->is('how-it-works'),
                ])>
                    <i class="bi bi-lightbulb text-amber-500"></i>
                    How It Works
                </a>

                <!-- FAQ (Always available) -->
                <a href="{{ route('faq') }}" @class([
                    'flex items-center gap-3 px-3 py-2.5 rounded-xl transition',
                    'bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 font-extrabold' => request()->is('faq'),
                    'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' => !request()->is('faq'),
                ])>
                    <i class="bi bi-question-circle text-indigo-500"></i>
                    FAQ
                </a>

                <!-- Provider: Bookings & Leads -->
                <a href="/provider/bookings" id="mobileNavBookings" @class([
                    'hidden flex items-center justify-between px-3 py-2.5 rounded-xl transition',
                    'bg-amber-50 dark:bg-amber-950/40 text-amber-900 dark:text-amber-300 font-extrabold' => (request()->is('provider/bookings*') || request()->is('dashboard/bookings*')),
                    'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' => !(request()->is('provider/bookings*') || request()->is('dashboard/bookings*')),
                ])>
                    <div class="flex items-center gap-3">
                        <i class="bi bi-calendar-check-fill text-slate-500"></i>
                        Bookings & Leads
                    </div>
                    <span id="mobilePendingBadge" class="hidden px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 dark:bg-amber-900/60 text-amber-800 dark:text-amber-300">1</span>
                </a>

                <!-- Provider: My Services -->
                <a href="/provider/services" id="mobileNavMyServices" @class([
                    'hidden flex items-center gap-3 px-3 py-2.5 rounded-xl transition',
                    'bg-amber-50 dark:bg-amber-950/40 text-amber-900 dark:text-amber-300 font-extrabold' => (request()->is('provider/services*') || request()->is('dashboard/services*')),
                    'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' => !(request()->is('provider/services*') || request()->is('dashboard/services*')),
                ])>
                    <i class="bi bi-tools text-slate-500"></i>
                    My Services
                </a>

                <!-- My Profile (Shown for logged in users) -->
                <a href="/profile" id="mobileNavProfile" @class([
                    'hidden flex items-center gap-3 px-3 py-2.5 rounded-xl transition',
                    'bg-amber-50 dark:bg-amber-950/40 text-amber-900 dark:text-amber-300 font-extrabold' => request()->is('profile*'),
                    'text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800' => !request()->is('profile*'),
                ])>
                    <i class="bi bi-person-circle text-slate-500"></i>
                    My Profile
                </a>

                <!-- Guest: Become a Provider -->
                <a href="/register?role=provider" id="mobileNavBecomeProvider" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Become a Provider
                </a>
            </nav>
        </div>

        <!-- Drawer Footer (Auth or Logout) -->
        <div class="border-t border-slate-100 dark:border-slate-800 pt-4" id="mobileDrawerFooter">
            <!-- Injected dynamically via script -->
        </div>
    </div>

    <!-- Main Content Slot -->
    <main class="flex-1">
        @yield('content')
    </main>

    <!-- ======================================================== -->
    <!-- FLOATING CLIENT COMPARISON DOCK                          -->
    <!-- ======================================================== -->
    <div id="floatingCompareBar" class="hidden fixed bottom-6 left-1/2 -translate-x-1/2 z-50 w-[94%] max-w-lg bg-slate-900/95 dark:bg-slate-800/95 backdrop-blur-md text-white p-3.5 rounded-3xl shadow-2xl border border-slate-700/60 transition-all duration-300">
        <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <div id="compareBarAvatars" class="flex items-center -space-x-2 shrink-0">
                    <!-- Dynamic avatars -->
                </div>
                <div class="min-w-0">
                    <span class="text-xs font-black block truncate" id="compareBarTitle">Comparing Providers</span>
                    <span class="text-[10px] text-slate-300 block truncate" id="compareBarSub">Select up to 4 pros</span>
                </div>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <button type="button" onclick="clearCompareList()" class="px-2.5 py-1.5 rounded-xl text-slate-400 hover:text-white text-xs font-bold transition">
                    Clear
                </button>
                <a id="btnGoCompare" href="/compare" class="px-4 py-2 rounded-2xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs shadow-md transition flex items-center gap-1.5">
                    <span>Compare Now</span>
                    <span id="compareBarCountPill" class="w-4 h-4 rounded-full bg-slate-950 text-white text-[9px] flex items-center justify-center font-bold">2</span>
                </a>
            </div>
        </div>
    </div>



    <!-- Header State & Dropdowns Script -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            initHeaderState();
        });

        function initHeaderState() {
            const token = localStorage.getItem('khedma_token');
            const userStr = localStorage.getItem('khedma_user');

            const guestControls = document.getElementById('guestHeaderControls');
            const authControls = document.getElementById('authHeaderControls');
            const navHome = document.getElementById('navHomeItem');
            const navDashboard = document.getElementById('navDashboardItem');
            const navFindProviders = document.getElementById('navFindProvidersItem');
            const navCompare = document.getElementById('navCompareItem');
            const navBookings = document.getElementById('navBookingsItem');
            const navServices = document.getElementById('navServicesItem');
            const navGuestBecomeProvider = document.getElementById('navGuestBecomeProvider');

            const mobileNavHome = document.getElementById('mobileNavHome');
            const mobileNavDashboard = document.getElementById('mobileNavDashboard');
            const mobileNavProviders = document.getElementById('mobileNavProviders');
            const mobileNavCompare = document.getElementById('mobileNavCompare');
            const mobileNavBookings = document.getElementById('mobileNavBookings');
            const mobileNavMyServices = document.getElementById('mobileNavMyServices');
            const mobileNavProfile = document.getElementById('mobileNavProfile');
            const mobileNavBecomeProvider = document.getElementById('mobileNavBecomeProvider');
            const mobileFooter = document.getElementById('mobileDrawerFooter');

            if (token && userStr) {
                try {
                    const user = JSON.parse(userStr);
                    guestControls.classList.add('hidden');
                    authControls.classList.remove('hidden');

                    document.getElementById('headerUserName').textContent = user.name;
                    document.getElementById('headerUserAvatar').textContent = user.name.charAt(0).toUpperCase();
                    document.getElementById('dropdownUserEmail').textContent = user.email;

                    // Public profile preview link for providers
                    const pubLink = document.getElementById('dropdownPublicProfileLink');
                    if (pubLink) {
                        if (user.role === 'provider' && user.id) {
                            pubLink.href = '/providers/' + user.id;
                            pubLink.classList.remove('hidden');
                        } else {
                            pubLink.classList.add('hidden');
                        }
                    }

                    // Favorites link for clients
                    const favLink = document.getElementById('dropdownFavoritesLink');
                    if (favLink) {
                        if (user.role === 'client') {
                            favLink.classList.remove('hidden');
                        } else {
                            favLink.classList.add('hidden');
                        }
                    }

                    // Hide Home link from logged in users
                    if (navHome) navHome.style.display = 'none';
                    if (mobileNavHome) mobileNavHome.style.display = 'none';

                    // Show Dashboard
                    if (navDashboard) navDashboard.style.display = 'flex';
                    if (mobileNavDashboard) mobileNavDashboard.classList.remove('hidden');

                    // Show Profile link in mobile drawer
                    if (mobileNavProfile) mobileNavProfile.classList.remove('hidden');

                    if (user.role === 'provider') {
                        document.getElementById('headerUserRole').textContent = '🛠️ Provider';
                        document.getElementById('headerUserRole').className = 'text-[10px] font-bold text-amber-600 uppercase';
                        
                        // Show Provider links
                        if (navBookings) navBookings.style.display = 'flex';
                        if (navServices) navServices.style.display = 'flex';
                        if (mobileNavBookings) mobileNavBookings.classList.remove('hidden');
                        if (mobileNavMyServices) mobileNavMyServices.classList.remove('hidden');

                        // Hide Compare and Find Providers for providers
                        if (navFindProviders) navFindProviders.style.display = 'none';
                        if (navCompare) navCompare.style.display = 'none';
                        if (mobileNavProviders) mobileNavProviders.classList.add('hidden');
                        if (mobileNavCompare) mobileNavCompare.classList.add('hidden');

                        if (navGuestBecomeProvider) navGuestBecomeProvider.style.display = 'none';
                        if (mobileNavBecomeProvider) mobileNavBecomeProvider.style.display = 'none';

                        // Badge indicator
                        const badge = document.getElementById('navPendingBadge');
                        const mobBadge = document.getElementById('mobilePendingBadge');
                        if (badge) badge.classList.remove('hidden');
                        if (mobBadge) mobBadge.classList.remove('hidden');
                    } else {
                        document.getElementById('headerUserRole').textContent = '👤 Client';
                        document.getElementById('headerUserRole').className = 'text-[10px] font-bold text-blue-600 uppercase';

                        // Show Find Providers and Compare for clients
                        if (navFindProviders) navFindProviders.style.display = 'flex';
                        if (navCompare) navCompare.style.display = 'flex';
                        if (mobileNavProviders) mobileNavProviders.classList.remove('hidden');
                        if (mobileNavCompare) mobileNavCompare.classList.remove('hidden');

                        // Hide Provider specific tabs for client
                        if (navBookings) navBookings.style.display = 'none';
                        if (navServices) navServices.style.display = 'none';
                        if (mobileNavBookings) mobileNavBookings.classList.add('hidden');
                        if (mobileNavMyServices) mobileNavMyServices.classList.add('hidden');

                        if (navGuestBecomeProvider) navGuestBecomeProvider.style.display = 'none';
                        if (mobileNavBecomeProvider) mobileNavBecomeProvider.style.display = 'none';
                    }

                    if (mobileFooter) {
                        mobileFooter.innerHTML = `
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-8 h-8 rounded-full bg-amber-500 text-white font-bold text-xs flex items-center justify-center">
                                    ${user.name.charAt(0).toUpperCase()}
                                </div>
                                <div class="truncate">
                                    <div class="text-xs font-bold text-slate-900 dark:text-white">${user.name}</div>
                                    <div class="text-[10px] text-slate-400 truncate">${user.email}</div>
                                </div>
                            </div>
                            <button onclick="handleLogoutGlobal()" class="w-full py-2 bg-red-50 dark:bg-red-950/40 text-red-600 dark:text-red-400 font-bold text-xs rounded-xl flex items-center justify-center gap-2 border border-red-200 dark:border-red-900/50">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Logout</span>
                            </button>
                        `;
                    }
                } catch(e) {
                    console.error(e);
                }
            } else {
                guestControls.classList.remove('hidden');
                authControls.classList.add('hidden');

                // Show Home, Find Providers, and Compare for guests
                if (navHome) navHome.style.display = 'flex';
                if (mobileNavHome) mobileNavHome.style.display = 'flex';
                if (navFindProviders) navFindProviders.style.display = 'flex';
                if (navCompare) navCompare.style.display = 'flex';
                if (mobileNavProviders) mobileNavProviders.classList.remove('hidden');
                if (mobileNavCompare) mobileNavCompare.classList.remove('hidden');

                if (navDashboard) navDashboard.style.display = 'none';
                if (mobileNavDashboard) mobileNavDashboard.classList.add('hidden');
                if (navBookings) navBookings.style.display = 'none';
                if (mobileNavBookings) mobileNavBookings.classList.add('hidden');
                if (navServices) navServices.style.display = 'none';
                if (mobileNavMyServices) mobileNavMyServices.classList.add('hidden');
                if (mobileNavProfile) mobileNavProfile.classList.add('hidden');
                if (navGuestBecomeProvider) navGuestBecomeProvider.style.display = 'flex';
                if (mobileNavBecomeProvider) mobileNavBecomeProvider.style.display = 'flex';

                if (mobileFooter) {
                    mobileFooter.innerHTML = `
                        <div class="grid grid-cols-2 gap-2">
                            <a href="/login" class="text-center py-2 bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-200 font-bold text-xs rounded-xl">Sign In</a>
                            <a href="/register" class="text-center py-2 bg-blue-600 text-white font-bold text-xs rounded-xl shadow-sm">Sign Up</a>
                        </div>
                    `;
                }
            }
        }

        function toggleUserDropdown() {
            const menu = document.getElementById('userDropdownMenu');
            menu.classList.toggle('hidden');
            document.getElementById('notificationsMenu').classList.add('hidden');
        }

        function toggleNotificationsMenu() {
            const menu = document.getElementById('notificationsMenu');
            menu.classList.toggle('hidden');
            document.getElementById('userDropdownMenu').classList.add('hidden');
        }

        // Close dropdowns when clicking outside
        window.addEventListener('click', (e) => {
            if (!e.target.closest('#userDropdownMenu') && !e.target.closest('button[onclick="toggleUserDropdown()"]')) {
                const u = document.getElementById('userDropdownMenu');
                if (u) u.classList.add('hidden');
            }
            if (!e.target.closest('#notificationsMenu') && !e.target.closest('button[onclick="toggleNotificationsMenu()"]')) {
                const n = document.getElementById('notificationsMenu');
                if (n) n.classList.add('hidden');
            }
        });

        function toggleMobileMenu() {
            const drawer = document.getElementById('mobileDrawer');
            const backdrop = document.getElementById('mobileDrawerBackdrop');
            drawer.classList.toggle('-translate-x-full');
            backdrop.classList.toggle('hidden');
        }

        async function handleLogoutGlobal() {
            const token = localStorage.getItem('khedma_token');
            if (token) {
                try {
                    await fetch('/api/v1/auth/logout', {
                        method: 'POST',
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Accept': 'application/json'
                        }
                    });
                } catch(e) {}
            }
            localStorage.removeItem('khedma_token');
            localStorage.removeItem('khedma_user');
            window.location.href = '/login';
        }

        function toggleTheme() {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.setItem('khedma_theme', isDark ? 'dark' : 'light');
            updateThemeIcons();
        }

        function updateThemeIcons() {
            const isDark = document.documentElement.classList.contains('dark');
            document.querySelectorAll('.theme-icon-sun').forEach(el => el.classList.toggle('hidden', !isDark));
            document.querySelectorAll('.theme-icon-moon').forEach(el => el.classList.toggle('hidden', isDark));
        }

        // ==========================================
        // CLIENT SIDE COMPARISON DOCK MANAGEMENT
        // ==========================================
        function getCompareList() {
            try {
                return JSON.parse(localStorage.getItem('khedma_compare') || '[]');
            } catch (e) {
                return [];
            }
        }

        function setCompareList(list) {
            localStorage.setItem('khedma_compare', JSON.stringify(list));
            updateCompareDockUI();
            if (typeof updateCardCompareButtons === 'function') {
                updateCardCompareButtons();
            }
        }

        function toggleCompareProvider(id, name, initial, category, rate) {
            let list = getCompareList();
            const numericId = parseInt(id, 10);
            const idx = list.findIndex(p => parseInt(p.id, 10) === numericId);

            if (idx >= 0) {
                list.splice(idx, 1);
            } else {
                if (list.length >= 4) {
                    alert('You can compare a maximum of 4 providers at a time.');
                    return;
                }
                list.push({
                    id: numericId,
                    name: name,
                    initial: initial || name.charAt(0).toUpperCase(),
                    category: category || 'Contractor',
                    rate: rate || ''
                });
            }
            setCompareList(list);
        }

        function clearCompareList() {
            setCompareList([]);
        }

        function updateCompareDockUI() {
            // Providers never see comparison dock or badges
            try {
                const user = JSON.parse(localStorage.getItem('khedma_user') || '{}');
                if (user && user.role === 'provider') {
                    const bar = document.getElementById('floatingCompareBar');
                    if (bar) bar.classList.add('hidden');
                    const navBadge = document.getElementById('navCompareBadge');
                    if (navBadge) navBadge.classList.add('hidden');
                    const mobileBadge = document.getElementById('mobileCompareBadge');
                    if (mobileBadge) mobileBadge.classList.add('hidden');
                    return;
                }
            } catch(e) {}

            const list = getCompareList();
            const bar = document.getElementById('floatingCompareBar');
            const navBadge = document.getElementById('navCompareBadge');
            const mobileBadge = document.getElementById('mobileCompareBadge');

            if (navBadge) {
                navBadge.textContent = list.length;
                navBadge.classList.toggle('hidden', list.length === 0);
            }
            if (mobileBadge) {
                mobileBadge.textContent = list.length;
                mobileBadge.classList.toggle('hidden', list.length === 0);
            }

            if (!bar) return;

            if (list.length === 0) {
                bar.classList.add('hidden');
                return;
            }

            bar.classList.remove('hidden');
            const avatarContainer = document.getElementById('compareBarAvatars');
            if (avatarContainer) {
                avatarContainer.innerHTML = list.map(p => `
                    <div title="${p.name} (${p.category})" class="w-8 h-8 rounded-full bg-gradient-to-tr from-amber-500 to-orange-600 text-white font-black text-xs flex items-center justify-center border-2 border-slate-900 shadow">
                        ${p.initial || p.name.charAt(0).toUpperCase()}
                    </div>
                `).join('');
            }

            const countPill = document.getElementById('compareBarCountPill');
            if (countPill) countPill.textContent = list.length;

            const title = document.getElementById('compareBarTitle');
            if (title) title.textContent = `Comparing ${list.length} Provider${list.length > 1 ? 's' : ''}`;

            const sub = document.getElementById('compareBarSub');
            if (sub) {
                sub.textContent = list.length < 2 ? 'Select at least 1 more to compare' : 'Ready to evaluate side-by-side!';
            }

            const btnGo = document.getElementById('btnGoCompare');
            if (btnGo) {
                btnGo.href = `/compare?ids=${list.map(p => p.id).join(',')}`;
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            updateThemeIcons();
            updateCompareDockUI();
        });
    </script>
    @yield('scripts')
</body>
</html>
