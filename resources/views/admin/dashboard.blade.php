@extends('layouts.admin')

@section('title', 'Admin Operations Console | KHEDMA')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    <!-- Loading skeleton -->
    <div id="loadingState" class="py-24 text-center">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-red-600 border-t-transparent mb-4"></div>
        <p class="text-sm text-slate-500 font-semibold">Verifying administrator privileges & loading system metrics...</p>
    </div>

    <!-- Unauthorized Banner if not admin -->
    <div id="unauthorizedBanner" class="hidden py-16 text-center max-w-md mx-auto space-y-4">
        <div class="w-16 h-16 rounded-3xl bg-red-100 text-red-600 flex items-center justify-center mx-auto font-black text-2xl">
            ✕
        </div>
        <h2 class="text-2xl font-black text-slate-900 tracking-tight">Access Restricted</h2>
        <p class="text-xs text-slate-500">
            This area requires Administrator permissions. Your current role is not authorized to view or manage this area.
        </p>
        <div class="pt-2 flex items-center justify-center gap-3">
            <a href="/dashboard" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold">Go to My Dashboard</a>
            <a href="/login?admin=1" class="px-4 py-2 bg-red-600 text-white rounded-xl text-xs font-bold">Sign In as Admin</a>
        </div>
    </div>

    <!-- Main Admin Container -->
    <div id="adminContent" class="hidden space-y-8">
        <!-- 1. Admin Command Hero Banner -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white p-8 md:p-10 shadow-xl border border-slate-800">
            <div class="absolute top-0 right-0 -mt-10 -mr-10 w-72 h-72 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-start sm:items-center gap-5">
                    <div class="w-16 h-16 rounded-2xl bg-red-500/20 border border-red-500/30 text-red-400 font-black text-2xl flex items-center justify-center shadow-lg shrink-0">
                        <i class="bi bi-shield-shaded text-2xl text-red-400"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5 flex-wrap">
                            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Admin Operations Center</h1>
                            <span class="text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded-full bg-red-500/20 text-red-400 border border-red-500/30">
                                Restricted Access
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">Platform governance, user management, and platform financials oversight.</p>
                        <div class="mt-2 flex items-center gap-4 text-xs text-slate-300 flex-wrap">
                            <span class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                Logged in as: <strong id="adminNameDisplay" class="text-white">Admin</strong>
                            </span>
                            <span class="text-slate-500">•</span>
                            <span id="adminEmailDisplay" class="text-slate-400">admin@khedma.tn</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button onclick="handleLogoutGlobal()" class="px-4 py-2.5 rounded-xl bg-red-600/20 hover:bg-red-600/30 text-red-300 hover:text-white border border-red-500/30 text-xs font-bold transition flex items-center gap-2">
                        <i class="bi bi-box-arrow-right text-sm"></i>
                        Logout Admin
                    </button>
                </div>
            </div>
        </div>

        <!-- 2. System KPI Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Platform GMV -->
            <div class="bg-white dark:bg-slate-950/80 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm transition-colors duration-200">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 flex items-center gap-1.5 mb-1">
                    <i class="bi bi-cash-stack text-emerald-500"></i> Platform GMV (Revenue)
                </span>
                <div class="flex items-baseline gap-2 mt-2">
                    <span id="statGMV" class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">0</span>
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400">TND</span>
                </div>
                <p class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold mt-2">Total volume completed</p>
            </div>

            <!-- Total Users -->
            <div class="bg-white dark:bg-slate-950/80 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm transition-colors duration-200">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 flex items-center gap-1.5 mb-1">
                    <i class="bi bi-people-fill text-blue-500"></i> Total Registered Users
                </span>
                <div class="flex items-baseline gap-2 mt-2">
                    <span id="statTotalUsers" class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">0</span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-semibold mt-2" id="statUsersBreakdown">0 Clients • 0 Providers</p>
                <div class="mt-2.5 pt-2.5 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-[11px]">
                    <span id="statVerifiedUsers" class="text-emerald-600 dark:text-emerald-400 font-bold flex items-center gap-1">
                        <i class="bi bi-patch-check-fill"></i> <span id="statVerifiedCountText">0 Verified</span>
                    </span>
                    <span id="statSuspendedUsers" class="text-red-500 dark:text-red-400 font-bold flex items-center gap-1">
                        <i class="bi bi-slash-circle-fill"></i> <span id="statSuspendedCountText">0 Suspended</span>
                    </span>
                </div>
            </div>

            <!-- Total Bookings -->
            <div class="bg-white dark:bg-slate-950/80 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm transition-colors duration-200">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 flex items-center gap-1.5 mb-1">
                    <i class="bi bi-calendar2-check-fill text-amber-500"></i> Total Bookings
                </span>
                <div class="flex items-baseline gap-2 mt-2">
                    <span id="statTotalBookings" class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">0</span>
                </div>
                <p class="text-xs text-blue-600 dark:text-blue-400 font-semibold mt-2" id="statBookingsBreakdown">0 Completed • 0 Active</p>
            </div>

            <!-- Active Services -->
            <div class="bg-white dark:bg-slate-950/80 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm transition-colors duration-200">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 flex items-center gap-1.5 mb-1">
                    <i class="bi bi-tools text-emerald-500"></i> Active Services Catalog
                </span>
                <div class="flex items-baseline gap-2 mt-2">
                    <span id="statServicesCount" class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">0</span>
                </div>
                <p class="text-xs text-amber-600 dark:text-amber-400 font-semibold mt-2">Services offered by pros</p>
            </div>
        </div>

        <!-- 3. Navigation Hub to Dedicated Management Pages -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Users & Moderation Page Card -->
            <a href="/admin/users" class="group bg-white dark:bg-slate-950 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-blue-500/50 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center text-xl group-hover:scale-105 transition">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <span class="text-xs font-bold text-blue-600 dark:text-blue-400 flex items-center gap-1 group-hover:translate-x-1 transition">
                        Open Page <i class="bi bi-arrow-right"></i>
                    </span>
                </div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white mb-1">Users & Moderation</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    Search, inspect profiles, toggle identity verification, suspend or permanently remove user accounts.
                </p>
            </a>

            <!-- Platform Bookings Page Card -->
            <a href="/admin/bookings" class="group bg-white dark:bg-slate-950 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-amber-500/50 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 flex items-center justify-center text-xl group-hover:scale-105 transition">
                        <i class="bi bi-calendar2-check-fill"></i>
                    </div>
                    <span class="text-xs font-bold text-amber-600 dark:text-amber-400 flex items-center gap-1 group-hover:translate-x-1 transition">
                        Open Page <i class="bi bi-arrow-right"></i>
                    </span>
                </div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white mb-1">Platform Bookings</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    Track all service agreements, schedules, payments, and client-provider interaction lifecycles.
                </p>
            </a>

            <!-- Services Catalog Page Card -->
            <a href="/admin/services" class="group bg-white dark:bg-slate-950 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-emerald-500/50 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-xl group-hover:scale-105 transition">
                        <i class="bi bi-tools"></i>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1 group-hover:translate-x-1 transition">
                        Open Page <i class="bi bi-arrow-right"></i>
                    </span>
                </div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white mb-1">Services Catalog</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    Create service categories and review, approve, reject, or moderate provider service listings.
                </p>
            </a>

            <a href="{{ route('admin.reports') }}" class="group bg-white dark:bg-slate-950 p-6 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs hover:border-red-500/50 hover:shadow-md transition">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 flex items-center justify-center text-xl group-hover:scale-105 transition">
                        <i class="bi bi-flag-fill"></i>
                    </div>
                    <span class="text-xs font-bold text-red-600 dark:text-red-400 flex items-center gap-1 group-hover:translate-x-1 transition">
                        Open Page <i class="bi bi-arrow-right"></i>
                    </span>
                </div>
                <h3 class="text-base font-extrabold text-slate-900 dark:text-white mb-1">Reports & Issues</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                    Review client reports, investigate platform issues, and update report statuses.
                </p>
            </a>
        </div>

        <!-- 4. Recent Users Preview -->
        <div class="bg-white dark:bg-slate-950 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden transition-colors duration-200">
            <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-extrabold text-slate-900 dark:text-white tracking-tight">Recent Users</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Latest community members registered on KHEDMA.</p>
                </div>
                <a href="/admin/users" class="px-4 py-2 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/60 text-xs font-bold transition flex items-center gap-1.5">
                    View & Moderate All Users <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                    <thead class="bg-slate-50 dark:bg-slate-900/60 text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800">
                        <tr>
                            <th class="py-3.5 px-6">User</th>
                            <th class="py-3.5 px-6">Role</th>
                            <th class="py-3.5 px-6">Verification</th>
                            <th class="py-3.5 px-6">Status</th>
                            <th class="py-3.5 px-6">Joined Date</th>
                        </tr>
                    </thead>
                    <tbody id="overviewUsersTableBody" class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        <!-- Populated dynamically -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 5. Recent Bookings Preview -->
        <div class="bg-white dark:bg-slate-950 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden transition-colors duration-200">
            <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-extrabold text-slate-900 dark:text-white tracking-tight">Recent Bookings</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Latest service agreements across the platform.</p>
                </div>
                <a href="/admin/bookings" class="px-4 py-2 rounded-xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-900/60 text-xs font-bold transition flex items-center gap-1.5">
                    View All Bookings <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                    <thead class="bg-slate-50 dark:bg-slate-900/60 text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800">
                        <tr>
                            <th class="py-3.5 px-6">Booking #</th>
                            <th class="py-3.5 px-6">Client</th>
                            <th class="py-3.5 px-6">Provider</th>
                            <th class="py-3.5 px-6">Service</th>
                            <th class="py-3.5 px-6">Amount</th>
                            <th class="py-3.5 px-6">Status</th>
                        </tr>
                    </thead>
                    <tbody id="overviewBookingsTableBody" class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        <!-- Populated dynamically -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const token = localStorage.getItem('khedma_token');
        const userStr = localStorage.getItem('khedma_user');

        if (!token || !userStr) {
            window.location.href = '/login?admin=1';
            return;
        }

        try {
            const user = JSON.parse(userStr);
            if (user.role !== 'admin') {
                document.getElementById('loadingState').classList.add('hidden');
                document.getElementById('unauthorizedBanner').classList.remove('hidden');
                return;
            }

            await loadAdminOverview(token);
        } catch(e) {
            window.location.href = '/login?admin=1';
        }
    });

    async function loadAdminOverview(token) {
        try {
            const res = await fetch('/api/v1/admin/dashboard', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            if (!res.ok) {
                document.getElementById('loadingState').classList.add('hidden');
                document.getElementById('unauthorizedBanner').classList.remove('hidden');
                return;
            }

            const data = await res.json();
            const users = data.users || [];
            const bookings = data.bookings || [];
            const metrics = data.metrics || {};

            // Metrics
            document.getElementById('statGMV').textContent = (metrics.platform_gmv ?? 0).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
            document.getElementById('statTotalUsers').textContent = metrics.total_users ?? 0;
            document.getElementById('statUsersBreakdown').textContent = `${metrics.clients_count ?? 0} Clients • ${metrics.providers_count ?? 0} Providers • ${metrics.admins_count ?? 0} Admins`;
            if (document.getElementById('statVerifiedCountText')) {
                document.getElementById('statVerifiedCountText').textContent = `${metrics.verified_users_count || 0} Verified`;
            }
            if (document.getElementById('statSuspendedCountText')) {
                document.getElementById('statSuspendedCountText').textContent = `${metrics.suspended_users_count || 0} Suspended`;
            }
            document.getElementById('statTotalBookings').textContent = metrics.total_bookings ?? 0;
            document.getElementById('statBookingsBreakdown').textContent = `${metrics.completed_bookings ?? 0} Completed • ${metrics.active_bookings ?? 0} Active • ${metrics.pending_bookings ?? 0} Pending`;
            document.getElementById('statServicesCount').textContent = metrics.services_count ?? 0;

            const adminUser = JSON.parse(localStorage.getItem('khedma_user') || '{}');
            document.getElementById('adminNameDisplay').textContent = adminUser.name || 'Admin';
            document.getElementById('adminEmailDisplay').textContent = adminUser.email || 'admin@khedma.tn';

            renderOverviewUsers(users.slice(0, 5));
            renderOverviewBookings(bookings.slice(0, 5));

            document.getElementById('loadingState').classList.add('hidden');
            document.getElementById('adminContent').classList.remove('hidden');
        } catch(e) {
            console.error(e);
        }
    }

    function renderOverviewUsers(users) {
        const tbody = document.getElementById('overviewUsersTableBody');
        tbody.innerHTML = '';

        if (!users.length) {
            tbody.innerHTML = '<tr><td colspan="5" class="py-8 text-center text-xs text-slate-400 font-semibold">No registered users yet.</td></tr>';
            return;
        }

        users.forEach(u => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-50/70 dark:hover:bg-slate-900/40 transition';

            let roleBadge = 'bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-300';
            if (u.role === 'provider') roleBadge = 'bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300';
            if (u.role === 'admin') roleBadge = 'bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-300';

            const isVerified = !!u.is_verified;
            const verifBadge = isVerified
                ? `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400"><i class="bi bi-patch-check-fill"></i> Verified</span>`
                : `<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-500"><i class="bi bi-shield-x"></i> Unverified</span>`;

            const statusBadge = u.status === 'active'
                ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300'
                : 'bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-300';

            tr.innerHTML = `
                <td class="py-3.5 px-6">
                    <div class="font-extrabold text-xs text-slate-900 dark:text-white">${u.name}</div>
                    <div class="text-[11px] text-slate-400">${u.email}</div>
                </td>
                <td class="py-3.5 px-6">
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider ${roleBadge}">${u.role}</span>
                </td>
                <td class="py-3.5 px-6">${verifBadge}</td>
                <td class="py-3.5 px-6">
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider ${statusBadge}">${u.status}</span>
                </td>
                <td class="py-3.5 px-6 text-xs text-slate-400 whitespace-nowrap">${new Date(u.created_at).toLocaleDateString()}</td>
            `;
            tbody.appendChild(tr);
        });
    }

    function renderOverviewBookings(bookings) {
        const tbody = document.getElementById('overviewBookingsTableBody');
        tbody.innerHTML = '';

        if (!bookings.length) {
            tbody.innerHTML = '<tr><td colspan="6" class="py-8 text-center text-xs text-slate-400 font-semibold">No bookings placed yet.</td></tr>';
            return;
        }

        bookings.forEach(b => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-50/70 dark:hover:bg-slate-900/40 transition';

            tr.innerHTML = `
                <td class="py-3.5 px-6 font-mono font-bold text-xs text-slate-900 dark:text-white">${b.booking_number}</td>
                <td class="py-3.5 px-6 text-xs font-bold text-slate-900 dark:text-white">${b.client?.name || '—'}</td>
                <td class="py-3.5 px-6 text-xs font-bold text-amber-800 dark:text-amber-400">${b.provider?.name || '—'}</td>
                <td class="py-3.5 px-6 text-xs text-slate-700 dark:text-slate-300">${b.service_title}</td>
                <td class="py-3.5 px-6 font-bold text-xs text-slate-900 dark:text-white">${parseFloat(b.total_price || 0).toFixed(0)} TND</td>
                <td class="py-3.5 px-6">
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                        ${b.status}
                    </span>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }
</script>
@endsection
