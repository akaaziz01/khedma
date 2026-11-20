@extends('layouts.app')

@section('title', 'Dashboard | Khedma')

@section('content')
<div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-8">
    <!-- Loading skeleton -->
    <div id="loadingState" class="py-24 text-center">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-amber-600 border-t-transparent mb-4"></div>
        <p class="text-sm text-slate-500 font-semibold">Loading real database data...</p>
    </div>

    <!-- Main Dashboard Container -->
    <div id="dashboardContent" class="hidden space-y-8">

        <!-- ========================================== -->
        <!-- PROVIDER DASHBOARD (Spotlight Inspired)    -->
        <!-- ========================================== -->
        <div id="providerDashboardView" class="space-y-8 hidden">
            <!-- 1. Hero Banner (Spotlight Gradient Style) -->
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-amber-600 via-orange-600 to-amber-800 text-white p-8 md:p-10 shadow-xl shadow-amber-600/20">
                <div class="absolute top-0 right-0 -mt-10 -mr-10 w-72 h-72 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute bottom-0 left-10 -mb-10 w-56 h-56 bg-orange-400/20 rounded-full blur-2xl pointer-events-none"></div>

                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-start sm:items-center gap-5">
                        <div id="providerAvatar" class="w-20 h-20 rounded-2xl bg-white/20 backdrop-blur-md border border-white/30 text-white font-extrabold text-3xl flex items-center justify-center shadow-lg shrink-0">
                            Y
                        </div>
                        <div>
                            <div class="flex items-center gap-2.5 flex-wrap">
                                <h1 id="providerNameDisplay" class="text-2xl sm:text-3xl font-extrabold tracking-tight">Youssef El Amrani</h1>
                                <span id="providerCategoryTag" class="text-xs font-extrabold uppercase tracking-wider px-3 py-1 rounded-full bg-white/20 backdrop-blur-md border border-white/30">
                                    Plumbing
                                </span>
                            </div>
                            <p id="providerBusinessSub" class="text-sm text-amber-100 font-medium mt-1">Amrani Plomberie & Chauffage Grand Tunis</p>
                            <div class="mt-2 flex items-center gap-4 text-xs text-amber-100/90 flex-wrap">
                                <span class="flex items-center gap-1.5" id="providerLocationDisplay">
                                    <i class="bi bi-geo-alt-fill text-amber-200"></i>
                                    Tunis
                                </span>
                                <span class="flex items-center gap-1.5" id="providerPhoneDisplay">
                                    <i class="bi bi-telephone-fill text-amber-200"></i>
                                    +216 98 998 877
                                </span>
                                <span class="flex items-center gap-1.5">
                                    <i class="bi bi-cash-stack text-amber-200"></i>
                                    <strong id="providerRateDisplayBanner">35</strong> TND / hr
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Quick Actions & Availability Switch -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                        <!-- Real-time Availability Toggle -->
                        <button type="button" onclick="toggleOnlineStatus()" id="availabilityBtn"
                            class="px-4 py-2.5 rounded-xl bg-white/20 hover:bg-white/30 backdrop-blur-md border border-white/30 text-xs font-bold transition flex items-center gap-2 shadow-sm">
                            <span id="availabilityDot" class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span id="availabilityText">Available for Jobs</span>
                        </button>

                        <a href="/profile"
                            class="px-4 py-2.5 rounded-xl bg-white/20 hover:bg-white/30 backdrop-blur-md border border-white/30 text-xs font-bold transition flex items-center gap-1.5 shadow-sm text-white">
                            <i class="bi bi-person-circle text-sm"></i>
                            <span>My Profile</span>
                        </a>

                        <button onclick="openAddServiceModal()"
                            class="px-4 py-2.5 rounded-xl bg-white text-amber-800 hover:bg-amber-50 text-xs font-extrabold transition flex items-center gap-1.5 shadow-md">
                            <i class="bi bi-plus-lg text-sm"></i>
                            Add Service
                        </button>

                        <button onclick="handleLogout()"
                            class="px-3.5 py-2.5 rounded-xl bg-black/20 hover:bg-black/30 text-xs font-bold transition text-amber-100 hover:text-white border border-white/20 flex items-center gap-1.5">
                            <i class="bi bi-box-arrow-right text-sm"></i>
                            Logout
                        </button>
                    </div>
                </div>
            </div>

            <!-- 2. Spotlight 4 Stat Metric Cards (Direct from MySQL) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- Total Revenue Card -->
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Total Revenue</span>
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold">
                            <i class="bi bi-cash-coin text-lg"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-baseline gap-2">
                        <span id="statRevenue" class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">0</span>
                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400">TND</span>
                    </div>
                    <p class="text-xs text-emerald-600 dark:text-emerald-400 font-semibold mt-2 flex items-center gap-1">
                        <i class="bi bi-arrow-up-right"></i> From completed client jobs
                    </p>
                </div>

                <!-- Active Jobs Card -->
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Active Jobs</span>
                        <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                            <i class="bi bi-hourglass-split text-lg"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span id="statActiveJobs" class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">0</span>
                    </div>
                    <p class="text-xs text-blue-600 dark:text-blue-400 font-semibold mt-2">Accepted & in-progress</p>
                </div>

                <!-- Pending Leads Card -->
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">New Leads</span>
                        <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 flex items-center justify-center">
                            <i class="bi bi-bell-fill text-lg"></i>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span id="statPendingLeads" class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">0</span>
                    </div>
                    <p class="text-xs text-amber-600 dark:text-amber-400 font-semibold mt-2">Awaiting your acceptance</p>
                </div>

                <!-- Overall Rating Card -->
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Client Rating</span>
                        <div class="w-10 h-10 rounded-xl bg-yellow-100 dark:bg-yellow-950/60 text-yellow-600 dark:text-yellow-400 flex items-center justify-center font-bold">
                            <i class="bi bi-star-fill text-base"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-baseline gap-2">
                        <span id="statRating" class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">5.0</span>
                        <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">/ 5.0</span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 font-medium" id="statReviewCount">Based on real client reviews</p>
                </div>
            </div>

            <!-- 3. Interactive Bookings / Service Requests Table (Real Database Records) -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden transition">
                <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                            <i class="bi bi-journal-check text-amber-600 dark:text-amber-400"></i>
                            <span>Client Bookings & Job Inquiries</span>
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Live requests from MySQL database `bookings` table.</p>
                    </div>

                    <!-- Status Filters (Spotlight Style) -->
                    <div class="flex items-center gap-1.5 p-1 bg-slate-100 dark:bg-slate-800 rounded-xl text-xs font-bold flex-wrap border border-transparent dark:border-slate-700/60">
                        <button onclick="filterBookings('all')" id="filterAll" class="px-3 py-1.5 rounded-lg bg-white dark:bg-slate-700 shadow-sm text-slate-900 dark:text-white">All</button>
                        <button onclick="filterBookings('pending')" id="filterPending" class="px-3 py-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white">Pending</button>
                        <button onclick="filterBookings('accepted')" id="filterAccepted" class="px-3 py-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white">Accepted</button>
                        <button onclick="filterBookings('in_progress')" id="filterInProgress" class="px-3 py-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white">In Progress</button>
                        <button onclick="filterBookings('completed')" id="filterCompleted" class="px-3 py-1.5 rounded-lg text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white">Completed</button>
                    </div>
                </div>

                <!-- Bookings Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600 dark:text-slate-300">
                        <thead class="bg-slate-50 dark:bg-slate-800/60 text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800">
                            <tr>
                                <th class="py-3.5 px-6">Booking #</th>
                                <th class="py-3.5 px-6">Client</th>
                                <th class="py-3.5 px-6">Service Requested</th>
                                <th class="py-3.5 px-6">Date & Address</th>
                                <th class="py-3.5 px-6">Amount</th>
                                <th class="py-3.5 px-6">Status</th>
                                <th class="py-3.5 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="bookingsTableBody" class="divide-y divide-slate-100 dark:divide-slate-800">
                            <!-- Populated with real database rows -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 4. Two Columns: Services Catalog & Recent Client Reviews -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Services Catalog (2 cols) -->
                <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-6 sm:p-8 space-y-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                                <i class="bi bi-tools text-amber-600 dark:text-amber-400"></i>
                                <span>My Service Offerings</span>
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Real service records currently live in MySQL database.</p>
                        </div>
                        <button onclick="openAddServiceModal()" class="px-3 py-1.5 rounded-lg bg-amber-50 dark:bg-amber-950/40 hover:bg-amber-100 dark:hover:bg-amber-900/60 text-amber-800 dark:text-amber-300 font-bold text-xs border border-amber-200 dark:border-amber-800/60 transition flex items-center gap-1.5">
                            <i class="bi bi-plus-lg text-xs"></i> Add Service
                        </button>
                    </div>

                    <div id="servicesGrid" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Populated dynamically from MySQL -->
                    </div>
                </div>

                <!-- Recent Client Reviews (1 col) -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-6 sm:p-8 space-y-6">
                    <div>
                        <h3 class="text-lg font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                            <i class="bi bi-chat-square-quote-fill text-amber-500"></i>
                            <span>Verified Client Reviews</span>
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Feedback linked to completed bookings.</p>
                    </div>

                    <div id="reviewsList" class="space-y-4">
                        <!-- Populated dynamically from MySQL -->
                    </div>
                </div>
            </div>

            <!-- 5. Provider Profile & Bio Card -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-6 sm:p-8 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="bi bi-person-badge-fill text-amber-600 dark:text-amber-400"></i>
                        <span>Professional Bio & Specializations</span>
                    </h3>
                </div>
                <p id="providerBioText" class="text-sm text-slate-700 dark:text-slate-300 bg-slate-50 dark:bg-slate-800/60 p-4 rounded-2xl border border-slate-100 dark:border-slate-800 leading-relaxed italic">
                    Loading bio...
                </p>
                <div>
                    <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-2">Registered Skills & Certifications</span>
                    <div id="providerSkillsContainer" class="flex flex-wrap gap-2"></div>
                </div>
            </div>

        </div>

        <!-- ========================================== -->
        <!-- CLIENT DASHBOARD VIEW                      -->
        <!-- ========================================== -->
        <div id="clientDashboardView" class="space-y-8 hidden">
            <!-- Client Top Banner -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-3xl p-8 shadow-xl shadow-blue-600/20 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div id="clientAvatar" class="w-16 h-16 rounded-2xl bg-white/20 border border-white/30 text-white font-extrabold text-2xl flex items-center justify-center shadow">
                        F
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 id="clientNameDisplay" class="text-2xl font-extrabold">Mariem Ben Salem</h1>
                            <span class="text-xs font-bold uppercase tracking-wider px-2.5 py-0.5 rounded-full bg-white/20">Client</span>
                        </div>
                        <p id="clientEmailDisplay" class="text-xs text-blue-100 mt-1">client@khedma.tn</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="/profile" class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white text-xs font-bold rounded-xl border border-white/30 transition flex items-center gap-1.5">
                        <i class="bi bi-person-circle text-xs"></i>
                        <span>My Profile</span>
                    </a>
                    <button onclick="handleLogout()" class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white text-xs font-bold rounded-xl border border-white/30 transition flex items-center gap-1.5">
                        <i class="bi bi-box-arrow-right text-xs"></i>
                        Logout
                    </button>
                </div>
            </div>

            <!-- Client Info Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
                    <span class="text-xs font-bold uppercase text-slate-400 dark:text-slate-500 block mb-1">My Address</span>
                    <p id="clientFullAddress" class="text-sm font-semibold text-slate-800 dark:text-slate-200 mt-2">Tunis</p>
                </div>
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm">
                    <span class="text-xs font-bold uppercase text-slate-400 dark:text-slate-500 block mb-1">Current Active Bookings</span>
                    <p class="text-3xl font-black text-slate-900 dark:text-white mt-2">2</p>
                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-1 font-semibold">1 urgent leak repair in progress</p>
                </div>
                <div class="bg-white dark:bg-slate-900 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex flex-col justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase text-slate-400 dark:text-slate-500 block mb-1">Quick Action</span>
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">Find & Hire a Pro</p>
                    </div>
                    <a href="/services" class="mt-4 text-center py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg transition">
                        Explore Verified Pros
                    </a>
                </div>
            </div>

            <!-- Client Saved & Favorite Providers Section -->
            <div id="favorites" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm p-6 sm:p-8 space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 dark:border-slate-800 pb-5">
                    <div>
                        <div class="flex items-center gap-2.5">
                            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-2">
                                <i class="bi bi-heart-fill text-rose-500"></i>
                                <span>My Saved & Favorite Providers</span>
                            </h2>
                            <span id="clientFavoritesCountBadge" class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-900/40">
                                0 Saved
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Quickly access and book your trusted specialists.</p>
                    </div>

                    <!-- Search Input for Saved Pros -->
                    <div class="relative w-full sm:w-64">
                        <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                        <input type="text" id="favSearchInput" oninput="filterClientFavorites(this.value)" placeholder="Search saved pros..."
                            class="w-full pl-9 pr-3.5 py-2 rounded-xl text-xs border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-rose-500 transition">
                    </div>
                </div>

                <!-- Loading spinner for favorites -->
                <div id="favoritesLoadingState" class="py-12 text-center">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-3 border-rose-500 border-t-transparent mb-2"></div>
                    <p class="text-xs text-slate-400 font-semibold">Loading your saved providers...</p>
                </div>

                <!-- Empty State -->
                <div id="clientFavoritesEmpty" class="hidden py-12 text-center max-w-sm mx-auto space-y-3">
                    <div class="w-14 h-14 rounded-2xl bg-rose-50 dark:bg-rose-950/40 text-rose-500 flex items-center justify-center mx-auto text-2xl shadow-inner">
                        <i class="bi bi-bookmark-heart"></i>
                    </div>
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">No Saved Providers Yet</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        When you find skilled professionals you like, click the heart icon on their profile to bookmark them here.
                    </p>
                    <div class="pt-2">
                        <a href="/services" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold transition shadow-sm">
                            <i class="bi bi-search"></i>
                            <span>Browse Verified Pros</span>
                        </a>
                    </div>
                </div>

                <!-- Favorites Grid -->
                <div id="clientFavoritesGrid" class="hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <!-- Populated dynamically via JS -->
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ========================================== -->
<!-- MODAL: ADD NEW SERVICE                     -->
<!-- ========================================== -->
<div id="addServiceModal" class="hidden fixed inset-0 z-50 bg-slate-900/60 dark:bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 max-w-lg w-full p-6 sm:p-8 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <h3 class="text-lg font-extrabold text-slate-900 dark:text-white">Add New Service Offering</h3>
            <button onclick="closeAddServiceModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-base font-bold">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form onsubmit="handleCreateService(event)" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Service Title *</label>
                <input type="text" id="newServiceTitle" required placeholder="e.g. Radiator Heating Inspection"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Category *</label>
                    <select id="newServiceCategory" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 bg-white">
                        <option value="Plumbing">Plumbing</option>
                        <option value="Electricity">Electricity</option>
                        <option value="HVAC & AC">HVAC & AC</option>
                        <option value="Carpentry">Carpentry</option>
                        <option value="Painting">Painting</option>
                        <option value="Cleaning">Cleaning</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Price (TND) *</label>
                    <input type="number" id="newServicePrice" required min="10" step="5" placeholder="50"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Estimated Duration (Hours)</label>
                <input type="number" id="newServiceDuration" step="0.5" min="0.5" value="1.5"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Service Description</label>
                <textarea id="newServiceDesc" rows="3" placeholder="Explain what is included in this service..."
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500"></textarea>
            </div>

            <div class="pt-2 flex items-center justify-end gap-3">
                <button type="button" onclick="closeAddServiceModal()" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                    Cancel
                </button>
                <button type="submit" id="saveServiceBtn" class="px-5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-extrabold rounded-xl shadow-md transition">
                    Save to Database
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let globalBookings = [];
    let currentFilter = 'all';

    document.addEventListener('DOMContentLoaded', async () => {
        const token = localStorage.getItem('khedma_token');
        if (!token) {
            window.location.href = '/login';
            return;
        }

        try {
            // Check user identity first
            const userRes = await fetch('/api/v1/auth/user', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            if (!userRes.ok) {
                localStorage.removeItem('khedma_token');
                window.location.href = '/login';
                return;
            }

            const userData = await userRes.json();
            const user = userData.user;

            if (user.role === 'provider') {
                await loadProviderDashboard(token);
            } else {
                renderClientDashboard(user);
            }
        } catch (err) {
            console.error('Error initializing dashboard:', err);
        } finally {
            document.getElementById('loadingState').classList.add('hidden');
            document.getElementById('dashboardContent').classList.remove('hidden');
        }
    });

    // ── Load Real Provider Data from MySQL ─────────────────────────────────────
    async function loadProviderDashboard(token) {
        document.getElementById('providerDashboardView').classList.remove('hidden');
        document.getElementById('clientDashboardView').classList.add('hidden');

        try {
            const res = await fetch('/api/v1/provider/dashboard', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            if (!res.ok) throw new Error('Failed to fetch provider dashboard data');

            const data = await res.json();
            const provider = data.provider;
            const metrics = data.metrics;
            globalBookings = data.bookings || [];

            // 1. Populate Hero Banner
            document.getElementById('providerNameDisplay').textContent = provider.name;
            document.getElementById('providerAvatar').textContent = provider.name.charAt(0).toUpperCase();
            document.getElementById('providerBusinessSub').textContent = provider.profile?.business_name || 'Independent Specialist';
            document.getElementById('providerCategoryTag').textContent = provider.profile?.category || 'Provider';
            document.getElementById('providerLocationDisplay').innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                ${provider.profile?.city || 'Tunis'}
            `;
            document.getElementById('providerPhoneDisplay').innerHTML = `
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                ${provider.phone || '+216 20 000 000'}
            `;
            document.getElementById('providerRateDisplayBanner').textContent = metrics.hourly_rate ? metrics.hourly_rate.toFixed(0) : '—';

            // Availability toggle display
            updateAvailabilityDisplay(metrics.is_available);

            // 2. Metrics Cards
            document.getElementById('statRevenue').textContent = metrics.total_revenue.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
            document.getElementById('statActiveJobs').textContent = metrics.active_jobs;
            document.getElementById('statPendingLeads').textContent = metrics.pending_leads;
            document.getElementById('statRating').textContent = metrics.average_rating.toFixed(1);
            document.getElementById('statReviewCount').textContent = `Based on ${metrics.total_reviews} verified reviews`;

            // 3. Render Bookings Table
            renderBookingsTable(globalBookings);

            // 4. Render Services Catalog
            renderServices(data.services || []);

            // 5. Render Reviews
            renderReviews(data.reviews || []);

            // 6. Bio & Skills
            document.getElementById('providerBioText').textContent = provider.profile?.bio ? `"${provider.profile.bio}"` : 'No professional bio added yet.';
            const skillsContainer = document.getElementById('providerSkillsContainer');
            skillsContainer.innerHTML = '';
            const skills = Array.isArray(provider.profile?.skills) ? provider.profile.skills : [];
            if (skills.length > 0) {
                skills.forEach(s => {
                    const tag = document.createElement('span');
                    tag.className = 'px-3 py-1 rounded-xl bg-amber-50 text-amber-800 border border-amber-200 text-xs font-semibold shadow-xs';
                    tag.textContent = s;
                    skillsContainer.appendChild(tag);
                });
            } else {
                skillsContainer.innerHTML = '<span class="text-xs text-slate-400">No skills specified.</span>';
            }

        } catch (err) {
            console.error('Error fetching provider data:', err);
        }
    }

    // ── Toggle Availability in Database ────────────────────────────────────────
    async function toggleOnlineStatus() {
        const token = localStorage.getItem('khedma_token');
        try {
            const res = await fetch('/api/v1/provider/availability', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            const data = await res.json();
            if (res.ok) {
                updateAvailabilityDisplay(data.is_available);
            }
        } catch(e) {
            console.error(e);
        }
    }

    function updateAvailabilityDisplay(isAvailable) {
        const dot = document.getElementById('availabilityDot');
        const text = document.getElementById('availabilityText');
        if (isAvailable) {
            dot.className = 'w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse';
            text.textContent = 'Available for Jobs';
        } else {
            dot.className = 'w-2.5 h-2.5 rounded-full bg-slate-400';
            text.textContent = 'Offline';
        }
    }

    // ── Render Bookings Table ──────────────────────────────────────────────────
    function renderBookingsTable(bookings) {
        const tbody = document.getElementById('bookingsTableBody');
        tbody.innerHTML = '';

        const filtered = currentFilter === 'all' ? bookings : bookings.filter(b => b.status === currentFilter);

        if (filtered.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="py-8 text-center text-xs text-slate-400">
                        No bookings found matching "${currentFilter}".
                    </td>
                </tr>
            `;
            return;
        }

        filtered.forEach(b => {
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-slate-50/70 transition';

            // Status badge classes
            let badgeClass = 'bg-slate-100 text-slate-700';
            if (b.status === 'pending') badgeClass = 'bg-amber-100 text-amber-800 font-bold';
            if (b.status === 'accepted') badgeClass = 'bg-blue-100 text-blue-800 font-bold';
            if (b.status === 'in_progress') badgeClass = 'bg-purple-100 text-purple-800 font-bold animate-pulse';
            if (b.status === 'completed') badgeClass = 'bg-emerald-100 text-emerald-800 font-bold';
            if (b.status === 'declined') badgeClass = 'bg-red-100 text-red-800 font-bold';

            // Action buttons based on status
            let actionsHtml = '';
            if (b.status === 'pending') {
                actionsHtml = `
                    <div class="flex items-center justify-end gap-1.5">
                        <button onclick="updateStatus(${b.id}, 'accepted')" class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition">Accept</button>
                        <button onclick="updateStatus(${b.id}, 'declined')" class="px-2 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs transition">Decline</button>
                    </div>
                `;
            } else if (b.status === 'accepted') {
                actionsHtml = `
                    <button onclick="updateStatus(${b.id}, 'in_progress')" class="px-2.5 py-1 rounded-lg bg-purple-600 hover:bg-purple-700 text-white font-bold text-xs transition">Start Job</button>
                `;
            } else if (b.status === 'in_progress') {
                actionsHtml = `
                    <button onclick="updateStatus(${b.id}, 'completed')" class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition">✓ Mark Done</button>
                `;
            } else if (b.status === 'completed') {
                actionsHtml = `
                    <span class="text-xs text-emerald-600 font-bold flex items-center justify-end gap-1">
                        <span>✓</span> Paid
                    </span>
                `;
            } else {
                actionsHtml = `<span class="text-xs text-slate-400">—</span>`;
            }

            const formattedDate = new Date(b.scheduled_at).toLocaleDateString('en-GB', {
                day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit'
            });

            tr.innerHTML = `
                <td class="py-4 px-6 font-mono font-bold text-xs text-slate-900">${b.booking_number}</td>
                <td class="py-4 px-6">
                    <div class="font-bold text-slate-900 text-xs">${b.client?.name || 'Client'}</div>
                    <div class="text-[11px] text-slate-400">${b.client?.phone || 'No phone'}</div>
                </td>
                <td class="py-4 px-6">
                    <div class="font-semibold text-slate-900 text-xs">${b.service_title}</div>
                    <div class="text-[11px] text-slate-500">${b.category}</div>
                    ${b.client_notes ? `<div class="text-[10px] text-amber-700 italic mt-0.5 line-clamp-1">"${b.client_notes}"</div>` : ''}
                </td>
                <td class="py-4 px-6">
                    <div class="text-xs text-slate-800 font-medium">${formattedDate}</div>
                    <div class="text-[11px] text-slate-400">${b.address}, ${b.city}</div>
                </td>
                <td class="py-4 px-6 font-bold text-xs text-slate-900">${parseFloat(b.total_price).toFixed(0)} TND</td>
                <td class="py-4 px-6">
                    <span class="inline-block px-2.5 py-1 rounded-full text-[10px] uppercase tracking-wider ${badgeClass}">
                        ${b.status.replace('_', ' ')}
                    </span>
                </td>
                <td class="py-4 px-6 text-right">${actionsHtml}</td>
            `;
            tbody.appendChild(tr);
        });
    }

    // ── Filter Bookings ────────────────────────────────────────────────────────
    function filterBookings(status) {
        currentFilter = status;
        const buttons = ['all', 'pending', 'accepted', 'in_progress', 'completed'];
        buttons.forEach(b => {
            const btn = document.getElementById('filter' + b.split('_').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(''));
            if (btn) {
                if (b === status) {
                    btn.className = 'px-3 py-1.5 rounded-lg bg-white shadow-sm text-slate-900 font-bold';
                } else {
                    btn.className = 'px-3 py-1.5 rounded-lg text-slate-600 hover:text-slate-900';
                }
            }
        });
        renderBookingsTable(globalBookings);
    }

    // ── Update Booking Status in Database ──────────────────────────────────────
    async function updateStatus(bookingId, newStatus) {
        const token = localStorage.getItem('khedma_token');
        try {
            const res = await fetch(`/api/v1/provider/bookings/${bookingId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: newStatus })
            });

            if (res.ok) {
                // Reload dashboard data
                await loadProviderDashboard(token);
            }
        } catch(e) {
            console.error('Failed to update status', e);
        }
    }

    // ── Render Services Catalog ────────────────────────────────────────────────
    function renderServices(services) {
        const grid = document.getElementById('servicesGrid');
        grid.innerHTML = '';

        if (services.length === 0) {
            grid.innerHTML = '<div class="col-span-2 text-center text-xs text-slate-400 py-6">No services added yet. Click "+ Add Service" to offer a service.</div>';
            return;
        }

        services.forEach(s => {
            const card = document.createElement('div');
            card.className = 'p-4 rounded-2xl border border-slate-200/90 hover:border-amber-400/80 hover:shadow-md transition bg-white flex flex-col justify-between';
            card.innerHTML = `
                <div>
                    <div class="flex items-center justify-between gap-2 mb-1.5">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full border border-amber-200">${s.category}</span>
                        <span class="text-xs font-black text-slate-900">${parseFloat(s.price).toFixed(0)} TND</span>
                    </div>
                    <h4 class="font-extrabold text-sm text-slate-900">${s.title}</h4>
                    <p class="text-xs text-slate-500 mt-1 line-clamp-2">${s.description || 'Professional service.'}</p>
                </div>
                <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
                    <span>⏱ ~${s.duration_hours || 1.0} hrs</span>
                    <span class="text-emerald-600 font-bold">Active in Catalog</span>
                </div>
            `;
            grid.appendChild(card);
        });
    }

    // ── Render Reviews ─────────────────────────────────────────────────────────
    function renderReviews(reviews) {
        const list = document.getElementById('reviewsList');
        list.innerHTML = '';

        if (reviews.length === 0) {
            list.innerHTML = '<div class="text-xs text-slate-400 py-4 text-center">No reviews yet. Complete bookings to receive reviews.</div>';
            return;
        }

        reviews.forEach(r => {
            const item = document.createElement('div');
            item.className = 'p-3.5 bg-slate-50 rounded-2xl border border-slate-100 space-y-1.5';
            
            const stars = '★'.repeat(r.rating) + '☆'.repeat(5 - r.rating);
            item.innerHTML = `
                <div class="flex items-center justify-between">
                    <span class="text-xs font-extrabold text-slate-900">${r.client?.name || 'Verified Client'}</span>
                    <span class="text-amber-500 font-bold text-xs">${stars}</span>
                </div>
                <p class="text-xs text-slate-600 italic">"${r.comment}"</p>
                <div class="text-[10px] text-slate-400 text-right">${new Date(r.created_at).toLocaleDateString()}</div>
            `;
            list.appendChild(item);
        });
    }

    // ── Modal Handlers ─────────────────────────────────────────────────────────
    function openAddServiceModal() {
        document.getElementById('addServiceModal').classList.remove('hidden');
    }

    function closeAddServiceModal() {
        document.getElementById('addServiceModal').classList.add('hidden');
    }

    async function handleCreateService(e) {
        e.preventDefault();
        const token = localStorage.getItem('khedma_token');
        const saveBtn = document.getElementById('saveServiceBtn');

        const payload = {
            title: document.getElementById('newServiceTitle').value,
            category: document.getElementById('newServiceCategory').value,
            price: parseFloat(document.getElementById('newServicePrice').value),
            duration_hours: parseFloat(document.getElementById('newServiceDuration').value),
            description: document.getElementById('newServiceDesc').value,
        };

        saveBtn.disabled = true;
        saveBtn.textContent = 'Saving...';

        try {
            const res = await fetch('/api/v1/provider/services', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(payload)
            });

            if (res.ok) {
                closeAddServiceModal();
                await loadProviderDashboard(token);
            } else {
                alert('Could not save service. Please check fields.');
            }
        } catch(e) {
            console.error(e);
        } finally {
            saveBtn.disabled = false;
            saveBtn.textContent = 'Save to Database';
        }
    }

    // ── Render Client Dashboard ────────────────────────────────────────────────
    let clientFavoritesList = [];

    async function renderClientDashboard(user) {
        document.getElementById('clientDashboardView').classList.remove('hidden');
        document.getElementById('providerDashboardView').classList.add('hidden');

        document.getElementById('clientNameDisplay').textContent = user.name;
        document.getElementById('clientAvatar').textContent = user.name.charAt(0).toUpperCase();
        document.getElementById('clientEmailDisplay').textContent = user.email;
        document.getElementById('clientFullAddress').textContent = `${user.client_profile?.address || 'Les Berges du Lac 2'}, ${user.client_profile?.city || 'Tunis'}`;

        const token = localStorage.getItem('khedma_token');
        if (token) {
            await loadClientFavorites(token);
        }
    }

    async function loadClientFavorites(token) {
        const loading = document.getElementById('favoritesLoadingState');
        const empty = document.getElementById('clientFavoritesEmpty');
        const grid = document.getElementById('clientFavoritesGrid');
        const badge = document.getElementById('clientFavoritesCountBadge');

        try {
            const res = await fetch('/api/v1/client/favorites', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            if (!res.ok) throw new Error('Could not fetch favorites');

            const json = await res.json();
            clientFavoritesList = json.data || [];

            badge.textContent = `${clientFavoritesList.length} Saved`;

            loading.classList.add('hidden');

            if (clientFavoritesList.length === 0) {
                empty.classList.remove('hidden');
                grid.classList.add('hidden');
            } else {
                empty.classList.add('hidden');
                grid.classList.remove('hidden');
                renderClientFavoritesGrid(clientFavoritesList);
            }
        } catch(e) {
            console.error('Error loading client favorites:', e);
            loading.classList.add('hidden');
            empty.classList.remove('hidden');
        }
    }

    function renderClientFavoritesGrid(favorites) {
        const grid = document.getElementById('clientFavoritesGrid');
        grid.innerHTML = '';

        favorites.forEach(fav => {
            const card = document.createElement('div');
            card.className = 'p-5 rounded-2xl border border-slate-200/90 dark:border-slate-800 hover:border-rose-300 dark:hover:border-rose-900/60 hover:shadow-md transition bg-white dark:bg-slate-900 flex flex-col justify-between space-y-4';

            const stars = '★'.repeat(Math.round(fav.rating)) + '☆'.repeat(5 - Math.round(fav.rating));

            card.innerHTML = `
                <div class="space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-amber-500 text-white font-extrabold text-lg flex items-center justify-center shrink-0 shadow-sm">
                                ${fav.avatar_initial}
                            </div>
                            <div class="min-w-0">
                                <a href="${fav.profile_url}" class="font-extrabold text-sm text-slate-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition block truncate">
                                    ${fav.name}
                                </a>
                                <span class="text-[10px] font-bold text-blue-600 dark:text-blue-400 block truncate">
                                    ${fav.trade}
                                </span>
                            </div>
                        </div>

                        <!-- Remove favorite button -->
                        <button onclick="removeFavoriteFromDashboard(${fav.provider_id}, '${fav.name.replace(/'/g, "\\'")}')" 
                                class="p-1.5 rounded-lg text-slate-300 dark:text-slate-600 hover:text-rose-500 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition shrink-0" 
                                title="Remove from favorites">
                            <i class="bi bi-trash3 text-xs"></i>
                        </button>
                    </div>

                    <!-- Meta information -->
                    <div class="flex items-center justify-between text-xs pt-1 border-t border-slate-100 dark:border-slate-800/80">
                        <span class="text-slate-500 dark:text-slate-400 flex items-center gap-1">
                            <i class="bi bi-geo-alt-fill text-rose-500 text-[10px]"></i>
                            ${fav.city}
                        </span>
                        <span class="text-amber-500 font-bold text-xs flex items-center gap-1">
                            <i class="bi bi-star-fill text-[10px]"></i>
                            ${fav.rating.toFixed(1)} <span class="text-[10px] text-slate-400 font-normal">(${fav.reviews_count})</span>
                        </span>
                    </div>

                    ${fav.hourly_rate ? `
                        <div class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                            Rate: <strong class="text-slate-900 dark:text-white">${fav.hourly_rate.toFixed(0)} TND</strong> / hr
                        </div>
                    ` : ''}

                    ${fav.notes ? `
                        <div class="p-2 rounded-xl bg-amber-50/70 dark:bg-amber-950/30 border border-amber-200/50 dark:border-amber-900/30 text-[11px] text-amber-800 dark:text-amber-300 italic">
                            <i class="bi bi-pin-angle-fill mr-1"></i> "${fav.notes}"
                        </div>
                    ` : ''}
                </div>

                <div class="pt-2 border-t border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <a href="${fav.profile_url}" class="flex-1 py-2 text-center rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-800 dark:text-slate-200 text-xs font-bold transition">
                        View Profile
                    </a>
                </div>
            `;

            grid.appendChild(card);
        });
    }

    function filterClientFavorites(query) {
        const q = (query || '').toLowerCase().trim();
        const filtered = clientFavoritesList.filter(f => 
            f.name.toLowerCase().includes(q) || 
            (f.trade && f.trade.toLowerCase().includes(q)) ||
            (f.city && f.city.toLowerCase().includes(q))
        );
        renderClientFavoritesGrid(filtered);
    }

    async function removeFavoriteFromDashboard(providerId, name) {
        if (!confirm(`Remove ${name} from your saved providers?`)) return;

        const token = localStorage.getItem('khedma_token');
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const res = await fetch(`/api/v1/client/favorites/${providerId}`, {
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken || ''
                }
            });

            if (res.ok) {
                // Optimistically remove from list
                clientFavoritesList = clientFavoritesList.filter(f => f.provider_id !== providerId);
                document.getElementById('clientFavoritesCountBadge').textContent = `${clientFavoritesList.length} Saved`;

                if (clientFavoritesList.length === 0) {
                    document.getElementById('clientFavoritesEmpty').classList.remove('hidden');
                    document.getElementById('clientFavoritesGrid').classList.add('hidden');
                } else {
                    renderClientFavoritesGrid(clientFavoritesList);
                }
            } else {
                alert('Could not remove provider.');
            }
        } catch(e) {
            console.error(e);
        }
    }

    // ── Logout ─────────────────────────────────────────────────────────────────
    async function handleLogout() {
        const token = localStorage.getItem('khedma_token');
        if (token) {
            try {
                await fetch('/api/v1/auth/logout', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });
            } catch(e) {}
        }
        localStorage.removeItem('khedma_token');
        localStorage.removeItem('khedma_user');
        window.location.href = '/login';
    }
</script>
@endsection
