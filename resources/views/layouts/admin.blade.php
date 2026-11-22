<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Operations Console | KHEDMA')</title>
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
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 9999px;
        }
        .dark ::-webkit-scrollbar-thumb {
            background: #334155;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        .dark ::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }
    </style>
</head>
<body class="h-full bg-slate-100 dark:bg-slate-900 text-slate-900 dark:text-slate-100 flex overflow-hidden antialiased transition-colors duration-200">

    <!-- ======================================================== -->
    <!-- MOBILE SIDEBAR BACKDROP & DRAWER                         -->
    <!-- ======================================================== -->
    <div id="adminMobileDrawerBackdrop" onclick="toggleAdminSidebar()"
        class="hidden fixed inset-0 z-40 bg-slate-900/60 dark:bg-black/70 backdrop-blur-xs transition-opacity lg:hidden"></div>

    <!-- ======================================================== -->
    <!-- DEDICATED ADMIN SIDEBAR                                  -->
    <!-- ======================================================== -->
    <aside id="adminSidebar"
        class="fixed inset-y-0 left-0 z-50 w-72 bg-white dark:bg-slate-950 border-r border-slate-200 dark:border-slate-800 flex flex-col justify-between transform -translate-x-full transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 shrink-0 shadow-lg lg:shadow-none">
        
        <!-- Top Section: Logo & Navigation Links -->
        <div class="flex flex-col h-full overflow-y-auto">
            
            <!-- Sidebar Header / Brand Logo -->
            <div class="p-6 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <a href="/admin/dashboard" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-red-600 via-rose-600 to-indigo-600 flex items-center justify-center text-white font-extrabold text-xl shadow-lg shadow-red-500/20 group-hover:scale-105 transition">
                        <i class="bi bi-shield-shaded text-white text-lg"></i>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-1.5">
                            <span class="text-base font-black tracking-tight text-slate-900 dark:text-white leading-none">KHEDMA</span>
                            <span class="text-[9px] font-black uppercase tracking-wider px-1.5 py-0.5 rounded-md bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-500/30">ADMIN</span>
                        </div>
                        <span class="text-[10px] font-bold tracking-widest text-slate-500 dark:text-slate-400 uppercase mt-1">Operations Console</span>
                    </div>
                </a>

                <!-- Mobile Close Button -->
                <button onclick="toggleAdminSidebar()" class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800">
                    <i class="bi bi-x-lg text-base"></i>
                </button>
            </div>

            <!-- Platform Cluster Status Pill -->
            <div class="px-5 pt-5 pb-2">
                <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-900/80 border border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                        </span>
                        <span class="text-slate-700 dark:text-slate-300 font-semibold text-[11px]">Tunisia Cluster (TN)</span>
                    </div>
                    <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/30">LIVE</span>
                </div>
            </div>

            <!-- Sidebar Navigation Menu -->
            <nav class="flex-1 px-4 py-3 space-y-6 text-xs font-semibold">
                @php
                    $isOverview = request()->is('admin/dashboard') || request()->is('admin');
                    $isUsers = request()->is('admin/users*');
                    $isBookings = request()->is('admin/bookings*');
                    $isServices = request()->is('admin/services*') || request()->is('admin/catalog*');
                    $isReports = request()->is('admin/reports*');

                    $activeClass = 'flex items-center justify-between px-3.5 py-2.5 rounded-xl text-slate-900 dark:text-white bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700/60 font-bold transition';
                    $inactiveClass = 'flex items-center justify-between px-3.5 py-2.5 rounded-xl text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-900 border border-transparent hover:border-slate-200 dark:hover:border-slate-800 transition';
                @endphp

                <!-- Section 1: Main Operations -->
                <div>
                    <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 block mb-2">Main Operations</span>
                    <div class="space-y-1">
                        <!-- Dashboard Overview -->
                        <a href="/admin/dashboard" id="navLinkOverview"
                            class="{{ $isOverview ? $activeClass : $inactiveClass }}">
                            <div class="flex items-center gap-3">
                                <i class="bi bi-speedometer2 text-base text-red-500 dark:text-red-400"></i>
                                <span>Overview</span>
                            </div>
                            <span class="text-[10px] px-2 py-0.5 rounded-md bg-white dark:bg-slate-900 text-slate-500 dark:text-slate-400 shadow-2xs">KPIs</span>
                        </a>

                        <!-- Users Management -->
                        <a href="/admin/users" id="navLinkUsers"
                            class="{{ $isUsers ? $activeClass : $inactiveClass }}">
                            <div class="flex items-center gap-3">
                                <i class="bi bi-people-fill text-base text-blue-500"></i>
                                <span>Users & Moderation</span>
                            </div>
                            <span id="sidebarUserCountBadge" class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-400">0</span>
                        </a>

                        <!-- Platform Bookings -->
                        <a href="/admin/bookings" id="navLinkBookings"
                            class="{{ $isBookings ? $activeClass : $inactiveClass }}">
                            <div class="flex items-center gap-3">
                                <i class="bi bi-calendar2-check-fill text-base text-amber-500"></i>
                                <span>Platform Bookings</span>
                            </div>
                            <span id="sidebarBookingsCountBadge" class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-amber-100 dark:bg-amber-500/20 text-amber-700 dark:text-amber-400">0</span>
                        </a>

                        <!-- Services Catalog -->
                        <a href="/admin/services" id="navLinkServices"
                            class="{{ $isServices ? $activeClass : $inactiveClass }}">
                            <div class="flex items-center gap-3">
                                <i class="bi bi-tools text-base text-emerald-500"></i>
                                <span>Services Catalog</span>
                            </div>
                            <span id="sidebarServicesBadge" class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400">Active</span>
                        </a>

                        <a href="{{ route('admin.reports') }}" id="navLinkReports"
                            class="{{ $isReports ? $activeClass : $inactiveClass }}">
                            <div class="flex items-center gap-3">
                                <i class="bi bi-flag-fill text-base text-red-500"></i>
                                <span>Reports & Issues</span>
                            </div>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-400">Review</span>
                        </a>
                    </div>
                </div>

                <!-- Section 2: Financials & System -->
                <div>
                    <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 block mb-2">Financials & System</span>
                    <div class="space-y-1">
                        <!-- Revenue / Financials -->
                        <div class="px-3.5 py-3 rounded-xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800">
                            <div class="flex items-center justify-between text-slate-500 dark:text-slate-400 text-[11px] mb-1">
                                <span class="flex items-center gap-1.5"><i class="bi bi-cash-stack text-emerald-600 dark:text-emerald-400"></i> Volume (TND)</span>
                                <span class="text-emerald-600 dark:text-emerald-400 font-bold">100% Settled</span>
                            </div>
                            <div class="flex items-baseline gap-1.5">
                                <span id="sidebarGMVDisplay" class="text-lg font-black text-slate-900 dark:text-white">0</span>
                                <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400">TND</span>
                            </div>
                        </div>

                        <!-- System Security & Logs -->
                        <div class="px-3.5 py-2.5 rounded-xl text-slate-600 dark:text-slate-400 flex items-center justify-between text-[11px]">
                            <span class="flex items-center gap-2">
                                <i class="bi bi-shield-lock-fill text-indigo-500 dark:text-indigo-400"></i>
                                <span>Sanctum RBAC Guard</span>
                            </span>
                            <span class="text-[9px] px-1.5 py-0.5 rounded bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-300 font-mono">Enforced</span>
                        </div>
                    </div>
                </div>

            </nav>

        </div>

        <!-- Bottom Section: Admin Profile Card & Logout -->
        <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/80 space-y-3">
            
            <!-- Admin Profile Details -->
            <div class="flex items-center gap-3 p-2.5 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 shadow-2xs">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-red-600 to-indigo-600 text-white font-extrabold text-sm flex items-center justify-center shadow-md">
                    <span id="adminSidebarAvatar">A</span>
                </div>
                <div class="flex flex-col min-w-0 flex-1">
                    <div class="flex items-center justify-between">
                        <span id="adminSidebarName" class="text-xs font-bold text-slate-900 dark:text-white truncate">Administrator</span>
                        <span class="text-[9px] font-bold text-red-600 dark:text-red-400 uppercase tracking-wider">Super Admin</span>
                    </div>
                    <span id="adminSidebarEmail" class="text-[10px] text-slate-500 dark:text-slate-400 truncate">admin@khedma.tn</span>
                </div>
            </div>

            <!-- Action Buttons: Public Marketplace + Sign Out -->
            <div class="grid grid-cols-2 gap-2 text-xs">
                <a href="/" target="_blank"
                    class="p-2 rounded-xl bg-white dark:bg-slate-900 hover:bg-slate-100 dark:hover:bg-slate-800 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white transition flex items-center justify-center gap-1.5 font-bold shadow-2xs">
                    <span>Marketplace</span>
                    <i class="bi bi-box-arrow-up-right text-xs"></i>
                </a>
                <button type="button" onclick="handleLogoutGlobal()"
                    class="p-2 rounded-xl bg-red-50 dark:bg-red-600/10 hover:bg-red-100 dark:hover:bg-red-600/20 border border-red-200 dark:border-red-500/20 text-red-600 dark:text-red-400 transition flex items-center justify-center gap-1.5 font-bold">
                    <i class="bi bi-box-arrow-right text-xs"></i>
                    <span>Logout</span>
                </button>
            </div>

        </div>

    </aside>

    <!-- ======================================================== -->
    <!-- MAIN ADMIN CONTENT WRAPPER                               -->
    <!-- ======================================================== -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-100 dark:bg-slate-900 transition-colors duration-200">
        
        <!-- Admin Top Navigation Bar -->
        <header class="h-16 bg-white/90 dark:bg-slate-950/80 backdrop-blur-md border-b border-slate-200 dark:border-slate-800 px-4 sm:px-6 flex items-center justify-between shrink-0 z-30 transition-colors duration-200">
            
            <!-- Left: Mobile Sidebar Trigger + Page Title -->
            <div class="flex items-center gap-3">
                <button onclick="toggleAdminSidebar()" class="lg:hidden p-2 rounded-xl text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <i class="bi bi-list text-xl"></i>
                </button>

                <div class="flex items-center gap-2">
                    <h1 class="text-sm sm:text-base font-extrabold text-slate-900 dark:text-white tracking-tight">Admin Operations Console</h1>
                    <span class="hidden sm:inline-block text-[10px] font-bold text-slate-400">•</span>
                    <span class="hidden sm:inline-block text-xs text-slate-500 dark:text-slate-400 font-medium">Tunisia Platform Governance</span>
                </div>
            </div>

            <!-- Right: Search, Status & Theme Toggle & Quick Actions -->
            <div class="flex items-center gap-2 sm:gap-3">
                <!-- Search Box -->
                <div class="relative hidden md:block w-64">
                    <input type="text" id="adminGlobalSearch" onkeyup="handleAdminGlobalSearch(this.value)"
                        placeholder="Search users, bookings..."
                        class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-1.5 pl-8 text-xs text-slate-900 dark:text-slate-200 placeholder-slate-400 dark:placeholder-slate-500 focus:outline-none focus:border-red-500/50">
                    <i class="bi bi-search text-xs text-slate-400 dark:text-slate-500 absolute left-2.5 top-2.5"></i>
                </div>

                <!-- Theme Toggle Button -->
                <button type="button" onclick="toggleTheme()" title="Toggle Dark/Light Mode"
                    class="p-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800 transition cursor-pointer shadow-2xs">
                    <i class="bi bi-sun-fill text-amber-500 theme-icon-sun hidden text-sm"></i>
                    <i class="bi bi-moon-stars-fill text-indigo-400 theme-icon-moon text-sm"></i>
                </button>

                <!-- Refresh Button -->
                <button type="button" onclick="refreshAdminData()" title="Refresh Data"
                    class="p-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:border-slate-300 dark:hover:border-slate-700 transition shadow-2xs">
                    <i id="refreshSpinner" class="bi bi-arrow-repeat text-sm"></i>
                </button>

                <!-- Admin Sign Out Button -->
                <button type="button" onclick="handleLogoutGlobal()"
                    class="px-3 py-1.5 rounded-xl bg-red-50 dark:bg-red-600/20 hover:bg-red-100 dark:hover:bg-red-600/30 text-red-600 dark:text-red-300 hover:text-red-700 dark:hover:text-white border border-red-200 dark:border-red-500/30 text-xs font-bold transition flex items-center gap-1.5 shadow-2xs">
                    <i class="bi bi-box-arrow-right text-xs"></i>
                    <span class="hidden sm:inline">Logout</span>
                </button>
            </div>

        </header>

        <!-- Main Scrollable Content Area -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 space-y-8">
            @yield('content')
        </main>

    </div>

    <!-- Admin Global Scripts -->
    <script>
        function toggleAdminSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const backdrop = document.getElementById('adminMobileDrawerBackdrop');
            if (sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            } else {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }
        }

        // Keep sidebar metrics synchronized across all admin pages
        document.addEventListener('DOMContentLoaded', async () => {
            const token = localStorage.getItem('khedma_token');
            const userStr = localStorage.getItem('khedma_user');
            if (!token || !userStr) return;

            try {
                const u = JSON.parse(userStr);
                if (document.getElementById('adminSidebarName')) document.getElementById('adminSidebarName').textContent = u.name;
                if (document.getElementById('adminSidebarEmail')) document.getElementById('adminSidebarEmail').textContent = u.email;
                if (document.getElementById('adminSidebarAvatar')) document.getElementById('adminSidebarAvatar').textContent = (u.name || 'A').charAt(0).toUpperCase();

                const res = await fetch('/api/v1/admin/dashboard', {
                    headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
                });
                if (res.ok) {
                    const data = await res.json();
                    const m = data.metrics || {};
                    if (document.getElementById('sidebarUserCountBadge')) document.getElementById('sidebarUserCountBadge').textContent = m.total_users ?? 0;
                    if (document.getElementById('sidebarBookingsCountBadge')) document.getElementById('sidebarBookingsCountBadge').textContent = m.total_bookings ?? 0;
                    if (document.getElementById('sidebarGMVDisplay')) document.getElementById('sidebarGMVDisplay').textContent = (m.platform_gmv ?? 0).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
                }
            } catch(e) {}
        });

        async function handleLogoutGlobal() {
            const token = localStorage.getItem('khedma_token');
            if (token) {
                try {
                    await fetch('/api/v1/auth/logout', {
                        method: 'POST',
                        headers: {
                            'Authorization': 'Bearer ' + token,
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });
                } catch(e) {}
            }
            localStorage.removeItem('khedma_token');
            localStorage.removeItem('khedma_user');
            window.location.href = '/login?admin=1';
        }

        function handleAdminGlobalSearch(query) {
            query = (query || '').toLowerCase().trim();
            const userRows = document.querySelectorAll('#usersTableBody tr');
            userRows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });

            const bookingRows = document.querySelectorAll('#adminBookingsTableBody tr');
            bookingRows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        }

        async function refreshAdminData() {
            const spinner = document.getElementById('refreshSpinner');
            if (spinner) spinner.classList.add('animate-spin');
            const token = localStorage.getItem('khedma_token');
            if (token && window.loadAdminDashboard) {
                await window.loadAdminDashboard(token);
            }
            setTimeout(() => {
                if (spinner) spinner.classList.remove('animate-spin');
            }, 500);
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

        document.addEventListener('DOMContentLoaded', () => {
            updateThemeIcons();
        });
    </script>

    @yield('scripts')
</body>
</html>
