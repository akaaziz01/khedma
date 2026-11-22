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
                Review incoming client requests, accept confirmed jobs, and track your workload.
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
        <div class="p-4 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200/80 dark:border-amber-900/40 shadow-xs">
            <span class="text-xs font-bold text-amber-600 dark:text-amber-400 block uppercase">⏳ Pending Review</span>
            <span id="statPendingBookings" class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1 block">0</span>
        </div>
        <div class="p-4 rounded-2xl bg-blue-50 dark:bg-blue-950/40 border border-blue-200/80 dark:border-blue-900/40 shadow-xs">
            <span class="text-xs font-bold text-blue-600 dark:text-blue-400 block uppercase">Active Jobs</span>
            <span id="statActiveJobs" class="text-2xl font-black text-blue-600 dark:text-blue-400 mt-1 block">0</span>
        </div>
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200/80 dark:border-emerald-900/40 shadow-xs">
            <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 block uppercase">Completed</span>
            <span id="statCompletedJobs" class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1 block">0</span>
        </div>
    </div>

    <!-- Bookings Container -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-6 sm:p-8 border-b border-slate-100 dark:border-slate-800">
            <div>
                <h2 class="text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="bi bi-inbox-fill text-amber-500"></i>
                    <span>All Bookings & Customer Requests</span>
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Filter by state. Pending requests require your action — accept or decline each one.</p>
            </div>

            <!-- Filter Buttons -->
            <div class="flex items-center gap-1.5 p-1 bg-slate-100 dark:bg-slate-800 rounded-xl text-xs font-bold flex-wrap border border-transparent dark:border-slate-700/60">
                <button onclick="filterBookings('all')"         id="btnFilterAll"        class="px-3 py-1.5 rounded-lg bg-white dark:bg-slate-700 shadow-xs text-slate-900 dark:text-white">All</button>
                <button onclick="filterBookings('pending')"     id="btnFilterPending"    class="px-3 py-1.5 rounded-lg text-amber-600 hover:bg-white dark:hover:bg-slate-700 font-black">⏳ Pending</button>
                <button onclick="filterBookings('accepted')"    id="btnFilterAccepted"   class="px-3 py-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white">Accepted</button>
                <button onclick="filterBookings('in_progress')" id="btnFilterInProgress" class="px-3 py-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white">In Progress</button>
                <button onclick="filterBookings('completed')"   id="btnFilterCompleted"  class="px-3 py-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white">Completed</button>
                <button onclick="filterBookings('declined')"    id="btnFilterDeclined"   class="px-3 py-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white">Declined</button>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4 sm:px-6">Booking #</th>
                        <th class="py-3.5 px-4 sm:px-6">Client</th>
                        <th class="py-3.5 px-4 sm:px-6">Service / Job</th>
                        <th class="py-3.5 px-4 sm:px-6">Date & Location</th>
                        <th class="py-3.5 px-4 sm:px-6">Amount</th>
                        <th class="py-3.5 px-4 sm:px-6">Status</th>
                        <th class="py-3.5 px-4 sm:px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="bookingsTableBody" class="divide-y divide-slate-100 dark:divide-slate-800">
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            <div class="inline-block animate-spin rounded-full h-8 w-8 border-2 border-amber-500 border-t-transparent mb-2"></div>
                            <p class="text-xs">Loading bookings from database...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Decline Reason Modal -->
<div id="declineModal" class="hidden fixed inset-0 z-50 bg-slate-900/70 dark:bg-black/80 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 max-w-md w-full p-6 sm:p-8 space-y-5">
        <div class="flex items-start justify-between">
            <div>
                <h3 class="text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="bi bi-x-circle-fill text-red-500"></i>
                    <span>Decline Booking Request</span>
                </h3>
                <p id="declineModalBookingRef" class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 font-mono">BK-XXXXXX — Client Name</p>
            </div>
            <button onclick="closeDeclineModal()" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>

        <div class="flex items-start gap-3 p-3.5 rounded-xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-900/40">
            <i class="bi bi-exclamation-triangle-fill text-red-500 mt-0.5 shrink-0"></i>
            <p class="text-xs text-red-800 dark:text-red-300 leading-relaxed">
                Declining will notify the client and free their request. This action <strong>cannot be undone</strong>.
            </p>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                Reason for Declining <span class="text-red-500">*</span>
            </label>
            <textarea id="declineReasonText" rows="4" maxlength="1000" required
                placeholder="Please explain why you cannot take this booking (e.g. fully booked that week, outside your service area, missing required materials)..."
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 dark:border-slate-700 text-xs bg-white dark:bg-slate-800 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500 resize-none"></textarea>
            <p class="text-[11px] text-slate-400 mt-1">The client will see this message. Keep it professional and helpful.</p>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2 border-t border-slate-100 dark:border-slate-800">
            <button onclick="closeDeclineModal()" class="px-4 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition">
                Cancel
            </button>
            <button id="confirmDeclineBtn" onclick="confirmDecline()"
                class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white text-xs font-extrabold rounded-xl shadow-md transition flex items-center gap-2">
                <i class="bi bi-x-circle-fill text-xs"></i>
                <span>Confirm Decline</span>
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let allBookings = [];
    let currentFilter = 'all';
    let _declineBookingId = null;

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
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
            });

            if (res.status === 401 || res.status === 403) {
                window.location.href = '/login';
                return;
            }

            const data = await res.json();
            allBookings = data.bookings || [];

            document.getElementById('statTotalBookings').textContent   = allBookings.length;
            document.getElementById('statPendingBookings').textContent  = allBookings.filter(b => b.status === 'pending').length;
            document.getElementById('statActiveJobs').textContent       = allBookings.filter(b => ['accepted', 'in_progress'].includes(b.status)).length;
            document.getElementById('statCompletedJobs').textContent    = allBookings.filter(b => b.status === 'completed').length;

            renderBookingsTable();
        } catch (err) {
            document.getElementById('bookingsTableBody').innerHTML = `
                <tr><td colspan="7" class="py-12 text-center text-red-500 text-xs">
                    Failed to load bookings. Please refresh the page.
                </td></tr>`;
        }
    }

    function filterBookings(status) {
        currentFilter = status;
        const allIds = ['all', 'pending', 'accepted', 'in_progress', 'completed', 'declined'];
        allIds.forEach(s => {
            const id = 'btnFilter' + s.split('_').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join('');
            const btn = document.getElementById(id);
            if (btn) btn.className = 'px-3 py-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white';
        });
        const activeId = 'btnFilter' + status.split('_').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join('');
        const activeBtn = document.getElementById(activeId);
        if (activeBtn) activeBtn.className = 'px-3 py-1.5 rounded-lg bg-white dark:bg-slate-700 shadow-xs text-slate-900 dark:text-white font-bold';
        renderBookingsTable();
    }

    function renderBookingsTable() {
        const tbody = document.getElementById('bookingsTableBody');
        let filtered = currentFilter === 'all' ? allBookings : allBookings.filter(b => b.status === currentFilter);

        if (filtered.length === 0) {
            const labels = { pending: 'pending requests', accepted: 'accepted bookings', in_progress: 'active jobs', completed: 'completed jobs', declined: 'declined bookings' };
            tbody.innerHTML = `
                <tr><td colspan="7" class="py-12 text-center text-slate-400">
                    <i class="bi bi-calendar-x text-3xl block mb-2 text-slate-300 dark:text-slate-600"></i>
                    <p class="text-sm font-bold text-slate-600 dark:text-slate-300">No ${labels[currentFilter] || 'bookings'} found</p>
                    <p class="text-xs text-slate-400 mt-1">When clients book your services, requests will appear here automatically.</p>
                </td></tr>`;
            return;
        }

        tbody.innerHTML = filtered.map(b => {
            const clientName  = b.client?.name  || 'Guest/Client';
            const clientPhone = b.client?.phone || 'Not provided';
            const scheduled   = b.scheduled_at
                ? new Date(b.scheduled_at).toLocaleDateString('fr-TN', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' })
                : 'Flexible';

            const badgeMap = {
                pending:     '<span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300 animate-pulse">⏳ Pending Review</span>',
                accepted:    '<span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300">✓ Accepted</span>',
                in_progress: '<span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-indigo-100 text-indigo-800 dark:bg-indigo-900/60 dark:text-indigo-300">🔧 In Progress</span>',
                completed:   '<span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-300">✅ Completed</span>',
                declined:    '<span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-red-100 text-red-800 dark:bg-red-900/60 dark:text-red-300">✗ Declined</span>',
                cancelled:   '<span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300">Cancelled</span>',
            };

            const escapedRef = `${b.booking_number || b.id} — ${clientName}`.replace(/'/g, "\\'");
            let actionBtns = '';
            if (b.status === 'pending') {
                actionBtns = `
                    <div class="flex items-center justify-end gap-1.5">
                        <button onclick="acceptBooking(${b.id}, '${(b.booking_number||b.id).toString().replace(/'/g,"\\'")}', '${clientName.replace(/'/g,"\\'")}')}"
                            class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition flex items-center gap-1 shadow-sm">
                            <i class="bi bi-check-lg text-[11px]"></i> Accept
                        </button>
                        <button onclick="openDeclineModal(${b.id}, '${escapedRef}')"
                            class="px-2.5 py-1.5 bg-red-50 hover:bg-red-100 dark:bg-red-950/40 dark:hover:bg-red-900/60 text-red-700 dark:text-red-400 border border-red-200 dark:border-red-900/40 rounded-lg text-xs font-bold transition">
                            Decline
                        </button>
                    </div>`;
            } else if (b.status === 'accepted') {
                actionBtns = `<button onclick="startBooking(${b.id})" class="px-2.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold transition">Start Job</button>`;
            } else if (b.status === 'in_progress') {
                actionBtns = `<button onclick="completeBooking(${b.id})" class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition">✓ Mark Done</button>`;
            } else if (b.status === 'declined' && b.declined_reason) {
                actionBtns = `<button onclick="alert('Decline reason: ${b.declined_reason.replace(/'/g,"\\'").substring(0, 200)}')" class="px-2.5 py-1 text-xs text-red-500 dark:text-red-400 hover:underline">View Reason</button>`;
            } else {
                actionBtns = `<span class="text-xs text-slate-400 font-semibold">—</span>`;
            }

            return `
                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                    <td class="py-4 px-4 sm:px-6 font-mono font-bold text-xs text-slate-900 dark:text-white">#${b.booking_number || b.id}</td>
                    <td class="py-4 px-4 sm:px-6">
                        <div class="font-bold text-slate-900 dark:text-white text-xs">${clientName}</div>
                        <div class="text-[11px] text-slate-400">${clientPhone}</div>
                    </td>
                    <td class="py-4 px-4 sm:px-6">
                        <div class="font-bold text-slate-900 dark:text-white text-xs">${b.service_title || b.category || 'Service Request'}</div>
                        <div class="text-[10px] text-amber-600 dark:text-amber-400 font-semibold">${b.category || ''}</div>
                        ${b.client_notes ? `<div class="text-[10px] text-slate-500 dark:text-slate-400 italic mt-0.5 max-w-xs truncate">"${b.client_notes}"</div>` : ''}
                    </td>
                    <td class="py-4 px-4 sm:px-6">
                        <div class="text-xs font-bold text-slate-800 dark:text-slate-200">${scheduled}</div>
                        <div class="text-[11px] text-slate-400 truncate max-w-xs">${b.address || b.city || 'Tunis'}</div>
                    </td>
                    <td class="py-4 px-4 sm:px-6 font-black text-slate-900 dark:text-white text-xs">
                        ${parseFloat(b.total_price || 0).toFixed(2)} <span class="text-[10px] text-amber-600 font-bold">TND</span>
                    </td>
                    <td class="py-4 px-4 sm:px-6">${badgeMap[b.status] || `<span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-slate-100 text-slate-800">${b.status}</span>`}</td>
                    <td class="py-4 px-4 sm:px-6 text-right whitespace-nowrap">
                        <div class="flex flex-col items-end gap-2">
                            ${actionBtns}
                            <a href="/report?type=client&booking_id=${Number(b.id)}" class="text-[11px] font-bold text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300">Report client</a>
                        </div>
                    </td>
                </tr>`;
        }).join('');
    }

    // ── Accept Booking ────────────────────────────────────────────────────────
    async function acceptBooking(bookingId, bookingNumber, clientName) {
        if (!confirm(`Accept booking ${bookingNumber} from ${clientName}?\n\nThe booking will be confirmed and the client will be notified.`)) return;
        const token = localStorage.getItem('khedma_token');
        try {
            const res = await fetch(`/api/v1/provider/bookings/${bookingId}/accept`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token, 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                body: '{}'
            });
            const data = await res.json();
            if (res.ok) { showToast('✅ ' + data.message, 'success'); await loadProviderBookings(); }
            else showToast(data.message || 'Could not accept booking.', 'error');
        } catch(e) { showToast('Network error.', 'error'); }
    }

    // ── Start Job ─────────────────────────────────────────────────────────────
    async function startBooking(bookingId) {
        const token = localStorage.getItem('khedma_token');
        const res = await fetch(`/api/v1/provider/bookings/${bookingId}/start`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token, 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
            body: '{}'
        });
        const data = await res.json();
        if (res.ok) { showToast('🔧 ' + data.message, 'info'); await loadProviderBookings(); }
        else showToast(data.message || 'Could not start job.', 'error');
    }

    // ── Complete Booking ──────────────────────────────────────────────────────
    async function completeBooking(bookingId) {
        if (!confirm('Mark this job as completed? The client will be able to leave a review.')) return;
        const token = localStorage.getItem('khedma_token');
        const res = await fetch(`/api/v1/provider/bookings/${bookingId}/complete`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token, 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
            body: '{}'
        });
        const data = await res.json();
        if (res.ok) { showToast('✅ ' + data.message, 'success'); await loadProviderBookings(); }
        else showToast(data.message || 'Could not complete booking.', 'error');
    }

    // ── Decline Modal ─────────────────────────────────────────────────────────
    function openDeclineModal(bookingId, label) {
        _declineBookingId = bookingId;
        document.getElementById('declineModalBookingRef').textContent = label;
        document.getElementById('declineReasonText').value = '';
        document.getElementById('declineModal').classList.remove('hidden');
    }
    function closeDeclineModal() {
        _declineBookingId = null;
        document.getElementById('declineModal').classList.add('hidden');
    }
    async function confirmDecline() {
        const reason = document.getElementById('declineReasonText').value.trim();
        if (!reason || reason.length < 5) { alert('Please provide a reason (at least 5 characters).'); return; }
        const btn = document.getElementById('confirmDeclineBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split text-xs"></i> Declining...';
        const token = localStorage.getItem('khedma_token');
        try {
            const res = await fetch(`/api/v1/provider/bookings/${_declineBookingId}/decline`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'Authorization': 'Bearer ' + token, 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                body: JSON.stringify({ declined_reason: reason })
            });
            const data = await res.json();
            if (res.ok) { closeDeclineModal(); showToast('Booking declined. Reason sent to client.', 'warning'); await loadProviderBookings(); }
            else showToast(data.message || 'Could not decline booking.', 'error');
        } catch(e) { showToast('Network error.', 'error'); }
        finally { btn.disabled = false; btn.innerHTML = '<i class="bi bi-x-circle-fill text-xs"></i> <span>Confirm Decline</span>'; }
    }

    // ── Toast ─────────────────────────────────────────────────────────────────
    function showToast(message, type = 'success') {
        let container = document.getElementById('toastContainerBookings');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toastContainerBookings';
            container.className = 'fixed bottom-6 right-6 z-50 flex flex-col gap-2';
            document.body.appendChild(container);
        }
        const colors = { success: 'bg-emerald-600', error: 'bg-red-600', info: 'bg-blue-600', warning: 'bg-amber-500' };
        const toast = document.createElement('div');
        toast.className = `px-4 py-3 rounded-xl shadow-xl text-xs font-bold text-white flex items-center gap-2 max-w-xs ${colors[type] || colors.success}`;
        toast.textContent = message;
        container.appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    }
</script>
@endsection
