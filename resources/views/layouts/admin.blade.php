<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Operations Console | KHEDMA')</title>
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
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        /* Custom scrollbar for admin console */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #0f172a;
        }
        ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 9999px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }
    </style>
</head>
<body class="h-full bg-slate-950 text-slate-100 flex overflow-hidden antialiased">

    <!-- ======================================================== -->
    <!-- MOBILE SIDEBAR BACKDROP & DRAWER                         -->
    <!-- ======================================================== -->
    <div id="adminMobileDrawerBackdrop" onclick="toggleAdminSidebar()"
        class="hidden fixed inset-0 z-40 bg-black/70 backdrop-blur-xs transition-opacity lg:hidden"></div>

    <!-- ======================================================== -->
    <!-- DEDICATED ADMIN SIDEBAR                                  -->
    <!-- ======================================================== -->
    <aside id="adminSidebar"
        class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-950 border-r border-slate-800/80 flex flex-col justify-between transform -translate-x-full transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 shrink-0">
        
        <!-- Top Section: Logo & Navigation Links -->
        <div class="flex flex-col h-full overflow-y-auto">
            
            <!-- Sidebar Header / Brand Logo -->
            <div class="p-6 border-b border-slate-800/80 flex items-center justify-between">
                <a href="/admin/dashboard" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-red-600 via-rose-600 to-indigo-600 flex items-center justify-center text-white font-extrabold text-xl shadow-lg shadow-red-500/20 group-hover:scale-105 transition">
                        🛡️
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-1.5">
                            <span class="text-base font-black tracking-tight text-white leading-none">KHEDMA</span>
                            <span class="text-[9px] font-black uppercase tracking-wider px-1.5 py-0.5 rounded-md bg-red-500/20 text-red-400 border border-red-500/30">ADMIN</span>
                        </div>
                        <span class="text-[10px] font-bold tracking-widest text-slate-400 uppercase mt-1">Operations Console</span>
                    </div>
                </a>

                <!-- Mobile Close Button -->
                <button onclick="toggleAdminSidebar()" class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Platform Cluster Status Pill -->
            <div class="px-5 pt-5 pb-2">
                <div class="p-3 rounded-2xl bg-slate-900/80 border border-slate-800 flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2">
                        <span class="relative flex h-2.5 w-2.5">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                        </span>
                        <span class="text-slate-300 font-semibold text-[11px]">Tunisia Cluster (TN)</span>
                    </div>
                    <span class="text-[10px] font-black px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">LIVE</span>
                </div>
            </div>

            <!-- Sidebar Navigation Menu -->
            <nav class="flex-1 px-4 py-3 space-y-6 text-xs font-semibold">
                
                <!-- Section 1: Main Operations -->
                <div>
                    <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 block mb-2">Main Operations</span>
                    <div class="space-y-1">
                        <!-- Dashboard Overview -->
                        <a href="/admin/dashboard" onclick="selectAdminSection('overview')" id="navLinkOverview"
                            class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-white bg-slate-800/80 border border-slate-700/60 font-bold transition">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                                <span>Overview</span>
                            </div>
                            <span class="text-[10px] px-2 py-0.5 rounded-md bg-slate-900 text-slate-400">KPIs</span>
                        </a>

                        <!-- Users Management -->
                        <a href="#sectionUsers" onclick="selectAdminSection('users')" id="navLinkUsers"
                            class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-slate-400 hover:text-white hover:bg-slate-900 border border-transparent hover:border-slate-800 transition">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                <span>Users & Moderation</span>
                            </div>
                            <span id="sidebarUserCountBadge" class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-blue-500/20 text-blue-400">0</span>
                        </a>

                        <!-- Platform Bookings -->
                        <a href="#sectionBookings" onclick="selectAdminSection('bookings')" id="navLinkBookings"
                            class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-slate-400 hover:text-white hover:bg-slate-900 border border-transparent hover:border-slate-800 transition">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                <span>Platform Bookings</span>
                            </div>
                            <span id="sidebarBookingsCountBadge" class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-amber-500/20 text-amber-400">0</span>
                        </a>

                        <!-- Services Catalog -->
                        <a href="#overview" onclick="selectAdminSection('overview')" id="navLinkServices"
                            class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-slate-400 hover:text-white hover:bg-slate-900 border border-transparent hover:border-slate-800 transition">
                            <div class="flex items-center gap-3">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                <span>Services Catalog</span>
                            </div>
                            <span id="sidebarServicesBadge" class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-emerald-500/20 text-emerald-400">Active</span>
                        </a>
                    </div>
                </div>

                <!-- Section 2: Financials & System -->
                <div>
                    <span class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 block mb-2">Financials & System</span>
                    <div class="space-y-1">
                        <!-- Revenue / Financials -->
                        <div class="px-3.5 py-3 rounded-xl bg-slate-900/60 border border-slate-800">
                            <div class="flex items-center justify-between text-slate-400 text-[11px] mb-1">
                                <span>Platform Volume (TND)</span>
                                <span class="text-emerald-400 font-bold">100% Settled</span>
                            </div>
                            <div class="flex items-baseline gap-1.5">
                                <span id="sidebarGMVDisplay" class="text-lg font-black text-white">0</span>
                                <span class="text-[10px] font-bold text-slate-400">TND</span>
                            </div>
                        </div>

                        <!-- System Security & Logs -->
                        <div class="px-3.5 py-2.5 rounded-xl text-slate-400 flex items-center justify-between text-[11px]">
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                <span>Sanctum RBAC Guard</span>
                            </span>
                            <span class="text-[9px] px-1.5 py-0.5 rounded bg-indigo-500/20 text-indigo-300 font-mono">Enforced</span>
                        </div>
                    </div>
                </div>

            </nav>

        </div>

        <!-- Bottom Section: Admin Profile Card & Logout -->
        <div class="p-4 border-t border-slate-800/80 bg-slate-950/80 space-y-3">
            
            <!-- Admin Profile Details -->
            <div class="flex items-center gap-3 p-2.5 rounded-2xl bg-slate-900 border border-slate-800">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-red-600 to-indigo-600 text-white font-extrabold text-sm flex items-center justify-center shadow-md">
                    <span id="adminSidebarAvatar">A</span>
                </div>
                <div class="flex flex-col min-w-0 flex-1">
                    <div class="flex items-center justify-between">
                        <span id="adminSidebarName" class="text-xs font-bold text-white truncate">Administrator</span>
                        <span class="text-[9px] font-bold text-red-400 uppercase tracking-wider">Super Admin</span>
                    </div>
                    <span id="adminSidebarEmail" class="text-[10px] text-slate-400 truncate">admin@khedma.tn</span>
                </div>
            </div>

            <!-- Action Buttons: Public Marketplace + Sign Out -->
            <div class="grid grid-cols-2 gap-2 text-xs">
                <a href="/" target="_blank"
                    class="p-2 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-300 hover:text-white transition flex items-center justify-center gap-1.5 font-bold">
                    <span>Marketplace</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
                <button type="button" onclick="handleLogoutGlobal()"
                    class="p-2 rounded-xl bg-red-600/10 hover:bg-red-600/20 border border-red-500/20 text-red-400 hover:text-red-300 transition flex items-center justify-center gap-1.5 font-bold">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span>Logout</span>
                </button>
            </div>

        </div>

    </aside>

    <!-- ======================================================== -->
    <!-- MAIN ADMIN CONTENT WRAPPER                               -->
    <!-- ======================================================== -->
    <div class="flex-1 flex flex-col h-screen overflow-hidden bg-slate-900">
        
        <!-- Admin Top Navigation Bar (NO Standard Consumer Menu) -->
        <header class="h-16 bg-slate-950/80 backdrop-blur-md border-b border-slate-800/80 px-4 sm:px-6 flex items-center justify-between shrink-0 z-30">
            
            <!-- Left: Mobile Sidebar Trigger + Page Title -->
            <div class="flex items-center gap-3">
                <button onclick="toggleAdminSidebar()" class="lg:hidden p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>

                <div class="flex items-center gap-2">
                    <h1 class="text-sm sm:text-base font-extrabold text-white tracking-tight">Admin Operations Console</h1>
                    <span class="hidden sm:inline-block text-[10px] font-bold text-slate-500">•</span>
                    <span class="hidden sm:inline-block text-xs text-slate-400 font-medium">Tunisia Platform Governance</span>
                </div>
            </div>

            <!-- Right: Search, Status & Quick Actions -->
            <div class="flex items-center gap-3">
                <!-- Search Box -->
                <div class="relative hidden md:block w-64">
                    <input type="text" id="adminGlobalSearch" onkeyup="handleAdminGlobalSearch(this.value)"
                        placeholder="Search users, bookings..."
                        class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-1.5 pl-8 text-xs text-slate-200 placeholder-slate-500 focus:outline-none focus:border-red-500/50">
                    <svg class="w-3.5 h-3.5 text-slate-500 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <!-- Refresh Button -->
                <button type="button" onclick="refreshAdminData()" title="Refresh Data"
                    class="p-2 rounded-xl bg-slate-900 border border-slate-800 text-slate-400 hover:text-white hover:border-slate-700 transition">
                    <svg id="refreshSpinner" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </button>

                <!-- Admin Sign Out Button -->
                <button type="button" onclick="handleLogoutGlobal()"
                    class="px-3 py-1.5 rounded-xl bg-red-600/20 hover:bg-red-600/30 text-red-300 hover:text-white border border-red-500/30 text-xs font-bold transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
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

        function selectAdminSection(section) {
            // Update active state in sidebar
            const overviewLink = document.getElementById('navLinkOverview');
            const usersLink = document.getElementById('navLinkUsers');
            const bookingsLink = document.getElementById('navLinkBookings');

            [overviewLink, usersLink, bookingsLink].forEach(link => {
                if (link) {
                    link.className = 'flex items-center justify-between px-3.5 py-2.5 rounded-xl text-slate-400 hover:text-white hover:bg-slate-900 border border-transparent hover:border-slate-800 transition';
                }
            });

            if (section === 'overview' && overviewLink) {
                overviewLink.className = 'flex items-center justify-between px-3.5 py-2.5 rounded-xl text-white bg-slate-800/80 border border-slate-700/60 font-bold transition';
                if (window.switchAdminTab) window.switchAdminTab('users');
            } else if (section === 'users' && usersLink) {
                usersLink.className = 'flex items-center justify-between px-3.5 py-2.5 rounded-xl text-white bg-slate-800/80 border border-slate-700/60 font-bold transition';
                if (window.switchAdminTab) window.switchAdminTab('users');
            } else if (section === 'bookings' && bookingsLink) {
                bookingsLink.className = 'flex items-center justify-between px-3.5 py-2.5 rounded-xl text-white bg-slate-800/80 border border-slate-700/60 font-bold transition';
                if (window.switchAdminTab) window.switchAdminTab('bookings');
            }

            // Close mobile sidebar if open
            if (window.innerWidth < 1024) {
                toggleAdminSidebar();
            }
        }

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
            // Filter users table
            const userRows = document.querySelectorAll('#usersTableBody tr');
            userRows.forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });

            // Filter bookings table
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
    </script>

    @yield('scripts')
</body>
</html>
