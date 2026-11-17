@extends('layouts.app')

@section('title', 'Admin Area | Khedma')

@section('content')
<div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-8">
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
        <!-- 1. Admin Command Hero Banner (Spotlight Style) -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white p-8 md:p-10 shadow-xl border border-slate-800">
            <div class="absolute top-0 right-0 -mt-10 -mr-10 w-72 h-72 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-start sm:items-center gap-5">
                    <div class="w-16 h-16 rounded-2xl bg-red-500/20 border border-red-500/30 text-red-400 font-black text-2xl flex items-center justify-center shadow-lg shrink-0">
                        🛡️
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
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        Logout Admin
                    </button>
                </div>
            </div>
        </div>

        <!-- 2. System KPI Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Platform GMV -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Platform GMV (Revenue)</span>
                <div class="flex items-baseline gap-2 mt-2">
                    <span id="statGMV" class="text-3xl font-black text-slate-900 tracking-tight">0</span>
                    <span class="text-xs font-bold text-slate-500">TND</span>
                </div>
                <p class="text-xs text-emerald-600 font-semibold mt-2">Total volume completed</p>
            </div>

            <!-- Total Users -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Total Registered Users</span>
                <div class="flex items-baseline gap-2 mt-2">
                    <span id="statTotalUsers" class="text-3xl font-black text-slate-900 tracking-tight">0</span>
                </div>
                <p class="text-xs text-slate-500 font-semibold mt-2" id="statUsersBreakdown">0 Clients • 0 Providers</p>
            </div>

            <!-- Total Bookings -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Total Bookings</span>
                <div class="flex items-baseline gap-2 mt-2">
                    <span id="statTotalBookings" class="text-3xl font-black text-slate-900 tracking-tight">0</span>
                </div>
                <p class="text-xs text-blue-600 font-semibold mt-2" id="statBookingsBreakdown">0 Completed • 0 Active</p>
            </div>

            <!-- Active Services -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Active Services Catalog</span>
                <div class="flex items-baseline gap-2 mt-2">
                    <span id="statServicesCount" class="text-3xl font-black text-slate-900 tracking-tight">0</span>
                </div>
                <p class="text-xs text-amber-600 font-semibold mt-2">Services offered by pros</p>
            </div>
        </div>

        <!-- 3. Navigation Tabs: Users Management vs Platform Bookings -->
        <div class="flex items-center gap-2 border-b border-slate-200 pb-2">
            <button onclick="switchAdminTab('users')" id="tabUsersBtn" class="px-4 py-2 rounded-xl text-xs font-extrabold bg-slate-900 text-white shadow-sm transition">
                👥 Users Management
            </button>
            <button onclick="switchAdminTab('bookings')" id="tabBookingsBtn" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition">
                📋 Platform Bookings
            </button>
        </div>

        <!-- 4. Tab 1: Users Management Table -->
        <div id="sectionUsers" class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-extrabold text-slate-900 tracking-tight">Platform Users & Access Control</h2>
                    <p class="text-xs text-slate-500 mt-1">Manage user roles, inspect profiles, and suspend/activate accounts.</p>
                </div>
                <!-- Role filter -->
                <div class="flex items-center gap-1.5 p-1 bg-slate-100 rounded-xl text-xs font-bold">
                    <button onclick="filterUsers('all')" id="userFilterAll" class="px-3 py-1 rounded-lg bg-white text-slate-900 shadow-xs">All</button>
                    <button onclick="filterUsers('client')" id="userFilterClient" class="px-3 py-1 rounded-lg text-slate-600 hover:text-slate-900">Clients</button>
                    <button onclick="filterUsers('provider')" id="userFilterProvider" class="px-3 py-1 rounded-lg text-slate-600 hover:text-slate-900">Providers</button>
                    <button onclick="filterUsers('admin')" id="userFilterAdmin" class="px-3 py-1 rounded-lg text-slate-600 hover:text-slate-900">Admins</button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3.5 px-6">User</th>
                            <th class="py-3.5 px-6">Role</th>
                            <th class="py-3.5 px-6">Status</th>
                            <th class="py-3.5 px-6">City / Details</th>
                            <th class="py-3.5 px-6">Joined Date</th>
                            <th class="py-3.5 px-6 text-right">Moderation Actions</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody" class="divide-y divide-slate-100">
                        <!-- Populated dynamically -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 5. Tab 2: Platform Bookings Table -->
        <div id="sectionBookings" class="hidden bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h2 class="text-lg font-extrabold text-slate-900 tracking-tight">Platform-Wide Service Bookings</h2>
                <p class="text-xs text-slate-500 mt-1">All service agreements between clients and providers.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-[11px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                        <tr>
                            <th class="py-3.5 px-6">Booking #</th>
                            <th class="py-3.5 px-6">Client</th>
                            <th class="py-3.5 px-6">Provider</th>
                            <th class="py-3.5 px-6">Service</th>
                            <th class="py-3.5 px-6">Amount</th>
                            <th class="py-3.5 px-6">Status</th>
                        </tr>
                    </thead>
                    <tbody id="adminBookingsTableBody" class="divide-y divide-slate-100">
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
    let allUsers = [];
    let allBookings = [];
    let currentUserRoleFilter = 'all';

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

            await loadAdminDashboard(token);
        } catch(e) {
            window.location.href = '/login?admin=1';
        }
    });

    async function loadAdminDashboard(token) {
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
            allUsers = data.users || [];
            allBookings = data.bookings || [];
            const metrics = data.metrics || {};

            // Metrics
            document.getElementById('statGMV').textContent = metrics.platform_gmv.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
            document.getElementById('statTotalUsers').textContent = metrics.total_users;
            document.getElementById('statUsersBreakdown').textContent = `${metrics.clients_count} Clients • ${metrics.providers_count} Providers • ${metrics.admins_count} Admins`;
            document.getElementById('statTotalBookings').textContent = metrics.total_bookings;
            document.getElementById('statBookingsBreakdown').textContent = `${metrics.completed_bookings} Completed • ${metrics.active_bookings} Active • ${metrics.pending_bookings} Pending`;
            document.getElementById('statServicesCount').textContent = metrics.services_count;

            const adminUser = JSON.parse(localStorage.getItem('khedma_user'));
            document.getElementById('adminNameDisplay').textContent = adminUser.name;
            document.getElementById('adminEmailDisplay').textContent = adminUser.email;

            renderUsersTable(allUsers);
            renderAdminBookingsTable(allBookings);

            document.getElementById('loadingState').classList.add('hidden');
            document.getElementById('adminContent').classList.remove('hidden');
        } catch(e) {
            console.error(e);
        }
    }

    function switchAdminTab(tab) {
        const uBtn = document.getElementById('tabUsersBtn');
        const bBtn = document.getElementById('tabBookingsBtn');
        const uSec = document.getElementById('sectionUsers');
        const bSec = document.getElementById('sectionBookings');

        if (tab === 'users') {
            uBtn.className = 'px-4 py-2 rounded-xl text-xs font-extrabold bg-slate-900 text-white shadow-sm transition';
            bBtn.className = 'px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition';
            uSec.classList.remove('hidden');
            bSec.classList.add('hidden');
        } else {
            bBtn.className = 'px-4 py-2 rounded-xl text-xs font-extrabold bg-slate-900 text-white shadow-sm transition';
            uBtn.className = 'px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:text-slate-900 hover:bg-slate-100 transition';
            bSec.classList.remove('hidden');
            uSec.classList.add('hidden');
        }
    }

    function renderUsersTable(users) {
        const tbody = document.getElementById('usersTableBody');
        tbody.innerHTML = '';

        const filtered = currentUserRoleFilter === 'all' ? users : users.filter(u => u.role === currentUserRoleFilter);

        filtered.forEach(u => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-50/70 transition';

            let roleBadge = 'bg-blue-100 text-blue-800';
            if (u.role === 'provider') roleBadge = 'bg-amber-100 text-amber-800';
            if (u.role === 'admin') roleBadge = 'bg-red-100 text-red-800';

            const statusBadge = u.status === 'active' 
                ? 'bg-emerald-100 text-emerald-800' 
                : 'bg-red-100 text-red-800';

            const city = u.client_profile?.city || u.provider_profile?.city || 'Tunis';

            const adminUser = JSON.parse(localStorage.getItem('khedma_user'));
            const isSelf = adminUser && adminUser.id === u.id;

            let actionHtml = '';
            if (isSelf) {
                actionHtml = '<span class="text-xs text-slate-400 font-semibold italic">Current Admin</span>';
            } else {
                const nextStatus = u.status === 'active' ? 'suspended' : 'active';
                const nextLabel = u.status === 'active' ? 'Suspend' : 'Activate';
                const nextClass = u.status === 'active' ? 'text-red-600 hover:bg-red-50 border-red-200' : 'text-emerald-700 hover:bg-emerald-50 border-emerald-200';
                actionHtml = `
                    <button onclick="toggleUserStatus(${u.id}, '${nextStatus}')" class="px-3 py-1 rounded-lg border text-xs font-bold transition ${nextClass}">
                        ${nextLabel}
                    </button>
                `;
            }

            tr.innerHTML = `
                <td class="py-4 px-6">
                    <div class="font-extrabold text-xs text-slate-900">${u.name}</div>
                    <div class="text-[11px] text-slate-400">${u.email}</div>
                </td>
                <td class="py-4 px-6">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider ${roleBadge}">
                        ${u.role}
                    </span>
                </td>
                <td class="py-4 px-6">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider ${statusBadge}">
                        ${u.status}
                    </span>
                </td>
                <td class="py-4 px-6 text-xs text-slate-700">
                    ${city}
                </td>
                <td class="py-4 px-6 text-xs text-slate-400">
                    ${new Date(u.created_at).toLocaleDateString()}
                </td>
                <td class="py-4 px-6 text-right">
                    ${actionHtml}
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function filterUsers(role) {
        currentUserRoleFilter = role;
        const buttons = ['all', 'client', 'provider', 'admin'];
        buttons.forEach(b => {
            const btn = document.getElementById('userFilter' + b.charAt(0).toUpperCase() + b.slice(1));
            if (btn) {
                if (b === role) {
                    btn.className = 'px-3 py-1 rounded-lg bg-white text-slate-900 shadow-xs font-bold';
                } else {
                    btn.className = 'px-3 py-1 rounded-lg text-slate-600 hover:text-slate-900';
                }
            }
        });
        renderUsersTable(allUsers);
    }

    async function toggleUserStatus(userId, nextStatus) {
        const token = localStorage.getItem('khedma_token');
        try {
            const res = await fetch(`/api/v1/admin/users/${userId}/status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: nextStatus })
            });

            if (res.ok) {
                await loadAdminDashboard(token);
            }
        } catch(e) {
            console.error(e);
        }
    }

    function renderAdminBookingsTable(bookings) {
        const tbody = document.getElementById('adminBookingsTableBody');
        tbody.innerHTML = '';

        bookings.forEach(b => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-50/70 transition';

            tr.innerHTML = `
                <td class="py-4 px-6 font-mono font-bold text-xs text-slate-900">${b.booking_number}</td>
                <td class="py-4 px-6 text-xs font-bold text-slate-900">${b.client?.name || 'Client'}</td>
                <td class="py-4 px-6 text-xs font-bold text-amber-800">${b.provider?.name || 'Provider'}</td>
                <td class="py-4 px-6 text-xs text-slate-700">${b.service_title}</td>
                <td class="py-4 px-6 font-bold text-xs text-slate-900">${parseFloat(b.total_price).toFixed(0)} TND</td>
                <td class="py-4 px-6">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700">
                        ${b.status}
                    </span>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }
</script>
@endsection
