<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Khedma | Service Marketplace')</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
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
<body class="bg-slate-50 text-slate-800 flex flex-col min-h-screen antialiased">

    <!-- ======================================================== -->
    <!-- SPOTLIGHT FIXED APP HEADER (CLIENT & PROVIDER ONLY)       -->
    <!-- ======================================================== -->
    @if(!request()->is('admin*'))
    <header class="fixed top-0 left-0 right-0 z-50 border-b border-slate-200/80 bg-white/85 backdrop-blur-md shadow-xs">
        <div class="mx-auto flex h-16 items-center px-4 md:max-w-7xl justify-between">

            <!-- Left: Mobile Menu Trigger + Brand Logo -->
            <div class="flex items-center gap-3">
                <!-- Mobile Drawer Trigger Button -->
                <button type="button" onclick="toggleMobileMenu()" class="lg:hidden p-2 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition relative">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <!-- Notification dot on mobile hamburger -->
                    <span id="mobileUnreadDot" class="hidden absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-red-500"></span>
                </button>

                <!-- Brand Logo (Spotlight Style) -->
                <a href="/" class="flex items-center gap-2.5 group">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-amber-500 flex items-center justify-center text-white font-extrabold text-lg shadow-md shadow-blue-500/15 group-hover:scale-105 transition">
                        K
                    </div>
                    <div class="flex flex-col">
                        <span class="text-lg font-black tracking-tight text-slate-900 leading-none">KHEDMA</span>
                        <span class="text-[9px] font-bold tracking-widest text-blue-600 uppercase mt-0.5">خدمة • PROS</span>
                    </div>
                </a>
            </div>

            <!-- Middle: Spotlight Navigation Menu with Active Underline Bar -->
            <nav class="hidden lg:flex items-center h-full space-x-1 ml-6">
                <!-- Discovery / Home -->
                <div class="relative flex h-full items-center">
                    <a href="/" class="h-full flex items-center gap-1.5 px-3.5 text-xs font-bold text-slate-600 hover:text-slate-900 transition {{ request()->is('/') ? 'text-slate-900 font-extrabold' : '' }}">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        Discovery
                    </a>
                    @if(request()->is('/'))
                        <div class="nav-active-bar bg-blue-600"></div>
                    @endif
                </div>

                <!-- Provider / Client Dynamic Tab 1: Dashboard -->
                <div class="relative flex h-full items-center" id="navDashboardItem" style="display: none;">
                    <a href="/dashboard" class="h-full flex items-center gap-1.5 px-3.5 text-xs font-bold text-slate-600 hover:text-slate-900 transition {{ request()->is('dashboard*') ? 'text-slate-900 font-extrabold' : '' }}">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        Dashboard
                    </a>
                    @if(request()->is('dashboard*'))
                        <div class="nav-active-bar bg-amber-500"></div>
                    @endif
                </div>

                <!-- Provider Dynamic Tab 2: Bookings / Leads -->
                <div class="relative flex h-full items-center" id="navBookingsItem" style="display: none;">
                    <a href="/dashboard#bookingsTableBody" class="h-full flex items-center gap-1.5 px-3.5 text-xs font-bold text-slate-600 hover:text-slate-900 transition">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Bookings & Leads
                        <span id="navPendingBadge" class="hidden px-1.5 py-0.2 rounded-full text-[9px] font-extrabold bg-amber-100 text-amber-800">1</span>
                    </a>
                </div>

                <!-- Provider Dynamic Tab 3: Services Catalog -->
                <div class="relative flex h-full items-center" id="navServicesItem" style="display: none;">
                    <a href="/dashboard#servicesGrid" class="h-full flex items-center gap-1.5 px-3.5 text-xs font-bold text-slate-600 hover:text-slate-900 transition">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                        My Services
                    </a>
                </div>

                <!-- Guest link: Become a Provider -->
                <div class="relative flex h-full items-center" id="navGuestBecomeProvider">
                    <a href="/register?role=provider" class="h-full flex items-center gap-1.5 px-3.5 text-xs font-bold text-slate-600 hover:text-amber-600 transition">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Become a Provider
                    </a>
                </div>
            </nav>

            <!-- Right: Spotlight User Controls / Auth Buttons -->
            <div class="flex items-center gap-2.5">
                <!-- LOGGED IN USER CONTROLS -->
                <div id="authHeaderControls" class="hidden flex items-center gap-2">
                    
                    <!-- Notifications Dropdown (Spotlight Style) -->
                    <div class="relative">
                        <button type="button" onclick="toggleNotificationsMenu()"
                            class="h-9 w-9 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 hover:text-slate-900 flex items-center justify-center transition relative">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            <!-- Badge -->
                            <span class="absolute -top-1 -right-1 h-4 min-w-4 px-1 rounded-full bg-red-500 text-white font-extrabold text-[9px] flex items-center justify-center border border-white">
                                2
                            </span>
                        </button>

                        <!-- Notification Dropdown Menu -->
                        <div id="notificationsMenu" class="hidden absolute right-0 mt-2 w-72 bg-white rounded-2xl shadow-xl border border-slate-200 p-3 space-y-2 text-xs z-50">
                            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                                <span class="font-extrabold text-slate-900">Notifications</span>
                                <span class="text-[10px] text-blue-600 font-bold cursor-pointer hover:underline">Mark all read</span>
                            </div>
                            <div class="space-y-1.5">
                                <div class="p-2 rounded-xl bg-amber-50/70 border border-amber-200/50">
                                    <div class="font-bold text-amber-900">New Booking Request</div>
                                    <p class="text-[11px] text-amber-800">Fatima Zahra requested Pipe Leak Diagnostics.</p>
                                </div>
                                <div class="p-2 rounded-xl bg-slate-50 border border-slate-100">
                                    <div class="font-bold text-slate-800">5-Star Review Received</div>
                                    <p class="text-[11px] text-slate-600">Amina Bensouda left feedback on Stopcock repair.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Messages Dropdown -->
                    <div class="relative hidden sm:block">
                        <button type="button" onclick="alert('Live chat coming soon with WebSocket / Reverb!')"
                            class="h-9 w-9 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 hover:text-slate-900 flex items-center justify-center transition relative">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        </button>
                    </div>

                    <!-- User Profile Dropdown Pill (Spotlight Style) -->
                    <div class="relative ml-1">
                        <button type="button" onclick="toggleUserDropdown()"
                            class="flex items-center gap-2 p-1.5 pl-2.5 rounded-full border border-slate-200 hover:border-slate-300 bg-white hover:bg-slate-50 transition cursor-pointer">
                            <div class="flex flex-col text-left leading-tight hidden sm:block pr-1">
                                <span id="headerUserName" class="text-xs font-bold text-slate-900 truncate max-w-[110px]">User</span>
                                <span id="headerUserRole" class="text-[10px] font-semibold text-amber-600">Provider</span>
                            </div>
                            <div id="headerUserAvatar" class="w-7 h-7 rounded-full bg-gradient-to-tr from-amber-500 to-orange-600 text-white font-bold text-xs flex items-center justify-center shadow-xs">
                                Y
                            </div>
                        </button>

                        <!-- User Dropdown Menu -->
                        <div id="userDropdownMenu" class="hidden absolute right-0 mt-2 w-52 bg-white rounded-2xl shadow-xl border border-slate-200 p-2 text-xs z-50 space-y-1">
                            <div class="px-3 py-2 border-b border-slate-100">
                                <span class="font-bold text-slate-900 block truncate" id="dropdownUserEmail">user@khedma.tn</span>
                                <span class="text-[10px] text-slate-400">Authenticated via Sanctum</span>
                            </div>
                            <a href="/dashboard" class="flex items-center gap-2 px-3 py-2 rounded-xl text-slate-700 hover:bg-slate-100 font-medium transition">
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                My Dashboard
                            </a>
                            <div class="border-t border-slate-100 my-1"></div>
                            <button onclick="handleLogoutGlobal()" class="w-full flex items-center gap-2 px-3 py-2 rounded-xl text-red-600 hover:bg-red-50 font-bold transition text-left">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Sign Out
                            </button>
                        </div>
                    </div>

                    <!-- Square Logout Button (Spotlight Style) -->
                    <button type="button" onclick="handleLogoutGlobal()" title="Sign Out"
                        class="h-9 w-9 rounded-xl border border-slate-200 bg-white hover:bg-red-50 hover:border-red-200 text-slate-500 hover:text-red-600 flex items-center justify-center transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </div>

                <!-- GUEST AUTH BUTTONS -->
                <div id="guestHeaderControls" class="flex items-center gap-2">
                    <a href="/login" class="text-xs font-bold text-slate-700 hover:text-blue-600 px-3 py-2 rounded-xl transition">
                        Sign In
                    </a>
                    <a href="/register" class="text-xs font-extrabold bg-blue-600 hover:bg-blue-700 text-white px-3.5 py-2 rounded-xl shadow-xs shadow-blue-500/20 transition flex items-center gap-1.5">
                        <span>Create Account</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
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
    <div id="mobileDrawerBackdrop" onclick="toggleMobileMenu()" class="hidden fixed inset-0 z-50 bg-slate-900/50 backdrop-blur-xs transition-opacity"></div>
    <div id="mobileDrawer" class="fixed top-0 bottom-0 left-0 z-50 w-72 bg-white shadow-2xl p-6 flex flex-col justify-between transform -translate-x-full transition-transform duration-300 ease-in-out">
        <div class="space-y-6">
            <!-- Drawer Header -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <a href="/" class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-blue-600 to-amber-500 flex items-center justify-center text-white font-extrabold text-base">
                        K
                    </div>
                    <span class="font-extrabold text-base text-slate-900">KHEDMA</span>
                </a>
                <button onclick="toggleMobileMenu()" class="p-1 rounded-lg text-slate-400 hover:text-slate-600">✕</button>
            </div>

            <!-- Drawer Navigation Items -->
            <nav class="space-y-1.5 text-xs font-bold">
                <a href="/" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-700 hover:bg-slate-100 transition {{ request()->is('/') ? 'bg-slate-100 text-slate-900' : '' }}">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    Discovery & Services
                </a>
                <a href="/dashboard" id="mobileNavDashboard" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-slate-700 hover:bg-slate-100 transition {{ request()->is('dashboard*') ? 'bg-amber-50 text-amber-900' : '' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        Dashboard
                    </div>
                    <span id="mobilePendingBadge" class="hidden px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">1</span>
                </a>
                <a href="/register?role=provider" id="mobileNavBecomeProvider" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-700 hover:bg-slate-100 transition">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Become a Provider
                </a>
            </nav>
        </div>

        <!-- Drawer Footer (Auth or Logout) -->
        <div class="border-t border-slate-100 pt-4" id="mobileDrawerFooter">
            <!-- Injected dynamically via script -->
        </div>
    </div>

    <!-- Main Content Slot -->
    <main class="flex-1">
        @yield('content')
    </main>



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
            const navDashboard = document.getElementById('navDashboardItem');
            const navBookings = document.getElementById('navBookingsItem');
            const navServices = document.getElementById('navServicesItem');
            const navGuestBecomeProvider = document.getElementById('navGuestBecomeProvider');
            const mobileFooter = document.getElementById('mobileDrawerFooter');

            if (token && userStr) {
                try {
                    const user = JSON.parse(userStr);
                    guestControls.classList.add('hidden');
                    authControls.classList.remove('hidden');

                    document.getElementById('headerUserName').textContent = user.name;
                    document.getElementById('headerUserAvatar').textContent = user.name.charAt(0).toUpperCase();
                    document.getElementById('dropdownUserEmail').textContent = user.email;

                    // Show dashboard links in main nav
                    navDashboard.style.display = 'flex';

                    if (user.role === 'provider') {
                        document.getElementById('headerUserRole').textContent = '🛠️ Provider';
                        document.getElementById('headerUserRole').className = 'text-[10px] font-bold text-amber-600 uppercase';
                        navBookings.style.display = 'flex';
                        navServices.style.display = 'flex';
                        navGuestBecomeProvider.style.display = 'none';

                        // Badge indicator
                        const badge = document.getElementById('navPendingBadge');
                        const mobBadge = document.getElementById('mobilePendingBadge');
                        if (badge) badge.classList.remove('hidden');
                        if (mobBadge) mobBadge.classList.remove('hidden');
                    } else {
                        document.getElementById('headerUserRole').textContent = '👤 Client';
                        document.getElementById('headerUserRole').className = 'text-[10px] font-bold text-blue-600 uppercase';
                    }

                    if (mobileFooter) {
                        mobileFooter.innerHTML = `
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-8 h-8 rounded-full bg-amber-500 text-white font-bold text-xs flex items-center justify-center">
                                    ${user.name.charAt(0).toUpperCase()}
                                </div>
                                <div class="truncate">
                                    <div class="text-xs font-bold text-slate-900">${user.name}</div>
                                    <div class="text-[10px] text-slate-400 truncate">${user.email}</div>
                                </div>
                            </div>
                            <button onclick="handleLogoutGlobal()" class="w-full py-2 bg-red-50 text-red-600 font-bold text-xs rounded-xl flex items-center justify-center gap-2 border border-red-200">
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
                navDashboard.style.display = 'none';
                navBookings.style.display = 'none';
                navServices.style.display = 'none';
                navGuestBecomeProvider.style.display = 'flex';

                if (mobileFooter) {
                    mobileFooter.innerHTML = `
                        <div class="grid grid-cols-2 gap-2">
                            <a href="/login" class="text-center py-2 bg-slate-100 text-slate-800 font-bold text-xs rounded-xl">Sign In</a>
                            <a href="/register" class="text-center py-2 bg-blue-600 text-white font-bold text-xs rounded-xl shadow-xs">Sign Up</a>
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
    </script>
    @yield('scripts')
</body>
</html>
