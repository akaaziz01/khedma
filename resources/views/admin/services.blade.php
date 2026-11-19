@extends('layouts.admin')

@section('title', 'Services Catalog & Moderation | KHEDMA Admin')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    <!-- Loading skeleton -->
    <div id="loadingState" class="py-24 text-center">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-emerald-600 border-t-transparent mb-4"></div>
        <p class="text-sm text-slate-500 font-semibold">Loading services catalog & moderation queues...</p>
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
                    <span class="text-emerald-600 dark:text-emerald-400">Services Catalog</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    Services Catalog & Moderation
                </h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Manage canonical service categories and moderate listings submitted by providers.
                </p>
            </div>

            <div class="flex items-center gap-2 self-start sm:self-auto">
                <a href="/admin/dashboard" class="px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-800 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition flex items-center gap-1.5">
                    <i class="bi bi-speedometer2"></i> Dashboard Overview
                </a>
            </div>
        </div>

        <!-- Section 1: Service Categories Management -->
        <div class="bg-white dark:bg-slate-950 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-extrabold text-slate-900 dark:text-white tracking-tight">Service Categories</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Configure service classification taxonomy across the marketplace.</p>
                </div>
                <button onclick="openCategoryModal()" class="shrink-0 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition flex items-center gap-2 shadow-sm">
                    <i class="bi bi-plus-lg"></i> New Category
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                    <thead class="bg-slate-50 dark:bg-slate-900/60 text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800">
                        <tr>
                            <th class="py-3.5 px-6">Category</th>
                            <th class="py-3.5 px-6">Slug</th>
                            <th class="py-3.5 px-6">Services</th>
                            <th class="py-3.5 px-6">Status</th>
                            <th class="py-3.5 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="categoriesTableBody" class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        <!-- Populated dynamically -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section 2: Service Listings Moderation -->
        <div class="bg-white dark:bg-slate-950 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <h2 class="text-lg font-extrabold text-slate-900 dark:text-white tracking-tight">Provider Service Listings</h2>
                        <span id="pendingServicesBadge" class="hidden px-2 py-0.5 rounded-full bg-amber-500 text-white text-[10px] font-black">0 Pending</span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Review, approve, reject, or moderate provider service offerings.</p>
                </div>
                <!-- Status filter pills -->
                <div class="flex items-center gap-1.5 p-1 bg-slate-100 dark:bg-slate-900 rounded-xl text-xs font-bold border border-transparent dark:border-slate-800 shrink-0">
                    <button onclick="filterServices('all')" id="svcFilterAll" class="px-3 py-1 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-xs">All</button>
                    <button onclick="filterServices('pending')" id="svcFilterPending" class="px-3 py-1 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">Pending</button>
                    <button onclick="filterServices('approved')" id="svcFilterApproved" class="px-3 py-1 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">Approved</button>
                    <button onclick="filterServices('rejected')" id="svcFilterRejected" class="px-3 py-1 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">Rejected</button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                    <thead class="bg-slate-50 dark:bg-slate-900/60 text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800">
                        <tr>
                            <th class="py-3.5 px-6">Service</th>
                            <th class="py-3.5 px-6">Provider</th>
                            <th class="py-3.5 px-6">Category</th>
                            <th class="py-3.5 px-6">Price</th>
                            <th class="py-3.5 px-6">Status</th>
                            <th class="py-3.5 px-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="servicesTableBody" class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        <!-- Populated dynamically -->
                    </tbody>
                </table>
            </div>
            <div id="servicesPagination" class="p-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs text-slate-500 hidden">
                <span id="servicesPaginationInfo"></span>
                <div class="flex gap-2">
                    <button onclick="loadServicesPage(currentServicesPage - 1)" id="svcPrevBtn" class="px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 font-bold disabled:opacity-40 hover:bg-slate-50 dark:hover:bg-slate-800 transition">← Prev</button>
                    <button onclick="loadServicesPage(currentServicesPage + 1)" id="svcNextBtn" class="px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 font-bold disabled:opacity-40 hover:bg-slate-50 dark:hover:bg-slate-800 transition">Next →</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===== MODALS ===== -->

<!-- Category Create / Edit Modal -->
<div id="categoryModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl w-full max-w-md border border-slate-200 dark:border-slate-700">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <h3 id="categoryModalTitle" class="text-base font-extrabold text-slate-900 dark:text-white">New Category</h3>
            <button onclick="closeCategoryModal()" class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 hover:text-slate-900 dark:hover:text-white transition text-xs font-bold">✕</button>
        </div>
        <form id="categoryForm" class="p-6 space-y-4" onsubmit="submitCategory(event)">
            <input type="hidden" id="categoryId" value="">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Name <span class="text-red-500">*</span></label>
                <input id="categoryName" type="text" required maxlength="100"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    placeholder="e.g. Plumbing">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Icon</label>
                <input id="categoryIcon" type="text" maxlength="100"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                    placeholder="🔧 or bi-tools">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Description</label>
                <textarea id="categoryDescription" rows="2" maxlength="500"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 resize-none"
                    placeholder="Short description…"></textarea>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex-1">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Sort Order</label>
                    <input id="categorySortOrder" type="number" min="0" value="0"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div class="flex-1">
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Active</label>
                    <select id="categoryIsActive"
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>
                </div>
            </div>
            <div id="categoryFormError" class="hidden text-xs text-red-600 dark:text-red-400 font-semibold p-3 bg-red-50 dark:bg-red-950/30 rounded-xl"></div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeCategoryModal()" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 text-xs font-bold hover:bg-slate-50 dark:hover:bg-slate-800 transition">Cancel</button>
                <button type="submit" id="categorySubmitBtn" class="flex-1 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-sm">Save Category</button>
            </div>
        </form>
    </div>
</div>

<!-- Reject Service Modal -->
<div id="rejectModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl w-full max-w-md border border-slate-200 dark:border-slate-700">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Reject Service</h3>
            <button onclick="closeRejectModal()" class="w-8 h-8 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 hover:text-slate-900 dark:hover:text-white transition text-xs font-bold">✕</button>
        </div>
        <div class="p-6 space-y-4">
            <p id="rejectModalServiceName" class="text-sm font-bold text-slate-900 dark:text-white"></p>
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Rejection Reason <span class="text-red-500">*</span></label>
                <textarea id="rejectionReason" rows="3" maxlength="500"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500 resize-none"
                    placeholder="Explain why this listing is being rejected…"></textarea>
            </div>
            <div id="rejectFormError" class="hidden text-xs text-red-600 dark:text-red-400 font-semibold"></div>
            <div class="flex gap-3 pt-2">
                <button onclick="closeRejectModal()" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400 text-xs font-bold hover:bg-slate-50 dark:hover:bg-slate-800 transition">Cancel</button>
                <button onclick="submitRejection()" class="flex-1 px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold transition shadow-sm">Confirm Reject</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    let allCategories = [];
    let currentServicesFilter = 'all';
    let currentServicesPage = 1;
    let servicesLastPage = 1;
    let pendingRejectServiceId = null;

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

            await loadCategories();
            await loadServicesPage(1);

            document.getElementById('loadingState').classList.add('hidden');
            document.getElementById('adminContent').classList.remove('hidden');
        } catch(e) {
            window.location.href = '/login?admin=1';
        }
    });

    // =========================================================================
    // CATEGORIES
    // =========================================================================

    async function loadCategories() {
        const token = localStorage.getItem('khedma_token');
        try {
            const res = await fetch('/api/v1/categories?include_inactive=true', {
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
            });
            const json = await res.json();
            allCategories = json.data || [];
            renderCategoriesTable(allCategories);
        } catch(e) { console.error(e); }
    }

    function renderCategoriesTable(categories) {
        const tbody = document.getElementById('categoriesTableBody');
        tbody.innerHTML = '';
        if (!categories.length) {
            tbody.innerHTML = '<tr><td colspan="5" class="py-10 text-center text-xs text-slate-400 font-semibold">No categories yet.</td></tr>';
            return;
        }
        categories.forEach(cat => {
            const activeBadge = cat.is_active
                ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300'
                : 'bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400';
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-50/70 dark:hover:bg-slate-900/40 transition';
            tr.innerHTML = `
                <td class="py-4 px-6">
                    <div class="flex items-center gap-2">
                        <span class="text-lg">${cat.icon || '📦'}</span>
                        <div>
                            <div class="font-extrabold text-xs text-slate-900 dark:text-white">${cat.name}</div>
                            <div class="text-[11px] text-slate-400 dark:text-slate-500 max-w-[180px] truncate">${cat.description || ''}</div>
                        </div>
                    </div>
                </td>
                <td class="py-4 px-6 font-mono text-xs text-slate-500 dark:text-slate-400">${cat.slug}</td>
                <td class="py-4 px-6 text-xs font-bold text-slate-700 dark:text-slate-300">${cat.services_count ?? 0}</td>
                <td class="py-4 px-6">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider ${activeBadge}">
                        ${cat.is_active ? 'Active' : 'Inactive'}
                    </span>
                </td>
                <td class="py-4 px-6 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <button onclick="openCategoryModal(${JSON.stringify(cat).replace(/"/g, '&quot;')})"
                            class="px-3 py-1 rounded-lg border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                            Edit
                        </button>
                        <button onclick="deleteCategory(${cat.id}, '${cat.name}')"
                            class="px-3 py-1 rounded-lg border border-red-200 dark:border-red-900/40 text-xs font-bold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition">
                            Delete
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function openCategoryModal(cat = null) {
        document.getElementById('categoryId').value        = cat ? cat.id : '';
        document.getElementById('categoryName').value      = cat ? cat.name : '';
        document.getElementById('categoryIcon').value      = cat ? (cat.icon || '') : '';
        document.getElementById('categoryDescription').value = cat ? (cat.description || '') : '';
        document.getElementById('categorySortOrder').value = cat ? cat.sort_order : 0;
        document.getElementById('categoryIsActive').value  = cat ? (cat.is_active ? '1' : '0') : '1';
        document.getElementById('categoryModalTitle').textContent = cat ? 'Edit Category' : 'New Category';
        document.getElementById('categoryFormError').classList.add('hidden');
        document.getElementById('categoryModal').classList.remove('hidden');
    }

    function closeCategoryModal() {
        document.getElementById('categoryModal').classList.add('hidden');
    }

    async function submitCategory(e) {
        e.preventDefault();
        const token = localStorage.getItem('khedma_token');
        const id    = document.getElementById('categoryId').value;
        const isEdit = !!id;
        const url    = isEdit ? `/api/v1/admin/categories/${id}` : '/api/v1/admin/categories';
        const method = isEdit ? 'PATCH' : 'POST';

        const body = {
            name:        document.getElementById('categoryName').value,
            icon:        document.getElementById('categoryIcon').value,
            description: document.getElementById('categoryDescription').value,
            sort_order:  parseInt(document.getElementById('categorySortOrder').value) || 0,
            is_active:   document.getElementById('categoryIsActive').value === '1',
        };

        const btn = document.getElementById('categorySubmitBtn');
        btn.textContent = 'Saving…';
        btn.disabled = true;

        try {
            const res = await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify(body),
            });

            const json = await res.json();
            if (!res.ok) {
                const errEl = document.getElementById('categoryFormError');
                const msgs  = json.errors ? Object.values(json.errors).flat().join(' ') : (json.message || 'Error saving.');
                errEl.textContent = msgs;
                errEl.classList.remove('hidden');
            } else {
                closeCategoryModal();
                loadCategories();
            }
        } catch(e) { console.error(e); }
        finally {
            btn.textContent = 'Save Category';
            btn.disabled = false;
        }
    }

    async function deleteCategory(id, name) {
        if (!confirm(`Delete category "${name}"? This cannot be undone.`)) return;
        const token = localStorage.getItem('khedma_token');
        try {
            const res  = await fetch(`/api/v1/admin/categories/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
            });
            const json = await res.json();
            if (!res.ok) { alert(json.message || 'Could not delete.'); return; }
            loadCategories();
        } catch(e) { console.error(e); }
    }

    // =========================================================================
    // SERVICE LISTINGS MODERATION
    // =========================================================================

    async function loadServicesPage(page = 1) {
        if (page < 1 || page > servicesLastPage) return;
        const token  = localStorage.getItem('khedma_token');
        const status = currentServicesFilter !== 'all' ? `&status=${currentServicesFilter}` : '';
        try {
            const res  = await fetch(`/api/v1/admin/services?per_page=15&page=${page}${status}`, {
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
            });
            const json = await res.json();
            currentServicesPage = json.pagination?.current_page || 1;
            servicesLastPage    = json.pagination?.last_page || 1;
            renderServicesTable(json.data || []);
            updateServicesPagination(json.pagination);

            if (currentServicesFilter === 'all' || currentServicesFilter === 'pending') {
                const pending = (json.data || []).filter(s => s.status === 'pending').length;
                const badge   = document.getElementById('pendingServicesBadge');
                if (pending > 0) {
                    badge.textContent = `${pending} Pending`;
                    badge.classList.remove('hidden');
                } else {
                    badge.classList.add('hidden');
                }
            }
        } catch(e) { console.error(e); }
    }

    function updateServicesPagination(pagination) {
        if (!pagination) return;
        const wrap = document.getElementById('servicesPagination');
        if (pagination.last_page <= 1) { wrap.classList.add('hidden'); return; }
        wrap.classList.remove('hidden');
        document.getElementById('servicesPaginationInfo').textContent =
            `Page ${pagination.current_page} of ${pagination.last_page} (${pagination.total} services)`;
        document.getElementById('svcPrevBtn').disabled = pagination.current_page <= 1;
        document.getElementById('svcNextBtn').disabled = pagination.current_page >= pagination.last_page;
    }

    function filterServices(status) {
        currentServicesFilter = status;
        currentServicesPage   = 1;
        ['all','pending','approved','rejected'].forEach(s => {
            const btn = document.getElementById('svcFilter' + s.charAt(0).toUpperCase() + s.slice(1));
            if (!btn) return;
            btn.className = s === status
                ? 'px-3 py-1 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-xs font-bold'
                : 'px-3 py-1 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white';
        });
        loadServicesPage(1);
    }

    function renderServicesTable(services) {
        const tbody = document.getElementById('servicesTableBody');
        tbody.innerHTML = '';
        if (!services.length) {
            tbody.innerHTML = '<tr><td colspan="6" class="py-10 text-center text-xs text-slate-400 font-semibold">No services found.</td></tr>';
            return;
        }
        services.forEach(svc => {
            const statusStyles = {
                pending:  'bg-amber-100  dark:bg-amber-900/40  text-amber-800  dark:text-amber-300',
                approved: 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300',
                rejected: 'bg-red-100   dark:bg-red-900/40    text-red-800    dark:text-red-300',
            };
            const badge = statusStyles[svc.status] || '';
            const catName = svc.category_detail?.name || svc.category || '—';

            let actions = '';
            if (svc.status === 'pending') {
                actions = `
                    <button onclick="approveService(${svc.id})"
                        class="px-3 py-1 rounded-lg border border-emerald-200 dark:border-emerald-900/40 text-xs font-bold text-emerald-700 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 transition">
                        ✓ Approve
                    </button>
                    <button onclick="openRejectModal(${svc.id}, '${svc.title.replace(/'/g, "\\'")}')"
                        class="px-3 py-1 rounded-lg border border-red-200 dark:border-red-900/40 text-xs font-bold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition">
                        ✕ Reject
                    </button>`;
            } else if (svc.status === 'approved') {
                const toggleLabel = svc.is_active ? 'Deactivate' : 'Activate';
                const toggleNext  = !svc.is_active;
                actions = `
                    <button onclick="toggleServiceActive(${svc.id}, ${toggleNext})"
                        class="px-3 py-1 rounded-lg border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                        ${toggleLabel}
                    </button>
                    <button onclick="openRejectModal(${svc.id}, '${svc.title.replace(/'/g, "\\'")}')"
                        class="px-3 py-1 rounded-lg border border-red-200 dark:border-red-900/40 text-xs font-bold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition">
                        Reject
                    </button>`;
            } else {
                actions = `
                    <button onclick="approveService(${svc.id})"
                        class="px-3 py-1 rounded-lg border border-emerald-200 dark:border-emerald-900/40 text-xs font-bold text-emerald-700 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 transition">
                        Re-approve
                    </button>`;
            }
            actions += `
                <button onclick="deleteService(${svc.id}, '${svc.title.replace(/'/g, "\\'")}')"
                    class="px-3 py-1 rounded-lg border border-red-200 dark:border-red-900/40 text-xs font-bold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 transition">
                    🗑
                </button>`;

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-50/70 dark:hover:bg-slate-900/40 transition';
            tr.innerHTML = `
                <td class="py-4 px-6">
                    <div class="font-extrabold text-xs text-slate-900 dark:text-white">${svc.title}</div>
                    <div class="text-[11px] text-slate-400 dark:text-slate-500">${parseFloat(svc.duration_hours).toFixed(1)}h</div>
                </td>
                <td class="py-4 px-6 text-xs font-bold text-slate-700 dark:text-slate-300">${svc.provider?.name || '—'}</td>
                <td class="py-4 px-6 text-xs text-slate-500 dark:text-slate-400">${catName}</td>
                <td class="py-4 px-6 font-bold text-xs text-slate-900 dark:text-white">${parseFloat(svc.price).toFixed(0)} TND</td>
                <td class="py-4 px-6">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider ${badge}">
                        ${svc.status}
                    </span>
                    ${!svc.is_active && svc.status === 'approved' ? '<span class="ml-1 text-[10px] text-slate-400 dark:text-slate-500">(off)</span>' : ''}
                </td>
                <td class="py-4 px-6">
                    <div class="flex items-center justify-end gap-1.5 flex-wrap">${actions}</div>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    async function approveService(id) {
        const token = localStorage.getItem('khedma_token');
        try {
            const res = await fetch(`/api/v1/admin/services/${id}/moderate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({ action: 'approve' }),
            });
            if (res.ok) loadServicesPage(currentServicesPage);
        } catch(e) { console.error(e); }
    }

    function openRejectModal(id, name) {
        pendingRejectServiceId = id;
        document.getElementById('rejectModalServiceName').textContent = `"${name}"`;
        document.getElementById('rejectionReason').value = '';
        document.getElementById('rejectFormError').classList.add('hidden');
        document.getElementById('rejectModal').classList.remove('hidden');
    }

    function closeRejectModal() {
        document.getElementById('rejectModal').classList.add('hidden');
        pendingRejectServiceId = null;
    }

    async function submitRejection() {
        const reason = document.getElementById('rejectionReason').value.trim();
        if (!reason) {
            document.getElementById('rejectFormError').textContent = 'Rejection reason is required.';
            document.getElementById('rejectFormError').classList.remove('hidden');
            return;
        }
        const token = localStorage.getItem('khedma_token');
        try {
            const res = await fetch(`/api/v1/admin/services/${pendingRejectServiceId}/moderate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({ action: 'reject', rejection_reason: reason }),
            });
            if (res.ok) { closeRejectModal(); loadServicesPage(currentServicesPage); }
        } catch(e) { console.error(e); }
    }

    async function toggleServiceActive(id, isActive) {
        const token = localStorage.getItem('khedma_token');
        try {
            const res = await fetch(`/api/v1/admin/services/${id}/active`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({ is_active: isActive }),
            });
            if (res.ok) loadServicesPage(currentServicesPage);
        } catch(e) { console.error(e); }
    }

    async function deleteService(id, name) {
        if (!confirm(`Permanently delete service "${name}"?`)) return;
        const token = localStorage.getItem('khedma_token');
        try {
            const res  = await fetch(`/api/v1/admin/services/${id}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
            });
            const json = await res.json();
            if (!res.ok) { alert(json.message || 'Cannot delete.'); return; }
            loadServicesPage(currentServicesPage);
        } catch(e) { console.error(e); }
    }

    window.openCategoryModal = openCategoryModal;
    window.closeCategoryModal = closeCategoryModal;
    window.submitCategory = submitCategory;
    window.deleteCategory = deleteCategory;
    window.filterServices = filterServices;
    window.loadServicesPage = loadServicesPage;
    window.approveService = approveService;
    window.openRejectModal = openRejectModal;
    window.closeRejectModal = closeRejectModal;
    window.submitRejection = submitRejection;
    window.toggleServiceActive = toggleServiceActive;
    window.deleteService = deleteService;
</script>
@endsection
