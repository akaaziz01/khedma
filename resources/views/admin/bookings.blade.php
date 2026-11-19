@extends('layouts.admin')

@section('title', 'Platform Bookings | KHEDMA Admin')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    <!-- Loading skeleton -->
    <div id="loadingState" class="py-24 text-center">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-amber-600 border-t-transparent mb-4"></div>
        <p class="text-sm text-slate-500 font-semibold">Loading platform bookings & transactions...</p>
    </div>

    <!-- Unauthorized Banner if not admin -->
    <div id="unauthorizedBanner" class="hidden py-16 text-center max-w-md mx-auto space-y-4">
        <div class="w-16 h-16 rounded-3xl bg-red-100 text-red-600 flex items-center justify-center mx-auto font-black text-2xl">
            ✕
        </div>
        <h2 class="text-2xl font-black text-slate-900 tracking-tight">Access Restricted</h2>
        <p class="text-xs text-slate-500">
            This area requires Administrator permissions.
        </p>
        <div class="pt-2 flex items-center justify-center gap-3">
            <a href="/login?admin=1" class="px-4 py-2 bg-red-600 text-white rounded-xl text-xs font-bold">Sign In as Admin</a>
        </div>
    </div>

    <!-- Main Content Container -->
    <div id="adminContent" class="hidden space-y-8">
        <!-- Breadcrumbs & Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs font-bold text-slate-400 mb-1">
                    <a href="/admin/dashboard" class="hover:text-slate-600 dark:hover:text-slate-300">Admin</a>
                    <span>/</span>
                    <span class="text-amber-600 dark:text-amber-400">Platform Bookings</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    Platform Service Bookings
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Monitor all service agreements, scheduled appointments, and transaction statuses across Tunisia.
                </p>
            </div>

            <div class="flex items-center gap-2 self-start sm:self-auto">
                <a href="/admin/dashboard" class="px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-800 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition flex items-center gap-1.5">
                    <i class="bi bi-speedometer2"></i> Dashboard Overview
                </a>
            </div>
        </div>

        <!-- Bookings KPI Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            <div class="bg-white dark:bg-slate-950/80 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Total Bookings</span>
                <span id="metricTotalBookings" class="text-2xl font-black text-slate-900 dark:text-white">0</span>
                <p class="text-[11px] text-slate-400 mt-1">Agreements created</p>
            </div>
            <div class="bg-white dark:bg-slate-950/80 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-500 block mb-1">Completed</span>
                <span id="metricCompletedBookings" class="text-2xl font-black text-emerald-600 dark:text-emerald-400">0</span>
                <p class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold mt-1">Services delivered</p>
            </div>
            <div class="bg-white dark:bg-slate-950/80 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-500 block mb-1">Active / In Progress</span>
                <span id="metricActiveBookings" class="text-2xl font-black text-blue-600 dark:text-blue-400">0</span>
                <p class="text-[11px] text-blue-600 dark:text-blue-400 font-semibold mt-1">Ongoing or accepted</p>
            </div>
            <div class="bg-white dark:bg-slate-950/80 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-500 block mb-1">Pending</span>
                <span id="metricPendingBookings" class="text-2xl font-black text-amber-600 dark:text-amber-400">0</span>
                <p class="text-[11px] text-amber-600 dark:text-amber-400 font-semibold mt-1">Awaiting acceptance</p>
            </div>
            <div class="bg-white dark:bg-slate-950/80 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs col-span-2 sm:col-span-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-500 block mb-1">Total GMV</span>
                <div class="flex items-baseline gap-1">
                    <span id="metricPlatformGMV" class="text-2xl font-black text-indigo-600 dark:text-indigo-400">0</span>
                    <span class="text-xs font-bold text-slate-400">TND</span>
                </div>
                <p class="text-[11px] text-indigo-600 dark:text-indigo-400 font-semibold mt-1">Completed volume</p>
            </div>
        </div>

        <!-- Bookings Management Table -->
        <div class="bg-white dark:bg-slate-950 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden transition-colors duration-200">
            <div class="p-6 border-b border-slate-100 dark:border-slate-800 space-y-4">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-extrabold text-slate-900 dark:text-white tracking-tight">Platform Bookings Directory</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Track and inspect service bookings placed on the platform.</p>
                    </div>

                    <!-- Status filter pills -->
                    <div class="flex items-center gap-1.5 p-1 bg-slate-100 dark:bg-slate-900 rounded-xl text-xs font-bold border border-transparent dark:border-slate-800 self-start md:self-auto shrink-0 flex-wrap">
                        <button onclick="filterBookingsStatus('all')" id="bkFilterAll" class="px-3 py-1 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-xs">All</button>
                        <button onclick="filterBookingsStatus('pending')" id="bkFilterPending" class="px-3 py-1 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">Pending</button>
                        <button onclick="filterBookingsStatus('accepted')" id="bkFilterAccepted" class="px-3 py-1 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">Accepted</button>
                        <button onclick="filterBookingsStatus('in_progress')" id="bkFilterInProgress" class="px-3 py-1 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">In Progress</button>
                        <button onclick="filterBookingsStatus('completed')" id="bkFilterCompleted" class="px-3 py-1 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">Completed</button>
                    </div>
                </div>

                <!-- Search input -->
                <div class="flex items-center gap-3">
                    <div class="relative flex-1">
                        <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input id="bookingSearchInput" type="text" oninput="handleBookingSearchInput(this.value)"
                            placeholder="Search by booking #, client, provider, or service title..."
                            class="w-full pl-9 pr-8 py-2 rounded-xl text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/50">
                    </div>
                    <span id="bookingsCountSummary" class="text-xs text-slate-400 font-semibold shrink-0"></span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                    <thead class="bg-slate-50 dark:bg-slate-900/60 text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800">
                        <tr>
                            <th class="py-3.5 px-6">Booking #</th>
                            <th class="py-3.5 px-6">Client</th>
                            <th class="py-3.5 px-6">Provider</th>
                            <th class="py-3.5 px-6">Service</th>
                            <th class="py-3.5 px-6">Price</th>
                            <th class="py-3.5 px-6">Scheduled For</th>
                            <th class="py-3.5 px-6">Status</th>
                        </tr>
                    </thead>
                    <tbody id="bookingsTableBody" class="divide-y divide-slate-100 dark:divide-slate-800/80">
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
    let allBookings = [];
    let currentStatusFilter = 'all';
    let currentBookingSearch = '';

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

            await loadBookings(token);
        } catch(e) {
            window.location.href = '/login?admin=1';
        }
    });

    async function loadBookings(token) {
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
            allBookings = data.bookings || [];
            const metrics = data.metrics || {};

            document.getElementById('metricTotalBookings').textContent = metrics.total_bookings ?? 0;
            document.getElementById('metricCompletedBookings').textContent = metrics.completed_bookings ?? 0;
            document.getElementById('metricActiveBookings').textContent = metrics.active_bookings ?? 0;
            document.getElementById('metricPendingBookings').textContent = metrics.pending_bookings ?? 0;
            document.getElementById('metricPlatformGMV').textContent = (metrics.platform_gmv ?? 0).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });

            applyBookingFilters();

            document.getElementById('loadingState').classList.add('hidden');
            document.getElementById('adminContent').classList.remove('hidden');
        } catch(e) {
            console.error(e);
        }
    }

    function handleBookingSearchInput(query) {
        currentBookingSearch = (query || '').toLowerCase().trim();
        applyBookingFilters();
    }

    function filterBookingsStatus(status) {
        currentStatusFilter = status;
        const buttons = ['all', 'pending', 'accepted', 'in_progress', 'completed'];
        buttons.forEach(s => {
            const btn = document.getElementById('bkFilter' + s.split('_').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(''));
            if (btn) {
                btn.className = s === status
                    ? 'px-3 py-1 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-xs font-bold'
                    : 'px-3 py-1 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white';
            }
        });
        applyBookingFilters();
    }

    function applyBookingFilters() {
        let filtered = allBookings.slice();

        if (currentStatusFilter !== 'all') {
            filtered = filtered.filter(b => b.status === currentStatusFilter);
        }

        if (currentBookingSearch) {
            filtered = filtered.filter(b => {
                const num = (b.booking_number || '').toLowerCase();
                const client = (b.client?.name || '').toLowerCase();
                const provider = (b.provider?.name || '').toLowerCase();
                const title = (b.service_title || '').toLowerCase();
                return num.includes(currentBookingSearch) ||
                    client.includes(currentBookingSearch) ||
                    provider.includes(currentBookingSearch) ||
                    title.includes(currentBookingSearch);
            });
        }

        const summaryEl = document.getElementById('bookingsCountSummary');
        if (summaryEl) {
            summaryEl.textContent = `Showing ${filtered.length} of ${allBookings.length} bookings`;
        }

        renderBookingsTable(filtered);
    }

    function renderBookingsTable(bookings) {
        const tbody = document.getElementById('bookingsTableBody');
        tbody.innerHTML = '';

        if (!bookings.length) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="py-12 text-center text-xs text-slate-400 dark:text-slate-500 font-semibold">
                        <i class="bi bi-calendar2-x text-3xl block mb-2 text-slate-300 dark:text-slate-600"></i>
                        No bookings found matching current filters.
                    </td>
                </tr>
            `;
            return;
        }

        bookings.forEach(b => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-50/70 dark:hover:bg-slate-900/40 transition';

            let statusBadge = 'bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300';
            if (b.status === 'completed') statusBadge = 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300';
            if (b.status === 'accepted' || b.status === 'in_progress') statusBadge = 'bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-300';
            if (b.status === 'pending') statusBadge = 'bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300';
            if (b.status === 'cancelled' || b.status === 'declined') statusBadge = 'bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-300';

            const scheduledDate = b.scheduled_at ? new Date(b.scheduled_at).toLocaleString() : '—';

            tr.innerHTML = `
                <td class="py-4 px-6 font-mono font-bold text-xs text-slate-900 dark:text-white">${b.booking_number}</td>
                <td class="py-4 px-6">
                    <div class="text-xs font-bold text-slate-900 dark:text-white">${b.client?.name || '—'}</div>
                    <div class="text-[11px] text-slate-400">${b.client?.email || ''}</div>
                </td>
                <td class="py-4 px-6">
                    <div class="text-xs font-bold text-amber-800 dark:text-amber-400">${b.provider?.name || '—'}</div>
                    <div class="text-[11px] text-slate-400">${b.provider?.email || ''}</div>
                </td>
                <td class="py-4 px-6 text-xs text-slate-700 dark:text-slate-300">
                    <div class="font-semibold">${b.service_title}</div>
                    <div class="text-[10px] text-slate-400">${b.category || ''}</div>
                </td>
                <td class="py-4 px-6 font-bold text-xs text-slate-900 dark:text-white">${parseFloat(b.total_price || 0).toFixed(0)} TND</td>
                <td class="py-4 px-6 text-xs text-slate-500 whitespace-nowrap">${scheduledDate}</td>
                <td class="py-4 px-6">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider ${statusBadge}">
                        ${b.status}
                    </span>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    window.filterBookingsStatus = filterBookingsStatus;
    window.handleBookingSearchInput = handleBookingSearchInput;
</script>
@endsection
