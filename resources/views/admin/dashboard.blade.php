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
        <!-- 1. Admin Command Hero Banner (Spotlight Style) -->
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

        <!-- 3. Navigation Tabs: Users Management | Platform Bookings | Services Catalog -->
        <div class="flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-2 flex-wrap">
            <button onclick="switchAdminTab('users')" id="tabUsersBtn" class="px-4 py-2 rounded-xl text-xs font-extrabold bg-slate-900 dark:bg-slate-800 text-white shadow-sm transition flex items-center gap-2">
                <i class="bi bi-people-fill text-blue-400"></i>
                <span>Users Management</span>
            </button>
            <button onclick="switchAdminTab('bookings')" id="tabBookingsBtn" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition flex items-center gap-2">
                <i class="bi bi-calendar2-check-fill text-amber-400"></i>
                <span>Platform Bookings</span>
            </button>
            <button onclick="switchAdminTab('catalog')" id="tabCatalogBtn" class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition flex items-center gap-2">
                <i class="bi bi-grid-fill text-emerald-400"></i>
                <span>Services Catalog</span>
                <span id="pendingServicesBadge" class="hidden px-1.5 py-0.5 rounded-full bg-amber-500 text-white text-[9px] font-black">0</span>
            </button>
        </div>

        <!-- 4. Tab 1: Users Management Table -->
        <div id="sectionUsers" class="bg-white dark:bg-slate-950 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden transition-colors duration-200">
            <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-lg font-extrabold text-slate-900 dark:text-white tracking-tight">Platform Users & Access Control</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Manage user roles, inspect profiles, and suspend/activate accounts.</p>
                </div>
                <!-- Role filter -->
                <div class="flex items-center gap-1.5 p-1 bg-slate-100 dark:bg-slate-900 rounded-xl text-xs font-bold border border-transparent dark:border-slate-800">
                    <button onclick="filterUsers('all')" id="userFilterAll" class="px-3 py-1 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-xs">All</button>
                    <button onclick="filterUsers('client')" id="userFilterClient" class="px-3 py-1 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">Clients</button>
                    <button onclick="filterUsers('provider')" id="userFilterProvider" class="px-3 py-1 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">Providers</button>
                    <button onclick="filterUsers('admin')" id="userFilterAdmin" class="px-3 py-1 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white">Admins</button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                    <thead class="bg-slate-50 dark:bg-slate-900/60 text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800">
                        <tr>
                            <th class="py-3.5 px-6">User</th>
                            <th class="py-3.5 px-6">Role</th>
                            <th class="py-3.5 px-6">Status</th>
                            <th class="py-3.5 px-6">City / Details</th>
                            <th class="py-3.5 px-6">Joined Date</th>
                            <th class="py-3.5 px-6 text-right">Moderation Actions</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody" class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        <!-- Populated dynamically -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 5. Tab 2: Platform Bookings Table -->
        <div id="sectionBookings" class="hidden bg-white dark:bg-slate-950 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden transition-colors duration-200">
            <div class="p-6 border-b border-slate-100 dark:border-slate-800">
                <h2 class="text-lg font-extrabold text-slate-900 dark:text-white tracking-tight">Platform-Wide Service Bookings</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">All service agreements between clients and providers.</p>
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
                    <tbody id="adminBookingsTableBody" class="divide-y divide-slate-100 dark:divide-slate-800/80">
                        <!-- Populated dynamically -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- 6. Tab 3: Services Catalog (Categories + Moderation) -->
        <div id="sectionCatalog" class="hidden space-y-6">

            <!-- 6a. Service Categories Management -->
            <div class="bg-white dark:bg-slate-950 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-extrabold text-slate-900 dark:text-white tracking-tight">Service Categories</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Create, edit, and remove the canonical list of service categories.</p>
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

            <!-- 6b. Service Listings Moderation -->
            <div class="bg-white dark:bg-slate-950 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-extrabold text-slate-900 dark:text-white tracking-tight">Service Listings Moderation</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Review, approve, reject, or remove provider service listings.</p>
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
    let allUsers = [];
    let allBookings = [];
    let currentUserRoleFilter = 'all';
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

            // Update dedicated admin sidebar items
            if (document.getElementById('sidebarUserCountBadge')) {
                document.getElementById('sidebarUserCountBadge').textContent = metrics.total_users;
            }
            if (document.getElementById('sidebarBookingsCountBadge')) {
                document.getElementById('sidebarBookingsCountBadge').textContent = metrics.total_bookings;
            }
            if (document.getElementById('sidebarGMVDisplay')) {
                document.getElementById('sidebarGMVDisplay').textContent = metrics.platform_gmv.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
            }
            if (document.getElementById('adminSidebarName') && adminUser) {
                document.getElementById('adminSidebarName').textContent = adminUser.name;
                document.getElementById('adminSidebarEmail').textContent = adminUser.email;
                document.getElementById('adminSidebarAvatar').textContent = adminUser.name.charAt(0).toUpperCase();
            }

            renderUsersTable(allUsers);
            renderAdminBookingsTable(allBookings);

            document.getElementById('loadingState').classList.add('hidden');
            document.getElementById('adminContent').classList.remove('hidden');
        } catch(e) {
            console.error(e);
        }
    }

    window.loadAdminDashboard = loadAdminDashboard;
    window.switchAdminTab = switchAdminTab;
    window.filterUsers = filterUsers;
    window.toggleUserStatus = toggleUserStatus;
    // Catalog globals (called via onclick attributes)
    window.loadServicesPage = loadServicesPage;
    window.filterServices = filterServices;
    window.approveService = approveService;
    window.openRejectModal = openRejectModal;
    window.closeRejectModal = closeRejectModal;
    window.submitRejection = submitRejection;
    window.toggleServiceActive = toggleServiceActive;
    window.deleteService = deleteService;
    window.openCategoryModal = openCategoryModal;
    window.closeCategoryModal = closeCategoryModal;
    window.submitCategory = submitCategory;
    window.deleteCategory = deleteCategory;

    function switchAdminTab(tab) {
        const tabs = ['users', 'bookings', 'catalog'];
        const sections = { users: 'sectionUsers', bookings: 'sectionBookings', catalog: 'sectionCatalog' };
        const buttons  = { users: 'tabUsersBtn',  bookings: 'tabBookingsBtn',  catalog: 'tabCatalogBtn' };
        const activeClass   = 'px-4 py-2 rounded-xl text-xs font-extrabold bg-slate-900 dark:bg-slate-800 text-white shadow-sm transition flex items-center gap-2';
        const inactiveClass = 'px-4 py-2 rounded-xl text-xs font-bold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition flex items-center gap-2';

        tabs.forEach(t => {
            document.getElementById(sections[t])?.classList.toggle('hidden', t !== tab);
            document.getElementById(buttons[t])?.setAttribute('class', t === tab ? activeClass : inactiveClass);
        });

        if (tab === 'catalog') {
            loadCategories();
            loadServicesPage(1);
        }
    }

    function renderUsersTable(users) {
        const tbody = document.getElementById('usersTableBody');
        tbody.innerHTML = '';

        const filtered = currentUserRoleFilter === 'all' ? users : users.filter(u => u.role === currentUserRoleFilter);

        filtered.forEach(u => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-50/70 dark:hover:bg-slate-900/40 transition';

            let roleBadge = 'bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-300';
            if (u.role === 'provider') roleBadge = 'bg-amber-100 dark:bg-amber-900/40 text-amber-800 dark:text-amber-300';
            if (u.role === 'admin') roleBadge = 'bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-300';

            const statusBadge = u.status === 'active' 
                ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300' 
                : 'bg-red-100 dark:bg-red-900/40 text-red-800 dark:text-red-300';

            const city = u.client_profile?.city || u.provider_profile?.city || 'Tunis';

            const adminUser = JSON.parse(localStorage.getItem('khedma_user'));
            const isSelf = adminUser && adminUser.id === u.id;

            let actionHtml = '';
            if (isSelf) {
                actionHtml = '<span class="text-xs text-slate-400 dark:text-slate-500 font-semibold italic">Current Admin</span>';
            } else {
                const nextStatus = u.status === 'active' ? 'suspended' : 'active';
                const nextLabel = u.status === 'active' ? 'Suspend' : 'Activate';
                const nextClass = u.status === 'active' ? 'text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/30 border-red-200 dark:border-red-900/40' : 'text-emerald-700 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/30 border-emerald-200 dark:border-emerald-900/40';
                actionHtml = `
                    <button onclick="toggleUserStatus(${u.id}, '${nextStatus}')" class="px-3 py-1 rounded-lg border text-xs font-bold transition ${nextClass}">
                        ${nextLabel}
                    </button>
                `;
            }

            tr.innerHTML = `
                <td class="py-4 px-6">
                    <div class="font-extrabold text-xs text-slate-900 dark:text-white">${u.name}</div>
                    <div class="text-[11px] text-slate-400 dark:text-slate-500">${u.email}</div>
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
                <td class="py-4 px-6 text-xs text-slate-700 dark:text-slate-300">
                    ${city}
                </td>
                <td class="py-4 px-6 text-xs text-slate-400 dark:text-slate-500">
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
                    btn.className = 'px-3 py-1 rounded-lg bg-white dark:bg-slate-800 text-slate-900 dark:text-white shadow-xs font-bold';
                } else {
                    btn.className = 'px-3 py-1 rounded-lg text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white';
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

    function renderAdminBookingsTable(bookings) {        const tbody = document.getElementById('adminBookingsTableBody');
        tbody.innerHTML = '';

        bookings.forEach(b => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-50/70 dark:hover:bg-slate-900/40 transition';

            tr.innerHTML = `
                <td class="py-4 px-6 font-mono font-bold text-xs text-slate-900 dark:text-white">${b.booking_number}</td>
                <td class="py-4 px-6 text-xs font-bold text-slate-900 dark:text-white">${b.client?.name || 'Client'}</td>
                <td class="py-4 px-6 text-xs font-bold text-amber-800 dark:text-amber-400">${b.provider?.name || 'Provider'}</td>
                <td class="py-4 px-6 text-xs text-slate-700 dark:text-slate-300">${b.service_title}</td>
                <td class="py-4 px-6 font-bold text-xs text-slate-900 dark:text-white">${parseFloat(b.total_price).toFixed(0)} TND</td>
                <td class="py-4 px-6">
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300">
                        ${b.status}
                    </span>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    // =========================================================================
    // CATALOG — Categories
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
    // CATALOG — Service Listings Moderation
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

            // Update pending badge
            if (currentServicesFilter === 'all' || currentServicesFilter === 'pending') {
                const pending = (json.data || []).filter(s => s.status === 'pending').length;
                const badge   = document.getElementById('pendingServicesBadge');
                if (pending > 0) {
                    badge.textContent = pending;
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
</script>
@endsection
