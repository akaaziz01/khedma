@extends('layouts.admin')

@section('title', 'Users & Community Moderation | KHEDMA Admin')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    <!-- Loading skeleton -->
    <div id="loadingState" class="py-24 text-center">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-blue-600 border-t-transparent mb-4"></div>
        <p class="text-sm text-slate-500 font-semibold">Verifying administrator privileges & loading user directory...</p>
    </div>

    <!-- Unauthorized Banner if not admin -->
    <div id="unauthorizedBanner" class="hidden py-16 text-center max-w-md mx-auto space-y-4">
        <div class="w-16 h-16 rounded-3xl bg-red-100 text-red-600 flex items-center justify-center mx-auto font-black text-2xl">
            ✕
        </div>
        <h2 class="text-2xl font-black text-slate-900 tracking-tight">Access Restricted</h2>
        <p class="text-xs text-slate-500">
            This area requires Administrator permissions. Your current role is not authorized to view or manage platform users.
        </p>
        <div class="pt-2 flex items-center justify-center gap-3">
            <a href="/dashboard" class="px-4 py-2 bg-slate-900 text-white rounded-xl text-xs font-bold">Go to My Dashboard</a>
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
                    <span class="text-blue-600 dark:text-blue-400">Users & Moderation</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    User Accounts & Community Safety
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    View, search, verify, suspend or delete clients and providers to keep the community safe and legitimate.
                </p>
            </div>

            <div class="flex items-center gap-2 self-start sm:self-auto">
                <a href="/admin/dashboard" class="px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-800 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition flex items-center gap-1.5">
                    <i class="bi bi-speedometer2"></i> Dashboard Overview
                </a>
            </div>
        </div>

        <!-- User Metrics Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            <div class="bg-white dark:bg-slate-950/80 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Total Registered</span>
                <span id="metricTotalUsers" class="text-2xl font-black text-slate-900 dark:text-white">0</span>
                <p class="text-[11px] text-slate-400 mt-1">Platform members</p>
            </div>
            <div class="bg-white dark:bg-slate-950/80 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-blue-500 block mb-1">Clients</span>
                <span id="metricClientsCount" class="text-2xl font-black text-slate-900 dark:text-white">0</span>
                <p class="text-[11px] text-blue-600 dark:text-blue-400 font-semibold mt-1">Demanding services</p>
            </div>
            <div class="bg-white dark:bg-slate-950/80 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-500 block mb-1">Providers</span>
                <span id="metricProvidersCount" class="text-2xl font-black text-slate-900 dark:text-white">0</span>
                <p class="text-[11px] text-amber-600 dark:text-amber-400 font-semibold mt-1">Offering services</p>
            </div>
            <div class="bg-white dark:bg-slate-950/80 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-500 block mb-1">Verified Accounts</span>
                <span id="metricVerifiedCount" class="text-2xl font-black text-emerald-600 dark:text-emerald-400">0</span>
                <p class="text-[11px] text-emerald-600 dark:text-emerald-400 font-semibold mt-1">Identity verified</p>
            </div>
            <div class="bg-white dark:bg-slate-950/80 p-5 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs col-span-2 sm:col-span-1">
                <span class="text-[10px] font-bold uppercase tracking-wider text-red-500 block mb-1">Suspended</span>
                <span id="metricSuspendedCount" class="text-2xl font-black text-red-600 dark:text-red-400">0</span>
                <p class="text-[11px] text-red-600 dark:text-red-400 font-semibold mt-1">Access revoked</p>
            </div>
        </div>

        <!-- Users Management Card & Table -->
        <div class="bg-white dark:bg-slate-950 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden transition-colors duration-200">
            <!-- Header & Search Toolbar -->
            <div class="p-6 border-b border-slate-100 dark:border-slate-800 space-y-4">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-lg font-extrabold text-slate-900 dark:text-white tracking-tight">Community Directory</h2>
                            <span id="userCountBadge" class="text-[10px] font-black px-2 py-0.5 rounded-full bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-300">0</span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Search, filter, inspect profiles, toggle verification, suspend access, or permanently remove accounts.</p>
                    </div>

                    <!-- Role filter pills -->
                    <div class="flex items-center gap-1.5 p-1 bg-slate-100 dark:bg-slate-900 rounded-xl text-xs font-bold border border-transparent dark:border-slate-800 self-start md:self-auto shrink-0">
                        <button onclick="filterUsersRole('all')" id="userFilterAll" class="px-3 py-1 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-xs">All Roles</button>
                        <button onclick="filterUsersRole('client')" id="userFilterClient" class="px-3 py-1 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">Clients</button>
                        <button onclick="filterUsersRole('provider')" id="userFilterProvider" class="px-3 py-1 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">Providers</button>
                        <button onclick="filterUsersRole('admin')" id="userFilterAdmin" class="px-3 py-1 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">Admins</button>
                    </div>
                </div>

                <!-- Search & Filters Toolbar -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 pt-1">
                    <!-- Search input -->
                    <div class="relative flex-1">
                        <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input id="userSearchInput" type="text" oninput="handleUserSearchInput(this.value)"
                            placeholder="Search by name, email, phone, city, or business..."
                            class="w-full pl-9 pr-8 py-2 rounded-xl text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                        <button id="userSearchClearBtn" onclick="clearUserSearch()" class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xs">✕</button>
                    </div>

                    <!-- Status Filter -->
                    <div class="flex items-center gap-2 shrink-0">
                        <label class="text-[11px] font-bold text-slate-500 dark:text-slate-400 whitespace-nowrap">Status:</label>
                        <select id="userStatusSelect" onchange="handleUserStatusFilter(this.value)"
                            class="px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200 font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                            <option value="all">All Statuses</option>
                            <option value="active">Active Only</option>
                            <option value="suspended">Suspended Only</option>
                            <option value="pending">Pending Only</option>
                        </select>
                    </div>

                    <!-- Verification Filter -->
                    <div class="flex items-center gap-2 shrink-0">
                        <label class="text-[11px] font-bold text-slate-500 dark:text-slate-400 whitespace-nowrap">Verification:</label>
                        <select id="userVerificationSelect" onchange="handleUserVerificationFilter(this.value)"
                            class="px-3 py-2 rounded-xl text-xs bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-800 dark:text-slate-200 font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/50">
                            <option value="all">All Verification</option>
                            <option value="verified">Verified Only (✓)</option>
                            <option value="unverified">Unverified Only</option>
                        </select>
                    </div>

                    <!-- Reset Filters Button -->
                    <button id="resetUserFiltersBtn" onclick="resetAllUserFilters()"
                        class="hidden px-3 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 transition items-center gap-1.5 shrink-0">
                        <i class="bi bi-x-circle"></i> Reset
                    </button>
                </div>

                <!-- Results count & active filter summary -->
                <div class="flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400 pt-0.5">
                    <span id="userResultsSummary">Loading user directory...</span>
                </div>
            </div>

            <!-- Users Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                    <thead class="bg-slate-50 dark:bg-slate-900/60 text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800">
                        <tr>
                            <th class="py-3.5 px-6">User / Contact</th>
                            <th class="py-3.5 px-6">Role</th>
                            <th class="py-3.5 px-6">Verification</th>
                            <th class="py-3.5 px-6">Account Status</th>
                            <th class="py-3.5 px-6">City / Details</th>
                            <th class="py-3.5 px-6">Joined Date</th>
                            <th class="py-3.5 px-6 text-right">Moderation Actions</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody" class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        <!-- Populated dynamically via JS -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ===== MODALS ===== -->

<!-- User Details Inspection Modal -->
<div id="userDetailsModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl w-full max-w-2xl border border-slate-200 dark:border-slate-700 max-h-[90vh] flex flex-col overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-blue-500/10 text-blue-600 dark:text-blue-400 font-black text-lg flex items-center justify-center">
                    <i class="bi bi-person-badge"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white">User Account & Profile Details</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Inspect community member details and safety credentials.</p>
                </div>
            </div>
            <button onclick="closeUserDetailsModal()" class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 hover:text-slate-900 dark:hover:text-white transition text-xs font-bold">✕</button>
        </div>

        <div id="userDetailsModalBody" class="p-6 space-y-6 overflow-y-auto">
            <!-- Dynamically populated via JS -->
        </div>

        <div class="p-4 bg-slate-50 dark:bg-slate-900/80 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between shrink-0">
            <div id="userDetailsModalActionsLeft">
                <!-- Populated via JS (Delete button) -->
            </div>
            <button onclick="closeUserDetailsModal()" class="px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 text-xs font-bold transition">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Delete User Confirmation Modal -->
<div id="deleteUserModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl w-full max-w-md border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-red-100 dark:bg-red-950/50 text-red-600 dark:text-red-400 font-black text-lg flex items-center justify-center">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Delete User Account</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Irreversible community moderation action.</p>
                </div>
            </div>
            <button onclick="closeDeleteUserModal()" class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 hover:text-slate-900 dark:hover:text-white transition text-xs font-bold">✕</button>
        </div>

        <div class="p-6 space-y-4">
            <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">
                Are you sure you want to permanently delete user <strong id="deleteUserModalName" class="text-slate-900 dark:text-white"></strong>?
            </p>
            <div class="p-3.5 rounded-2xl bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-900/40 text-[11px] text-red-700 dark:text-red-300 space-y-1">
                <div class="font-bold flex items-center gap-1.5"><i class="bi bi-shield-x"></i> Permanent Consequences:</div>
                <ul class="list-disc list-inside space-y-0.5 text-red-600 dark:text-red-400">
                    <li>All active authentication tokens will be revoked immediately.</li>
                    <li>Profile, services, and associated account data will be removed.</li>
                    <li>This action cannot be undone.</li>
                </ul>
            </div>
            <input type="hidden" id="deleteTargetUserId" value="">
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeDeleteUserModal()" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 text-xs font-bold hover:bg-slate-50 dark:hover:bg-slate-800 transition">Cancel</button>
                <button type="button" id="confirmDeleteUserBtn" onclick="submitDeleteUser()" class="flex-1 px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold transition shadow-sm flex items-center justify-center gap-1.5">
                    <i class="bi bi-trash3"></i> Delete User
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast notification element -->
<div id="adminToast" class="fixed bottom-6 right-6 z-50 transform translate-y-20 opacity-0 transition-all duration-300 pointer-events-none flex items-center gap-3 px-4 py-3 rounded-2xl shadow-2xl text-xs font-bold text-white bg-slate-900 border border-slate-700">
    <i id="adminToastIcon" class="bi bi-check-circle-fill text-emerald-400 text-base"></i>
    <span id="adminToastMessage">Action completed</span>
</div>

@endsection

@section('scripts')
<script>
    let allUsers = [];
    let currentUserRoleFilter = 'all';
    let currentUserStatusFilter = 'all';
    let currentUserVerificationFilter = 'all';
    let currentUserSearchQuery = '';
    let currentViewingUser = null;

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

            await loadUsersDirectory(token);
        } catch(e) {
            window.location.href = '/login?admin=1';
        }
    });

    async function loadUsersDirectory(token) {
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
            const metrics = data.metrics || {};

            // Metrics
            document.getElementById('metricTotalUsers').textContent = metrics.total_users ?? 0;
            document.getElementById('metricClientsCount').textContent = metrics.clients_count ?? 0;
            document.getElementById('metricProvidersCount').textContent = metrics.providers_count ?? 0;
            document.getElementById('metricVerifiedCount').textContent = metrics.verified_users_count ?? 0;
            document.getElementById('metricSuspendedCount').textContent = metrics.suspended_users_count ?? 0;
            if (document.getElementById('userCountBadge')) {
                document.getElementById('userCountBadge').textContent = metrics.total_users ?? 0;
            }

            applyUserFilters();

            document.getElementById('loadingState').classList.add('hidden');
            document.getElementById('adminContent').classList.remove('hidden');
        } catch(e) {
            console.error(e);
        }
    }

    // =========================================================================
    // SEARCH & FILTER LOGIC FOR USERS
    // =========================================================================

    function handleUserSearchInput(query) {
        currentUserSearchQuery = (query || '').trim().toLowerCase();
        const clearBtn = document.getElementById('userSearchClearBtn');
        if (clearBtn) {
            clearBtn.classList.toggle('hidden', !currentUserSearchQuery);
        }
        updateResetButtonVisibility();
        applyUserFilters();
    }

    function clearUserSearch() {
        const input = document.getElementById('userSearchInput');
        if (input) input.value = '';
        currentUserSearchQuery = '';
        document.getElementById('userSearchClearBtn')?.classList.add('hidden');
        updateResetButtonVisibility();
        applyUserFilters();
    }

    function filterUsersRole(role) {
        currentUserRoleFilter = role;
        const buttons = ['all', 'client', 'provider', 'admin'];
        buttons.forEach(b => {
            const btn = document.getElementById('userFilter' + b.charAt(0).toUpperCase() + b.slice(1));
            if (btn) {
                if (b === role) {
                    btn.className = 'px-3 py-1 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-xs font-bold';
                } else {
                    btn.className = 'px-3 py-1 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white';
                }
            }
        });
        updateResetButtonVisibility();
        applyUserFilters();
    }

    function handleUserStatusFilter(val) {
        currentUserStatusFilter = val;
        updateResetButtonVisibility();
        applyUserFilters();
    }

    function handleUserVerificationFilter(val) {
        currentUserVerificationFilter = val;
        updateResetButtonVisibility();
        applyUserFilters();
    }

    function resetAllUserFilters() {
        currentUserSearchQuery = '';
        currentUserRoleFilter = 'all';
        currentUserStatusFilter = 'all';
        currentUserVerificationFilter = 'all';

        const searchInput = document.getElementById('userSearchInput');
        if (searchInput) searchInput.value = '';
        document.getElementById('userSearchClearBtn')?.classList.add('hidden');

        const statusSelect = document.getElementById('userStatusSelect');
        if (statusSelect) statusSelect.value = 'all';

        const verifSelect = document.getElementById('userVerificationSelect');
        if (verifSelect) verifSelect.value = 'all';

        ['all', 'client', 'provider', 'admin'].forEach(b => {
            const btn = document.getElementById('userFilter' + b.charAt(0).toUpperCase() + b.slice(1));
            if (btn) {
                btn.className = b === 'all'
                    ? 'px-3 py-1 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-xs font-bold'
                    : 'px-3 py-1 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white';
            }
        });

        updateResetButtonVisibility();
        applyUserFilters();
    }

    function updateResetButtonVisibility() {
        const hasFilters = currentUserSearchQuery !== '' ||
            currentUserRoleFilter !== 'all' ||
            currentUserStatusFilter !== 'all' ||
            currentUserVerificationFilter !== 'all';
        const resetBtn = document.getElementById('resetUserFiltersBtn');
        if (resetBtn) {
            resetBtn.classList.toggle('hidden', !hasFilters);
            resetBtn.classList.toggle('flex', hasFilters);
        }
    }

    function applyUserFilters() {
        let filtered = allUsers.slice();

        // 1. Role filter
        if (currentUserRoleFilter !== 'all') {
            filtered = filtered.filter(u => u.role === currentUserRoleFilter);
        }

        // 2. Status filter
        if (currentUserStatusFilter !== 'all') {
            filtered = filtered.filter(u => u.status === currentUserStatusFilter);
        }

        // 3. Verification filter
        if (currentUserVerificationFilter === 'verified') {
            filtered = filtered.filter(u => !!u.is_verified);
        } else if (currentUserVerificationFilter === 'unverified') {
            filtered = filtered.filter(u => !u.is_verified);
        }

        // 4. Search query
        if (currentUserSearchQuery) {
            filtered = filtered.filter(u => {
                const name = (u.name || '').toLowerCase();
                const email = (u.email || '').toLowerCase();
                const phone = (u.phone || '').toLowerCase();
                const clientCity = (u.client_profile?.city || '').toLowerCase();
                const providerCity = (u.provider_profile?.city || '').toLowerCase();
                const bizName = (u.provider_profile?.business_name || '').toLowerCase();
                const category = (u.provider_profile?.category || '').toLowerCase();

                return name.includes(currentUserSearchQuery) ||
                    email.includes(currentUserSearchQuery) ||
                    phone.includes(currentUserSearchQuery) ||
                    clientCity.includes(currentUserSearchQuery) ||
                    providerCity.includes(currentUserSearchQuery) ||
                    bizName.includes(currentUserSearchQuery) ||
                    category.includes(currentUserSearchQuery);
            });
        }

        const summaryEl = document.getElementById('userResultsSummary');
        if (summaryEl) {
            summaryEl.textContent = `Showing ${filtered.length} of ${allUsers.length} total users`;
        }

        renderUsersTable(filtered);
    }

    // =========================================================================
    // RENDER USERS TABLE
    // =========================================================================

    function renderUsersTable(users) {
        const tbody = document.getElementById('usersTableBody');
        tbody.innerHTML = '';

        if (!users.length) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="py-12 text-center text-xs text-slate-400 dark:text-slate-500 font-semibold">
                        <i class="bi bi-person-x text-3xl block mb-2 text-slate-300 dark:text-slate-600"></i>
                        No users match the selected search or filter criteria.
                    </td>
                </tr>
            `;
            return;
        }

        const adminUser = JSON.parse(localStorage.getItem('khedma_user') || '{}');

        users.forEach(u => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-50/70 dark:hover:bg-slate-900/40 transition';

            // Role badge
            let roleBadge = 'bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-300';
            if (u.role === 'provider') roleBadge = 'bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300';
            if (u.role === 'admin') roleBadge = 'bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-300';

            // Verification badge
            const isVerified = !!u.is_verified;
            const verifBadge = isVerified
                ? `<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-800"><i class="bi bi-patch-check-fill text-emerald-600 dark:text-emerald-400"></i> Verified</span>`
                : `<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700"><i class="bi bi-shield-x text-slate-400"></i> Unverified</span>`;

            // Status badge
            let statusBadge = 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300';
            if (u.status === 'suspended') statusBadge = 'bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-300';
            if (u.status === 'pending') statusBadge = 'bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300';

            // Location & details
            const city = u.client_profile?.city || u.provider_profile?.city || '—';
            let extraDetails = '';
            if (u.role === 'provider') {
                const bizName = u.provider_profile?.business_name || u.provider_profile?.category || '';
                const svcCount = u.services_count ?? 0;
                extraDetails = bizName ? `<div class="text-[11px] text-slate-400 dark:text-slate-500 truncate max-w-[170px]" title="${bizName}">${bizName} • ${svcCount} services</div>` : `<div class="text-[11px] text-slate-400 dark:text-slate-500">${svcCount} services</div>`;
            } else if (u.role === 'client') {
                const bkCount = u.client_bookings_count ?? 0;
                extraDetails = `<div class="text-[11px] text-slate-400 dark:text-slate-500">${bkCount} bookings</div>`;
            }

            const isSelf = adminUser && adminUser.id === u.id;
            let actionHtml = '';

            if (isSelf) {
                actionHtml = '<span class="text-xs text-slate-400 dark:text-slate-500 font-semibold italic">Current Admin</span>';
            } else {
                const nextStatus = u.status === 'active' ? 'suspended' : 'active';
                const nextStatusLabel = u.status === 'active' ? 'Suspend' : 'Activate';
                const nextStatusClass = u.status === 'active'
                    ? 'text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 border-red-200 dark:border-red-900/40'
                    : 'text-emerald-700 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 border-emerald-200 dark:border-emerald-900/40';

                const verifActionLabel = isVerified ? 'Unverify' : 'Verify';
                const verifActionIcon = isVerified ? 'bi-patch-minus text-amber-500' : 'bi-patch-check-fill text-emerald-500';
                const verifActionClass = isVerified
                    ? 'text-amber-700 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/30 border-amber-200 dark:border-amber-900/40'
                    : 'text-emerald-700 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 border-emerald-200 dark:border-emerald-900/40';

                const safeName = (u.name || 'User').replace(/'/g, "\\'");

                actionHtml = `
                    <div class="flex items-center justify-end gap-1.5 flex-wrap">
                        <button onclick="viewUserDetails(${u.id})"
                            class="px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition flex items-center gap-1"
                            title="View complete user profile">
                            <i class="bi bi-eye"></i> View
                        </button>
                        <button onclick="toggleUserVerification(${u.id}, ${!isVerified})"
                            class="px-2.5 py-1 rounded-lg border text-xs font-bold transition flex items-center gap-1 ${verifActionClass}"
                            title="${verifActionLabel} user account">
                            <i class="bi ${verifActionIcon}"></i> ${verifActionLabel}
                        </button>
                        <button onclick="toggleUserStatus(${u.id}, '${nextStatus}')"
                            class="px-2.5 py-1 rounded-lg border text-xs font-bold transition flex items-center gap-1 ${nextStatusClass}"
                            title="${nextStatusLabel} account">
                            <i class="bi ${u.status === 'active' ? 'bi-slash-circle' : 'bi-check-circle'}"></i> ${nextStatusLabel}
                        </button>
                        <button onclick="openDeleteUserModal(${u.id}, '${safeName}')"
                            class="px-2 py-1 rounded-lg border border-red-200 dark:border-red-900/40 text-xs font-bold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition"
                            title="Delete user account">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                `;
            }

            const initial = (u.name || 'U').charAt(0).toUpperCase();

            tr.innerHTML = `
                <td class="py-4 px-6">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-extrabold text-xs flex items-center justify-center shrink-0 border border-slate-200 dark:border-slate-700">
                            ${initial}
                        </div>
                        <div>
                            <div class="font-extrabold text-xs text-slate-900 dark:text-white flex items-center gap-1.5">
                                <span>${u.name}</span>
                                ${isVerified ? '<i class="bi bi-patch-check-fill text-emerald-500 text-[11px]" title="Verified community member"></i>' : ''}
                            </div>
                            <div class="text-[11px] text-slate-400 dark:text-slate-500">${u.email}</div>
                            ${u.phone ? `<div class="text-[10px] text-slate-400 dark:text-slate-500">${u.phone}</div>` : ''}
                        </div>
                    </div>
                </td>
                <td class="py-4 px-6">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider ${roleBadge}">
                        ${u.role}
                    </span>
                </td>
                <td class="py-4 px-6">
                    ${verifBadge}
                </td>
                <td class="py-4 px-6">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider ${statusBadge}">
                        ${u.status}
                    </span>
                </td>
                <td class="py-4 px-6 text-xs text-slate-700 dark:text-slate-300">
                    <div class="font-semibold">${city}</div>
                    ${extraDetails}
                </td>
                <td class="py-4 px-6 text-xs text-slate-400 dark:text-slate-500 whitespace-nowrap">
                    ${new Date(u.created_at).toLocaleDateString()}
                </td>
                <td class="py-4 px-6 text-right">
                    ${actionHtml}
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    // =========================================================================
    // MODERATION ACTIONS (VERIFY, SUSPEND, DELETE)
    // =========================================================================

    async function toggleUserVerification(userId, isVerified) {
        const token = localStorage.getItem('khedma_token');
        try {
            const res = await fetch(`/api/v1/admin/users/${userId}/verify`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ is_verified: isVerified })
            });

            const json = await res.json();
            if (res.ok) {
                showAdminToast(json.message || 'User verification updated successfully.');
                await loadUsersDirectory(token);
                if (currentViewingUser && currentViewingUser.id === userId) {
                    await viewUserDetails(userId);
                }
            } else {
                showAdminToast(json.message || 'Error updating verification status.', 'error');
            }
        } catch(e) {
            console.error(e);
            showAdminToast('Network or server error updating verification.', 'error');
        }
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

            const json = await res.json();
            if (res.ok) {
                showAdminToast(json.message || `User status changed to ${nextStatus}.`);
                await loadUsersDirectory(token);
                if (currentViewingUser && currentViewingUser.id === userId) {
                    await viewUserDetails(userId);
                }
            } else {
                showAdminToast(json.message || 'Error updating user status.', 'error');
            }
        } catch(e) {
            console.error(e);
            showAdminToast('Network or server error updating user status.', 'error');
        }
    }

    function openDeleteUserModal(userId, userName) {
        document.getElementById('deleteTargetUserId').value = userId;
        document.getElementById('deleteUserModalName').textContent = `"${userName}"`;
        document.getElementById('deleteUserModal').classList.remove('hidden');
    }

    function closeDeleteUserModal() {
        document.getElementById('deleteUserModal').classList.add('hidden');
        document.getElementById('deleteTargetUserId').value = '';
    }

    async function submitDeleteUser() {
        const userId = document.getElementById('deleteTargetUserId').value;
        if (!userId) return;

        const token = localStorage.getItem('khedma_token');
        const btn = document.getElementById('confirmDeleteUserBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="inline-block animate-spin mr-1">⌛</span> Deleting...';

        try {
            const res = await fetch(`/api/v1/admin/users/${userId}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const json = await res.json();
            if (res.ok) {
                closeDeleteUserModal();
                closeUserDetailsModal();
                showAdminToast(json.message || 'User account successfully deleted.');
                await loadUsersDirectory(token);
            } else {
                showAdminToast(json.message || 'Could not delete user.', 'error');
            }
        } catch(e) {
            console.error(e);
            showAdminToast('Network error deleting user.', 'error');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-trash3"></i> Delete User';
        }
    }

    // =========================================================================
    // USER DETAILS INSPECTION MODAL
    // =========================================================================

    async function viewUserDetails(userId) {
        const token = localStorage.getItem('khedma_token');
        const modal = document.getElementById('userDetailsModal');
        const body = document.getElementById('userDetailsModalBody');
        const actionsLeft = document.getElementById('userDetailsModalActionsLeft');

        body.innerHTML = `
            <div class="py-12 text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-blue-600 border-t-transparent mb-2"></div>
                <p class="text-xs text-slate-400">Loading user profile and statistics...</p>
            </div>
        `;
        actionsLeft.innerHTML = '';
        modal.classList.remove('hidden');

        try {
            const res = await fetch(`/api/v1/admin/users/${userId}`, {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            if (!res.ok) {
                body.innerHTML = '<div class="p-6 text-center text-xs text-red-500">Failed to load user details.</div>';
                return;
            }

            const json = await res.json();
            const u = json.user;
            const stats = json.stats || {};
            currentViewingUser = u;

            const isVerified = !!u.is_verified;
            const adminUser = JSON.parse(localStorage.getItem('khedma_user') || '{}');
            const isSelf = adminUser && adminUser.id === u.id;

            let roleBadge = 'bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-300';
            if (u.role === 'provider') roleBadge = 'bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300';
            if (u.role === 'admin') roleBadge = 'bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-300';

            let profileSectionHtml = '';
            if (u.role === 'provider' && u.provider_profile) {
                const pp = u.provider_profile;
                const skillsHtml = Array.isArray(pp.skills) && pp.skills.length
                    ? pp.skills.map(s => `<span class="px-2 py-0.5 rounded-lg bg-slate-100 dark:bg-slate-800 text-[10px] font-semibold text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">${s}</span>`).join(' ')
                    : '<span class="text-xs text-slate-400">No skills listed</span>';

                profileSectionHtml = `
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/80 dark:border-slate-800 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider">Provider Business Profile</span>
                            <span class="text-xs font-bold ${pp.is_available ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400'}">
                                ${pp.is_available ? '● Available for bookings' : '○ Currently unavailable'}
                            </span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <div>
                                <span class="text-slate-400 block text-[11px]">Business / Trade Name</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200">${pp.business_name || '—'}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[11px]">Primary Category</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200">${pp.category || '—'}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[11px]">Hourly Rate</span>
                                <span class="font-bold text-emerald-600 dark:text-emerald-400">${pp.hourly_rate ? parseFloat(pp.hourly_rate).toFixed(0) + ' TND/hr' : 'Not specified'}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[11px]">Base Service City</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200">${pp.city || '—'}</span>
                            </div>
                        </div>
                        ${pp.bio ? `
                            <div class="pt-2 border-t border-slate-200/60 dark:border-slate-800">
                                <span class="text-slate-400 block text-[11px] mb-1">Biography / About</span>
                                <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed">${pp.bio}</p>
                            </div>
                        ` : ''}
                        <div class="pt-2 border-t border-slate-200/60 dark:border-slate-800">
                            <span class="text-slate-400 block text-[11px] mb-1.5">Skills & Specializations</span>
                            <div class="flex flex-wrap gap-1.5">${skillsHtml}</div>
                        </div>
                    </div>
                `;
            } else if (u.role === 'client' && u.client_profile) {
                const cp = u.client_profile;
                profileSectionHtml = `
                    <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/80 dark:border-slate-800 space-y-3">
                        <span class="text-xs font-bold text-slate-900 dark:text-white uppercase tracking-wider block">Client Residence Profile</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                            <div>
                                <span class="text-slate-400 block text-[11px]">City / Governorate</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200">${cp.city || '—'}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[11px]">Address / Residence</span>
                                <span class="font-bold text-slate-800 dark:text-slate-200">${cp.address || 'Not specified'}</span>
                            </div>
                        </div>
                    </div>
                `;
            }

            body.innerHTML = `
                <!-- User Identity Card -->
                <div class="flex items-start sm:items-center justify-between gap-4 p-4 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/80 dark:border-slate-800 flex-wrap">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-black text-xl flex items-center justify-center shrink-0 shadow-md">
                            ${(u.name || 'U').charAt(0).toUpperCase()}
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <h4 class="text-base font-extrabold text-slate-900 dark:text-white">${u.name}</h4>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider ${roleBadge}">
                                    ${u.role}
                                </span>
                            </div>
                            <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">${u.email} • ${u.phone || 'No phone'}</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        ${isVerified
                            ? `<span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-xs font-black bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 border border-emerald-300 dark:border-emerald-800"><i class="bi bi-patch-check-fill"></i> Verified Community Member</span>`
                            : `<span class="inline-flex items-center gap-1 px-3 py-1 rounded-xl text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-700"><i class="bi bi-shield-x"></i> Unverified Account</span>`
                        }
                    </div>
                </div>

                <!-- Moderation Control Strip -->
                ${!isSelf ? `
                    <div class="p-4 rounded-2xl bg-blue-50/50 dark:bg-blue-950/20 border border-blue-200/60 dark:border-blue-900/40 flex items-center justify-between gap-3 flex-wrap">
                        <div>
                            <span class="text-xs font-extrabold text-slate-900 dark:text-white block">Community Safety Controls</span>
                            <span class="text-[11px] text-slate-500 dark:text-slate-400">Account status is <strong>${u.status}</strong> • Verification is <strong>${isVerified ? 'granted' : 'pending/none'}</strong></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button onclick="toggleUserVerification(${u.id}, ${!isVerified})"
                                class="px-3 py-1.5 rounded-xl border text-xs font-bold transition flex items-center gap-1.5 ${isVerified ? 'border-amber-300 text-amber-700 hover:bg-amber-100 dark:text-amber-300 dark:hover:bg-amber-950/50' : 'bg-emerald-600 hover:bg-emerald-700 text-white'}">
                                <i class="bi ${isVerified ? 'bi-patch-minus' : 'bi-patch-check-fill'}"></i>
                                ${isVerified ? 'Revoke Verification' : 'Verify Account'}
                            </button>
                            <button onclick="toggleUserStatus(${u.id}, '${u.status === 'active' ? 'suspended' : 'active'}')"
                                class="px-3 py-1.5 rounded-xl border text-xs font-bold transition flex items-center gap-1.5 ${u.status === 'active' ? 'border-red-300 text-red-600 hover:bg-red-50 dark:border-red-900/60 dark:text-red-400 dark:hover:bg-red-950/40' : 'bg-emerald-600 hover:bg-emerald-700 text-white'}">
                                <i class="bi ${u.status === 'active' ? 'bi-slash-circle' : 'bi-check-circle'}"></i>
                                ${u.status === 'active' ? 'Suspend Account' : 'Reactivate Account'}
                            </button>
                        </div>
                    </div>
                ` : ''}

                <!-- Role Profile Section -->
                ${profileSectionHtml}

                <!-- Key Metrics & Timestamps -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                    <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/80 dark:border-slate-800">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Bookings</span>
                        <span class="text-lg font-black text-slate-900 dark:text-white">${stats.client_bookings_count || stats.provider_bookings_count || 0}</span>
                    </div>
                    <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/80 dark:border-slate-800">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Services</span>
                        <span class="text-lg font-black text-slate-900 dark:text-white">${stats.services_count || 0}</span>
                    </div>
                    <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/80 dark:border-slate-800">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Reviews</span>
                        <span class="text-lg font-black text-slate-900 dark:text-white">${stats.provider_reviews_count || 0}</span>
                    </div>
                    <div class="p-3 rounded-2xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200/80 dark:border-slate-800">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Rating</span>
                        <span class="text-lg font-black text-amber-500">${stats.average_rating ? stats.average_rating + ' ★' : '—'}</span>
                    </div>
                </div>

                <div class="text-[11px] text-slate-400 dark:text-slate-500 flex items-center justify-between border-t border-slate-100 dark:border-slate-800 pt-3">
                    <span>Account ID: #${u.id}</span>
                    <span>Joined: ${new Date(u.created_at).toLocaleDateString()} ${u.verified_at ? '• Verified on ' + new Date(u.verified_at).toLocaleDateString() : ''}</span>
                </div>
            `;

            if (!isSelf) {
                const safeName = (u.name || 'User').replace(/'/g, "\\'");
                actionsLeft.innerHTML = `
                    <button onclick="openDeleteUserModal(${u.id}, '${safeName}')"
                        class="px-3 py-2 rounded-xl border border-red-200 dark:border-red-900/40 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 text-xs font-bold transition flex items-center gap-1.5">
                        <i class="bi bi-trash3"></i> Delete Account
                    </button>
                `;
            }
        } catch(e) {
            console.error(e);
            body.innerHTML = '<div class="p-6 text-center text-xs text-red-500">Error loading details.</div>';
        }
    }

    function closeUserDetailsModal() {
        document.getElementById('userDetailsModal').classList.add('hidden');
        currentViewingUser = null;
    }

    // =========================================================================
    // TOAST NOTIFICATION UTILITY
    // =========================================================================

    function showAdminToast(message, type = 'success') {
        const toast = document.getElementById('adminToast');
        const icon = document.getElementById('adminToastIcon');
        const msg = document.getElementById('adminToastMessage');
        if (!toast || !msg) return;

        msg.textContent = message;
        if (type === 'error') {
            icon.className = 'bi bi-exclamation-triangle-fill text-red-400 text-base';
        } else {
            icon.className = 'bi bi-check-circle-fill text-emerald-400 text-base';
        }

        toast.classList.remove('translate-y-20', 'opacity-0');
        toast.classList.add('translate-y-0', 'opacity-100');

        setTimeout(() => {
            toast.classList.remove('translate-y-0', 'opacity-100');
            toast.classList.add('translate-y-20', 'opacity-0');
        }, 3200);
    }

    // Expose globals for HTML inline events
    window.loadUsersDirectory = loadUsersDirectory;
    window.filterUsersRole = filterUsersRole;
    window.handleUserSearchInput = handleUserSearchInput;
    window.clearUserSearch = clearUserSearch;
    window.handleUserStatusFilter = handleUserStatusFilter;
    window.handleUserVerificationFilter = handleUserVerificationFilter;
    window.resetAllUserFilters = resetAllUserFilters;
    window.toggleUserStatus = toggleUserStatus;
    window.toggleUserVerification = toggleUserVerification;
    window.viewUserDetails = viewUserDetails;
    window.closeUserDetailsModal = closeUserDetailsModal;
    window.openDeleteUserModal = openDeleteUserModal;
    window.closeDeleteUserModal = closeDeleteUserModal;
    window.submitDeleteUser = submitDeleteUser;
    window.showAdminToast = showAdminToast;
</script>
@endsection
