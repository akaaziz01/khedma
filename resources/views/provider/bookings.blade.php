@extends('layouts.app')

@section('title', 'Bookings & Leads | Khedma Provider')

@section('content')
<div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-8">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-200 dark:border-slate-800">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider mb-1">
                <i class="bi bi-calendar-check-fill"></i>
                <span>Provider Portal</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                Bookings & Leads Management
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Manage appointment requests, accept new customer leads, and track job statuses.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="/dashboard" class="px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition flex items-center gap-2 shadow-xs">
                <i class="bi bi-speedometer2"></i>
                <span>Main Dashboard</span>
            </a>
            <a href="/provider/services" class="px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold transition flex items-center gap-2 shadow-sm">
                <i class="bi bi-tools"></i>
                <span>My Services</span>
            </a>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <span class="text-xs font-bold text-slate-400 block uppercase">Total Inquiries</span>
            <span id="statTotalBookings" class="text-2xl font-black text-slate-900 dark:text-white mt-1 block">0</span>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <span class="text-xs font-bold text-amber-600 dark:text-amber-400 block uppercase">Pending Leads</span>
            <span id="statPendingBookings" class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1 block">0</span>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <span class="text-xs font-bold text-blue-600 dark:text-blue-400 block uppercase">Active Jobs</span>
            <span id="statActiveJobs" class="text-2xl font-black text-blue-600 dark:text-blue-400 mt-1 block">0</span>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 block uppercase">Completed</span>
            <span id="statCompletedJobs" class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1 block">0</span>
        </div>
    </div>

    <!-- Bookings & Leads Table Container -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden space-y-4 p-6 sm:p-8">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="bi bi-inbox-fill text-amber-500"></i>
                    <span>All Bookings & Customer Requests</span>
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Filter by booking state or search appointments</p>
            </div>

            <!-- Filter Buttons -->
            <div class="flex items-center gap-1.5 p-1 bg-slate-100 dark:bg-slate-800 rounded-xl text-xs font-bold flex-wrap">
                <button onclick="filterBookings('all')" id="btnFilterAll" class="px-3 py-1.5 rounded-lg bg-white dark:bg-slate-700 shadow-xs text-slate-900 dark:text-white">All</button>
                <button onclick="filterBookings('pending')" id="btnFilterPending" class="px-3 py-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white">Pending</button>
                <button onclick="filterBookings('accepted')" id="btnFilterAccepted" class="px-3 py-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white">Accepted</button>
                <button onclick="filterBookings('in_progress')" id="btnFilterInProgress" class="px-3 py-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white">In Progress</button>
                <button onclick="filterBookings('completed')" id="btnFilterCompleted" class="px-3 py-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white">Completed</button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto pt-2">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4 sm:px-6">Booking #</th>
                        <th class="py-3.5 px-4 sm:px-6">Client</th>
                        <th class="py-3.5 px-4 sm:px-6">Service Requested</th>
                        <th class="py-3.5 px-4 sm:px-6">Scheduled At & Address</th>
                        <th class="py-3.5 px-4 sm:px-6">Amount</th>
                        <th class="py-3.5 px-4 sm:px-6">Status</th>
                        <th class="py-3.5 px-4 sm:px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="bookingsTableBody" class="divide-y divide-slate-100 dark:divide-slate-800">
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-2 border-amber-500 border-t-transparent mb-2"></div>
                            <p class="text-xs">Loading bookings data...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    let allBookings = [];
    let currentFilter = 'all';

    document.addEventListener('DOMContentLoaded', () => {
        loadProviderBookings();
    });

    async function loadProviderBookings() {
        const token = localStorage.getItem('khedma_token');
        if (!token) {
            window.location.href = '/login?redirect=' + encodeURIComponent(window.location.pathname);
            return;
        }

        try {
            const res = await fetch('/api/v1/provider/dashboard', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            if (res.status === 401 || res.status === 403) {
                window.location.href = '/login';
                return;
            }

            const data = await res.json();
            allBookings = data.bookings || [];

            // Update stats
            document.getElementById('statTotalBookings').textContent = allBookings.length;
            document.getElementById('statPendingBookings').textContent = allBookings.filter(b => b.status === 'pending').length;
            document.getElementById('statActiveJobs').textContent = allBookings.filter(b => b.status === 'accepted' || b.status === 'in_progress').length;
            document.getElementById('statCompletedJobs').textContent = allBookings.filter(b => b.status === 'completed').length;

            renderBookingsTable();
        } catch (err) {
            console.error('Error fetching provider bookings:', err);
            document.getElementById('bookingsTableBody').innerHTML = `
                <tr>
                    <td colspan="7" class="py-12 text-center text-red-500 text-xs">
                        Failed to load bookings. Please refresh the page.
                    </td>
                </tr>
            `;
        }
    }

    function filterBookings(status) {
        currentFilter = status;
        const btnIds = ['btnFilterAll', 'btnFilterPending', 'btnFilterAccepted', 'btnFilterInProgress', 'btnFilterCompleted'];
        btnIds.forEach(id => {
            const btn = document.getElementById(id);
            if (btn) {
                btn.className = 'px-3 py-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white';
            }
        });

        const activeMap = {
            'all': 'btnFilterAll',
            'pending': 'btnFilterPending',
            'accepted': 'btnFilterAccepted',
            'in_progress': 'btnFilterInProgress',
            'completed': 'btnFilterCompleted'
        };

        const activeBtn = document.getElementById(activeMap[status]);
        if (activeBtn) {
            activeBtn.className = 'px-3 py-1.5 rounded-lg bg-white dark:bg-slate-700 shadow-xs text-slate-900 dark:text-white font-bold';
        }

        renderBookingsTable();
    }

    function renderBookingsTable() {
        const tbody = document.getElementById('bookingsTableBody');
        let filtered = allBookings;
        if (currentFilter !== 'all') {
            filtered = allBookings.filter(b => b.status === currentFilter);
        }

        if (filtered.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="py-12 text-center text-slate-400">
                        <i class="bi bi-calendar-x text-3xl block mb-2 text-slate-300 dark:text-slate-600"></i>
                        <p class="text-sm font-bold text-slate-600 dark:text-slate-300">No ${currentFilter === 'all' ? '' : currentFilter} bookings found</p>
                        <p class="text-xs text-slate-400 mt-1">When clients book your services, requests will appear here automatically.</p>
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = filtered.map(b => {
            const clientName = b.client ? b.client.name : 'Guest/Client';
            const clientPhone = b.client && b.client.phone ? b.client.phone : 'Not provided';
            const scheduled = b.scheduled_at ? new Date(b.scheduled_at).toLocaleDateString('fr-TN', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : 'Flexible';

            let statusBadge = '';
            if (b.status === 'pending') {
                statusBadge = '<span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300">Pending Review</span>';
            } else if (b.status === 'accepted') {
                statusBadge = '<span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300">Accepted</span>';
            } else if (b.status === 'in_progress') {
                statusBadge = '<span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-300">In Progress</span>';
            } else if (b.status === 'completed') {
                statusBadge = '<span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300">Completed</span>';
            } else {
                statusBadge = `<span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300">${b.status}</span>`;
            }

            let actionBtns = '';
            if (b.status === 'pending') {
                actionBtns = `
                    <button onclick="updateBookingStatus(${b.id}, 'accepted')" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition">Accept</button>
                    <button onclick="updateBookingStatus(${b.id}, 'declined')" class="px-2.5 py-1 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-xs font-bold transition">Decline</button>
                `;
            } else if (b.status === 'accepted') {
                actionBtns = `
                    <button onclick="updateBookingStatus(${b.id}, 'in_progress')" class="px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold transition">Start Job</button>
                `;
            } else if (b.status === 'in_progress') {
                actionBtns = `
                    <button onclick="updateBookingStatus(${b.id}, 'completed')" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition">Complete</button>
                `;
            } else {
                actionBtns = `<span class="text-xs text-slate-400 font-semibold">—</span>`;
            }

            return `
                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                    <td class="py-4 px-4 sm:px-6 font-mono font-bold text-xs text-slate-900 dark:text-white">
                        #${b.booking_number || b.id}
                    </td>
                    <td class="py-4 px-4 sm:px-6">
                        <div class="font-bold text-slate-900 dark:text-white text-xs">${clientName}</div>
                        <div class="text-[11px] text-slate-400">${clientPhone}</div>
                    </td>
                    <td class="py-4 px-4 sm:px-6">
                        <div class="font-bold text-slate-900 dark:text-white text-xs">${b.service_title || b.category || 'Service Request'}</div>
                        <div class="text-[10px] text-amber-600 dark:text-amber-400 font-semibold">${b.category || ''}</div>
                    </td>
                    <td class="py-4 px-4 sm:px-6">
                        <div class="text-xs font-bold text-slate-800 dark:text-slate-200">${scheduled}</div>
                        <div class="text-[11px] text-slate-400 truncate max-w-xs">${b.address || b.city || 'Tunis'}</div>
                    </td>
                    <td class="py-4 px-4 sm:px-6 font-black text-slate-900 dark:text-white text-xs">
                        ${parseFloat(b.total_price).toFixed(2)} <span class="text-[10px] text-amber-600">TND</span>
                    </td>
                    <td class="py-4 px-4 sm:px-6">
                        ${statusBadge}
                    </td>
                    <td class="py-4 px-4 sm:px-6 text-right space-x-1 whitespace-nowrap">
                        ${actionBtns}
                    </td>
                </tr>
            `;
        }).join('');
    }

    async function updateBookingStatus(bookingId, newStatus) {
        const token = localStorage.getItem('khedma_token');
        if (!token) return;

        try {
            const res = await fetch(`/api/v1/provider/bookings/${bookingId}/status`, {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: newStatus })
            });

            if (res.ok) {
                // Refresh data
                await loadProviderBookings();
            } else {
                alert('Could not update booking status. Please try again.');
            }
        } catch (e) {
            console.error(e);
            alert('Network error while updating status.');
        }
    }
</script>
@endsection
