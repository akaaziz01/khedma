@extends('layouts.app')

@section('title', 'My Services | Khedma Provider')

@section('content')
<div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-8">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-200 dark:border-slate-800">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-amber-600 dark:text-amber-400 uppercase tracking-wider mb-1">
                <i class="bi bi-tools"></i>
                <span>Provider Catalog</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                My Services Catalog
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 mt-1">
                Manage your service offerings, set pricing, and publish new trade packages to clients.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="/dashboard" class="px-4 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition flex items-center gap-2 shadow-xs">
                <i class="bi bi-speedometer2"></i>
                <span>Main Dashboard</span>
            </a>
            <button onclick="openAddServiceModal()" class="px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold transition flex items-center gap-2 shadow-md">
                <i class="bi bi-plus-lg"></i>
                <span>Add New Service</span>
            </button>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <span class="text-xs font-bold text-slate-400 block uppercase">Total Services</span>
            <span id="statTotalServices" class="text-2xl font-black text-slate-900 dark:text-white mt-1 block">0</span>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 block uppercase">Active Online</span>
            <span id="statActiveServices" class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1 block">0</span>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <span class="text-xs font-bold text-amber-600 dark:text-amber-400 block uppercase">Pending Approval</span>
            <span id="statPendingServices" class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1 block">0</span>
        </div>
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <span class="text-xs font-bold text-red-600 dark:text-red-400 block uppercase">Rejected</span>
            <span id="statRejectedServices" class="text-2xl font-black text-red-600 dark:text-red-400 mt-1 block">0</span>
        </div>
    </div>

    <!-- Services Grid Container -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-6 sm:p-8 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-black text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="bi bi-grid-fill text-amber-500"></i>
                    <span>Published Offerings</span>
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Services currently submitted to the Khedma marketplace</p>
            </div>
            <button onclick="openAddServiceModal()" class="px-3 py-1.5 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 font-bold text-xs border border-amber-200 dark:border-amber-800/60 hover:bg-amber-100 transition flex items-center gap-1.5">
                <i class="bi bi-plus-lg text-xs"></i> New Package
            </button>
        </div>

        <div id="servicesGridContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="col-span-full py-12 text-center text-slate-400">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-2 border-amber-500 border-t-transparent mb-2"></div>
                <p class="text-xs">Loading services...</p>
            </div>
        </div>
    </div>
</div>

<!-- ======================================================== -->
<!-- ADD SERVICE MODAL                                        -->
<!-- ======================================================== -->
<div id="addServiceModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-2xl bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center text-lg">
                    <i class="bi bi-plus-circle-fill"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Add New Service</h3>
                    <p class="text-xs text-slate-400">Publish a new trade package to clients</p>
                </div>
            </div>
            <button onclick="closeAddServiceModal()" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>

        <form id="addServiceForm" onsubmit="submitNewService(event)" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Service Title *</label>
                <input type="text" id="svcTitle" required placeholder="e.g. Emergency Pipe Leak Repair" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-medium focus:ring-2 focus:ring-amber-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Category / Trade *</label>
                    <select id="svcCategory" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-medium focus:ring-2 focus:ring-amber-500">
                        <option value="Plumbing">Plumbing</option>
                        <option value="Electricity">Electricity</option>
                        <option value="HVAC & Climate">HVAC & Climate</option>
                        <option value="Cleaning">Cleaning</option>
                        <option value="Painting">Painting</option>
                        <option value="Carpentry">Carpentry</option>
                        <option value="Appliances">Appliances</option>
                        <option value="IT & Network">IT & Network</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Price (TND) *</label>
                    <input type="number" step="0.5" min="1" id="svcPrice" required placeholder="50.00" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-medium focus:ring-2 focus:ring-amber-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Estimated Duration (Hours)</label>
                <input type="number" step="0.5" min="0.5" max="24" id="svcDuration" value="1.5" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-medium focus:ring-2 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Description & Scope</label>
                <textarea id="svcDescription" rows="3" placeholder="Describe what is included in this service (materials, labor, guarantee)..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-medium focus:ring-2 focus:ring-amber-500"></textarea>
            </div>

            <div class="p-3 bg-amber-50 dark:bg-amber-950/40 rounded-xl border border-amber-200/60 dark:border-amber-900/40 text-[11px] text-amber-800 dark:text-amber-300 flex items-start gap-2">
                <i class="bi bi-shield-check text-base shrink-0 mt-0.5"></i>
                <span>Note: New services undergo administrative moderation to verify safety and pricing before being visible publicly.</span>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeAddServiceModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
                    Cancel
                </button>
                <button type="submit" id="btnSubmitService" class="px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-md transition">
                    Publish Service
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ======================================================== -->
<!-- EDIT SERVICE MODAL                                       -->
<!-- ======================================================== -->
<div id="editServiceModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-5">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-slate-800">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-2xl bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center text-lg">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Edit Service & Rate</h3>
                    <p class="text-xs text-slate-400">Update offering details and pricing for clients</p>
                </div>
            </div>
            <button onclick="closeEditServiceModal()" class="p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>

        <form id="editServiceForm" onsubmit="submitEditService(event)" class="space-y-4">
            <input type="hidden" id="editSvcId">

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Service Title *</label>
                <input type="text" id="editSvcTitle" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-medium focus:ring-2 focus:ring-amber-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Category / Trade *</label>
                    <select id="editSvcCategory" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-medium focus:ring-2 focus:ring-amber-500">
                        <option value="Plumbing">Plumbing</option>
                        <option value="Electricity">Electricity</option>
                        <option value="HVAC & Climate">HVAC & Climate</option>
                        <option value="Cleaning">Cleaning</option>
                        <option value="Painting">Painting</option>
                        <option value="Carpentry">Carpentry</option>
                        <option value="Appliances">Appliances</option>
                        <option value="IT & Network">IT & Network</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Rate / Price (TND) *</label>
                    <input type="number" step="0.5" min="1" id="editSvcPrice" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-medium focus:ring-2 focus:ring-amber-500">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Estimated Duration (Hours)</label>
                    <input type="number" step="0.5" min="0.5" max="24" id="editSvcDuration" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-medium focus:ring-2 focus:ring-amber-500">
                </div>
                <div class="flex items-center pt-5">
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-700 dark:text-slate-300">
                        <input type="checkbox" id="editSvcIsActive" class="w-4 h-4 rounded text-amber-600 focus:ring-amber-500">
                        <span>Active (Publicly Visible)</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1">Description & Scope</label>
                <textarea id="editSvcDescription" rows="3" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-medium focus:ring-2 focus:ring-amber-500"></textarea>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="closeEditServiceModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
                    Cancel
                </button>
                <button type="submit" id="btnUpdateService" class="px-5 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-md transition">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let myServices = [];

    document.addEventListener('DOMContentLoaded', () => {
        loadProviderServices();
    });

    async function loadProviderServices() {
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
            myServices = data.services || [];

            // Stats
            document.getElementById('statTotalServices').textContent = myServices.length;
            document.getElementById('statActiveServices').textContent = myServices.filter(s => s.status === 'approved' && s.is_active).length;
            document.getElementById('statPendingServices').textContent = myServices.filter(s => s.status === 'pending').length;
            document.getElementById('statRejectedServices').textContent = myServices.filter(s => s.status === 'rejected').length;

            renderServicesGrid();
        } catch (err) {
            console.error('Error fetching provider services:', err);
            document.getElementById('servicesGridContainer').innerHTML = `
                <div class="col-span-full py-12 text-center text-red-500 text-xs">
                    Failed to load services. Please refresh the page.
                </div>
            `;
        }
    }

    function renderServicesGrid() {
        const container = document.getElementById('servicesGridContainer');

        if (myServices.length === 0) {
            container.innerHTML = `
                <div class="col-span-full py-12 text-center text-slate-400 space-y-3">
                    <i class="bi bi-tools text-4xl block text-slate-300 dark:text-slate-600"></i>
                    <p class="text-sm font-bold text-slate-700 dark:text-slate-300">You haven't listed any services yet</p>
                    <p class="text-xs text-slate-400 max-w-sm mx-auto">Click "Add New Service" above to publish your first offering.</p>
                    <button onclick="openAddServiceModal()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold transition shadow-sm">
                        <i class="bi bi-plus-lg"></i>
                        <span>Create Service</span>
                    </button>
                </div>
            `;
            return;
        }

        container.innerHTML = myServices.map(s => {
            let statusBadge = '';
            if (s.status === 'approved') {
                statusBadge = s.is_active 
                    ? '<span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300">Live & Active</span>'
                    : '<span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300">Paused</span>';
            } else if (s.status === 'pending') {
                statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300">Pending Review</span>';
            } else if (s.status === 'rejected') {
                statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-red-100 text-red-800 dark:bg-red-950/80 dark:text-red-300">Rejected</span>';
            }

            return `
                <div class="p-5 rounded-2xl bg-slate-50/80 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700 hover:border-amber-400 transition flex flex-col justify-between space-y-4">
                    <div class="space-y-3">
                        <div class="flex items-start justify-between gap-2">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300">
                                ${s.category}
                            </span>
                            <span class="text-base font-black text-slate-900 dark:text-white">
                                ${parseFloat(s.price).toFixed(2)} <span class="text-xs font-bold text-amber-600">TND</span>
                            </span>
                        </div>

                        <div>
                            <h3 class="font-extrabold text-slate-900 dark:text-white text-sm">
                                ${s.title}
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-3 leading-relaxed">
                                ${s.description || 'No description provided.'}
                            </p>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-200/60 dark:border-slate-700/60 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <span class="text-slate-400 flex items-center gap-1 text-[11px]">
                                <i class="bi bi-clock"></i>
                                <span>${s.duration_hours || 1} hr(s)</span>
                            </span>
                            <div>
                                ${statusBadge}
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <button onclick="openEditServiceModal(${s.id})" class="px-2.5 py-1.5 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-800 dark:text-amber-300 font-bold text-xs hover:bg-amber-100 dark:hover:bg-amber-900/40 transition flex items-center gap-1 border border-amber-200 dark:border-amber-800/60">
                                <i class="bi bi-pencil-square"></i>
                                <span>Edit Rate</span>
                            </button>
                            <button onclick="confirmDeleteService(${s.id}, '${s.title.replace(/'/g, "\\'")}')" class="px-2 py-1.5 rounded-lg text-red-600 hover:bg-red-50 dark:hover:bg-red-950/30 transition text-xs" title="Delete service">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    function openAddServiceModal() {
        document.getElementById('addServiceModal').classList.remove('hidden');
    }

    function closeAddServiceModal() {
        document.getElementById('addServiceModal').classList.add('hidden');
    }

    async function submitNewService(e) {
        e.preventDefault();
        const token = localStorage.getItem('khedma_token');
        if (!token) return;

        const btn = document.getElementById('btnSubmitService');
        btn.disabled = true;
        btn.textContent = 'Publishing...';

        const payload = {
            title: document.getElementById('svcTitle').value,
            category: document.getElementById('svcCategory').value,
            price: parseFloat(document.getElementById('svcPrice').value),
            duration_hours: parseFloat(document.getElementById('svcDuration').value),
            description: document.getElementById('svcDescription').value
        };

        try {
            const res = await fetch('/api/v1/provider/services', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            if (res.ok) {
                closeAddServiceModal();
                document.getElementById('addServiceForm').reset();
                await loadProviderServices();
            } else {
                const errData = await res.json();
                alert(errData.message || 'Failed to create service.');
            }
        } catch (err) {
            console.error(err);
            alert('Network error while publishing service.');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Publish Service';
        }
    }

    function openEditServiceModal(serviceId) {
        const s = myServices.find(item => item.id === serviceId);
        if (!s) return;

        document.getElementById('editSvcId').value = s.id;
        document.getElementById('editSvcTitle').value = s.title;
        document.getElementById('editSvcCategory').value = s.category;
        document.getElementById('editSvcPrice').value = parseFloat(s.price);
        document.getElementById('editSvcDuration').value = parseFloat(s.duration_hours || 1.0);
        document.getElementById('editSvcDescription').value = s.description || '';
        document.getElementById('editSvcIsActive').checked = !!s.is_active;

        document.getElementById('editServiceModal').classList.remove('hidden');
    }

    function closeEditServiceModal() {
        document.getElementById('editServiceModal').classList.add('hidden');
    }

    async function submitEditService(e) {
        e.preventDefault();
        const token = localStorage.getItem('khedma_token');
        if (!token) return;

        const id = document.getElementById('editSvcId').value;
        const btn = document.getElementById('btnUpdateService');
        btn.disabled = true;
        btn.textContent = 'Saving...';

        const payload = {
            title: document.getElementById('editSvcTitle').value,
            category: document.getElementById('editSvcCategory').value,
            price: parseFloat(document.getElementById('editSvcPrice').value),
            duration_hours: parseFloat(document.getElementById('editSvcDuration').value),
            description: document.getElementById('editSvcDescription').value,
            is_active: document.getElementById('editSvcIsActive').checked
        };

        try {
            const res = await fetch(`/api/v1/provider/services/${id}`, {
                method: 'PUT',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const data = await res.json();
            if (res.ok) {
                closeEditServiceModal();
                await loadProviderServices();
            } else {
                alert(data.message || 'Failed to update service.');
            }
        } catch (err) {
            console.error(err);
            alert('Network error while saving changes.');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Save Changes';
        }
    }

    async function confirmDeleteService(serviceId, title) {
        if (!confirm(`Are you sure you want to delete the service "${title}"?`)) return;

        const token = localStorage.getItem('khedma_token');
        if (!token) return;

        try {
            const res = await fetch(`/api/v1/provider/services/${serviceId}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            const data = await res.json();
            if (res.ok) {
                await loadProviderServices();
            } else {
                alert(data.message || 'Could not delete service.');
            }
        } catch(err) {
            console.error(err);
            alert('Network error while deleting service.');
        }
    }
</script>
@endsection
