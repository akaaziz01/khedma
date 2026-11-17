@extends('layouts.app')

@section('title', 'Manage My Profile | Khedma')

@section('content')
<div class="py-8 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto space-y-6">

    <!-- Loading Skeleton -->
    <div id="profileLoading" class="py-24 text-center">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-blue-600 border-t-transparent mb-4"></div>
        <p class="text-sm text-slate-500 dark:text-slate-400 font-semibold">Loading your profile...</p>
    </div>

    <!-- Main Profile Container -->
    <div id="profileContent" class="hidden space-y-6">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200 dark:border-slate-800">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight flex items-center gap-3">
                    <i class="bi bi-person-circle text-blue-600 dark:text-blue-400"></i>
                    <span>Manage Profile</span>
                </h1>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                    Update your account details, photo, and preferences.
                </p>
            </div>
            <div>
                <span id="roleBadge" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                    Client
                </span>
            </div>
        </div>

        <!-- Success Toast Alert -->
        <div id="successAlert" class="hidden p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/50 flex items-center gap-3 text-emerald-800 dark:text-emerald-300 text-sm font-medium transition">
            <i class="bi bi-check-circle-fill text-lg text-emerald-600 dark:text-emerald-400 shrink-0"></i>
            <span id="successMessage">Profile updated successfully!</span>
        </div>

        <!-- Global Error Alert -->
        <div id="globalErrorAlert" class="hidden p-4 rounded-2xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800/50 flex items-center gap-3 text-red-800 dark:text-red-300 text-sm font-medium transition">
            <i class="bi bi-exclamation-triangle-fill text-lg text-red-600 dark:text-red-400 shrink-0"></i>
            <span id="globalErrorMessage">Please check the highlighted errors below.</span>
        </div>

        <form id="profileForm" onsubmit="handleProfileSubmit(event)" enctype="multipart/form-data" class="space-y-6">

            <!-- ========================================== -->
            <!-- 1. AVATAR SECTION                          -->
            <!-- ========================================== -->
            <div class="p-6 sm:p-8 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100 dark:border-slate-800/80">
                    <i class="bi bi-image text-slate-400 dark:text-slate-500 text-base"></i>
                    <h2 class="text-base font-extrabold text-slate-900 dark:text-white">Profile Photo</h2>
                </div>

                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 pt-2">
                    <!-- Avatar Display / Preview -->
                    <div class="relative shrink-0">
                        <div id="avatarContainer" class="w-24 h-24 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-black text-3xl flex items-center justify-center shadow-md overflow-hidden border-2 border-slate-200 dark:border-slate-700">
                            <span id="avatarInitials">U</span>
                            <img id="avatarPreviewImg" src="" alt="Avatar preview" class="w-full h-full object-cover hidden">
                        </div>
                    </div>

                    <!-- Avatar Controls -->
                    <div class="flex-1 space-y-3 text-center sm:text-left">
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Upload a JPEG, PNG, or WebP photo up to 2 MB. Square images work best.
                        </p>
                        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2.5">
                            <label for="avatarInput" class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-blue-50 dark:bg-blue-950/50 hover:bg-blue-100 dark:hover:bg-blue-900/60 text-blue-700 dark:text-blue-300 font-bold text-xs border border-blue-200 dark:border-blue-800 transition shadow-2xs">
                                <i class="bi bi-upload"></i>
                                <span>Upload New Photo</span>
                            </label>
                            <input type="file" id="avatarInput" name="avatar" accept="image/jpeg,image/png,image/webp,image/jpg" class="hidden" onchange="previewAvatar(event)">

                            <button type="button" id="removeAvatarBtn" onclick="handleRemoveAvatar()" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/40 border border-transparent hover:border-red-200 dark:hover:border-red-900/40 transition">
                                <i class="bi bi-trash"></i>
                                <span>Remove Photo</span>
                            </button>
                        </div>
                        <div id="error-avatar" class="text-xs font-semibold text-red-600 dark:text-red-400 hidden"></div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- 2. PERSONAL & CONTACT DETAILS              -->
            <!-- ========================================== -->
            <div class="p-6 sm:p-8 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100 dark:border-slate-800/80">
                    <i class="bi bi-person-lines-fill text-slate-400 dark:text-slate-500 text-base"></i>
                    <h2 class="text-base font-extrabold text-slate-900 dark:text-white">Personal Information</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <!-- Full Name -->
                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" required max="255"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                        <div id="error-name" class="text-[11px] font-semibold text-red-600 dark:text-red-400 mt-1 hidden"></div>
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" required max="255"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                        <div id="error-email" class="text-[11px] font-semibold text-red-600 dark:text-red-400 mt-1 hidden"></div>
                    </div>

                    <!-- Phone Number -->
                    <div class="sm:col-span-2">
                        <label for="phone" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Phone Number <span id="phoneRequiredBadge" class="text-red-500 hidden">*</span>
                            <span class="text-[10px] font-normal text-slate-400 ml-1.5">(8 digits, e.g. +216 20 000 001)</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400 text-xs">
                                <i class="bi bi-telephone"></i>
                            </span>
                            <input type="tel" id="phone" name="phone" placeholder="+216 98 123 456"
                                class="w-full pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                        </div>
                        <div id="error-phone" class="text-[11px] font-semibold text-red-600 dark:text-red-400 mt-1 hidden"></div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- 3. LOCATION SECTION                        -->
            <!-- ========================================== -->
            <div id="locationSection" class="p-6 sm:p-8 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100 dark:border-slate-800/80">
                    <i class="bi bi-geo-alt-fill text-slate-400 dark:text-slate-500 text-base"></i>
                    <h2 class="text-base font-extrabold text-slate-900 dark:text-white">Location Details</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <!-- City / Governorate -->
                    <div>
                        <label for="city" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Governorate (City)
                        </label>
                        <select id="city" name="city"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                            <option value="">-- Select Governorate --</option>
                            <option value="Ariana">Ariana</option>
                            <option value="Beja">Beja</option>
                            <option value="Ben Arous">Ben Arous</option>
                            <option value="Bizerte">Bizerte</option>
                            <option value="Gabes">Gabes</option>
                            <option value="Gafsa">Gafsa</option>
                            <option value="Jendouba">Jendouba</option>
                            <option value="Kairouan">Kairouan</option>
                            <option value="Kasserine">Kasserine</option>
                            <option value="Kebili">Kebili</option>
                            <option value="Kef">Kef</option>
                            <option value="Mahdia">Mahdia</option>
                            <option value="Manouba">Manouba</option>
                            <option value="Medenine">Medenine</option>
                            <option value="Monastir">Monastir</option>
                            <option value="Nabeul">Nabeul</option>
                            <option value="Sfax">Sfax</option>
                            <option value="Sidi Bouzid">Sidi Bouzid</option>
                            <option value="Siliana">Siliana</option>
                            <option value="Sousse">Sousse</option>
                            <option value="Tataouine">Tataouine</option>
                            <option value="Tozeur">Tozeur</option>
                            <option value="Tunis">Tunis</option>
                            <option value="Zaghouan">Zaghouan</option>
                        </select>
                        <div id="error-city" class="text-[11px] font-semibold text-red-600 dark:text-red-400 mt-1 hidden"></div>
                    </div>

                    <!-- Client Address -->
                    <div id="addressGroup">
                        <label for="address" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Street Address
                        </label>
                        <input type="text" id="address" name="address" max="255" placeholder="e.g. Avenue Habib Bourguiba"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-blue-500 focus:outline-none transition">
                        <div id="error-address" class="text-[11px] font-semibold text-red-600 dark:text-red-400 mt-1 hidden"></div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- 4. PROFESSIONAL DETAILS (PROVIDERS ONLY)   -->
            <!-- ========================================== -->
            <div id="providerSection" class="hidden p-6 sm:p-8 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-xs space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100 dark:border-slate-800/80">
                    <i class="bi bi-briefcase-fill text-amber-500 text-base"></i>
                    <h2 class="text-base font-extrabold text-slate-900 dark:text-white">Professional Details</h2>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <!-- Business / Trade Name -->
                    <div>
                        <label for="business_name" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Business / Brand Name
                        </label>
                        <input type="text" id="business_name" name="business_name" max="150" placeholder="e.g. Tunis Electrical Pros"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-amber-500 focus:outline-none transition">
                        <div id="error-business_name" class="text-[11px] font-semibold text-red-600 dark:text-red-400 mt-1 hidden"></div>
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Main Category <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="category" name="category" max="100" placeholder="e.g. Plumbing, HVAC, Electricity"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-amber-500 focus:outline-none transition">
                        <div id="error-category" class="text-[11px] font-semibold text-red-600 dark:text-red-400 mt-1 hidden"></div>
                    </div>

                    <!-- Hourly Rate -->
                    <div>
                        <label for="hourly_rate" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Hourly Rate (TND)
                        </label>
                        <div class="relative">
                            <input type="number" step="0.5" min="0" id="hourly_rate" name="hourly_rate" placeholder="35.00"
                                class="w-full pl-3.5 pr-12 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-amber-500 focus:outline-none transition">
                            <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-slate-400 text-xs font-bold">
                                TND
                            </span>
                        </div>
                        <div id="error-hourly_rate" class="text-[11px] font-semibold text-red-600 dark:text-red-400 mt-1 hidden"></div>
                    </div>

                    <!-- Professional Bio -->
                    <div class="sm:col-span-2">
                        <label for="bio" class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Bio & Experience Summary
                        </label>
                        <textarea id="bio" name="bio" rows="3" max="1000" placeholder="Describe your background, certifications, and work guarantees..."
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-amber-500 focus:outline-none transition"></textarea>
                        <div id="error-bio" class="text-[11px] font-semibold text-red-600 dark:text-red-400 mt-1 hidden"></div>
                    </div>

                    <!-- Skills Tags Editor -->
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Skills & Specializations
                        </label>
                        <div class="flex items-center gap-2 mb-2">
                            <input type="text" id="skillInput" placeholder="Add skill and press Add (e.g. Copper pipes)"
                                class="flex-1 px-3.5 py-2 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-white text-xs font-medium focus:ring-2 focus:ring-amber-500 focus:outline-none"
                                onkeydown="if(event.key === 'Enter'){ event.preventDefault(); addSkill(); }">
                            <button type="button" onclick="addSkill()"
                                class="px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs transition">
                                Add
                            </button>
                        </div>
                        <div id="skillsTagsContainer" class="flex flex-wrap gap-1.5 min-h-[32px] p-2 rounded-xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200/80 dark:border-slate-800">
                            <!-- Skill pills will be rendered dynamically -->
                        </div>
                        <div id="error-skills" class="text-[11px] font-semibold text-red-600 dark:text-red-400 mt-1 hidden"></div>
                    </div>
                </div>
            </div>

            <!-- Submit Button Area -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="/dashboard" class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    Cancel
                </a>
                <button type="submit" id="submitBtn"
                    class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-md shadow-blue-500/20 transition flex items-center gap-2">
                    <span id="submitSpinner" class="hidden inline-block animate-spin rounded-full h-3.5 w-3.5 border-2 border-white border-t-transparent"></span>
                    <span id="submitBtnText">Save Changes</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let currentUser = null;
    let currentProfile = null;
    let skillsList = [];
    let pendingAvatarFile = null;

    document.addEventListener('DOMContentLoaded', async () => {
        const token = localStorage.getItem('khedma_token');
        if (!token) {
            window.location.href = '/login';
            return;
        }

        try {
            // Check auth first
            const userRes = await fetch('/api/v1/auth/user', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            if (!userRes.ok) {
                localStorage.removeItem('khedma_token');
                localStorage.removeItem('khedma_user');
                window.location.href = '/login';
                return;
            }

            // Fetch profile data
            const profileRes = await fetch('/api/v1/profile', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            if (!profileRes.ok) {
                throw new Error('Failed to load profile data.');
            }

            const data = await profileRes.json();
            currentUser = data.user;
            currentProfile = data.profile;

            populateForm(currentUser, currentProfile);

        } catch (err) {
            console.error('Error loading profile:', err);
            showGlobalError('Could not load profile. Please refresh or try again.');
        } finally {
            document.getElementById('profileLoading').classList.add('hidden');
            document.getElementById('profileContent').classList.remove('hidden');
        }
    });

    function populateForm(user, profile) {
        // 1. Role Badging
        const roleBadge = document.getElementById('roleBadge');
        const role = user.role;
        roleBadge.textContent = role.toUpperCase();

        const providerSection = document.getElementById('providerSection');
        const addressGroup = document.getElementById('addressGroup');
        const phoneRequiredBadge = document.getElementById('phoneRequiredBadge');
        const locationSection = document.getElementById('locationSection');

        if (role === 'provider') {
            roleBadge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-amber-50 text-amber-700 dark:bg-amber-950/60 dark:text-amber-300 border border-amber-200 dark:border-amber-800';
            providerSection.classList.remove('hidden');
            addressGroup.classList.add('hidden');
            phoneRequiredBadge.classList.remove('hidden');
        } else if (role === 'admin') {
            roleBadge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-purple-50 text-purple-700 dark:bg-purple-950/60 dark:text-purple-300 border border-purple-200 dark:border-purple-800';
            providerSection.classList.add('hidden');
            locationSection.classList.add('hidden');
        } else {
            roleBadge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold uppercase tracking-wider bg-blue-50 text-blue-700 dark:bg-blue-950/60 dark:text-blue-300 border border-blue-200 dark:border-blue-800';
            providerSection.classList.add('hidden');
            addressGroup.classList.remove('hidden');
            phoneRequiredBadge.classList.add('hidden');
        }

        // 2. Personal fields
        document.getElementById('name').value = user.name || '';
        document.getElementById('email').value = user.email || '';
        document.getElementById('phone').value = user.phone || '';

        // 3. Avatar
        renderAvatar(user.avatar_url, user.name);

        // 4. Location
        const citySelect = document.getElementById('city');
        const currentCity = profile?.city || '';
        if (currentCity) {
            let optionExists = false;
            for (let i = 0; i < citySelect.options.length; i++) {
                if (citySelect.options[i].value.toLowerCase() === currentCity.toLowerCase()) {
                    citySelect.selectedIndex = i;
                    optionExists = true;
                    break;
                }
            }
            if (!optionExists) {
                const opt = document.createElement('option');
                opt.value = currentCity;
                opt.textContent = currentCity + ' (Custom)';
                opt.selected = true;
                citySelect.appendChild(opt);
            }
        }

        if (profile?.address) {
            document.getElementById('address').value = profile.address;
        }

        // 5. Provider details
        if (role === 'provider' && profile) {
            document.getElementById('business_name').value = profile.business_name || '';
            document.getElementById('category').value = profile.category || '';
            document.getElementById('hourly_rate').value = profile.hourly_rate || '';
            document.getElementById('bio').value = profile.bio || '';

            if (Array.isArray(profile.skills)) {
                skillsList = [...profile.skills];
            } else if (typeof profile.skills === 'string') {
                try { skillsList = JSON.parse(profile.skills); } catch(e) { skillsList = []; }
            }
            renderSkillsTags();
        }
    }

    function renderAvatar(avatarUrl, name) {
        const previewImg = document.getElementById('avatarPreviewImg');
        const initials = document.getElementById('avatarInitials');
        const removeBtn = document.getElementById('removeAvatarBtn');

        if (avatarUrl) {
            previewImg.src = avatarUrl;
            previewImg.classList.remove('hidden');
            initials.classList.add('hidden');
            removeBtn.classList.remove('hidden');
        } else {
            previewImg.src = '';
            previewImg.classList.add('hidden');
            initials.textContent = (name || 'U').charAt(0).toUpperCase();
            initials.classList.remove('hidden');
            removeBtn.classList.add('hidden');
        }
    }

    function previewAvatar(event) {
        const file = event.target.files[0];
        if (!file) return;

        pendingAvatarFile = file;
        const objectUrl = URL.createObjectURL(file);

        const previewImg = document.getElementById('avatarPreviewImg');
        const initials = document.getElementById('avatarInitials');
        const removeBtn = document.getElementById('removeAvatarBtn');

        previewImg.src = objectUrl;
        previewImg.classList.remove('hidden');
        initials.classList.add('hidden');
        removeBtn.classList.remove('hidden');

        clearFieldError('avatar');
    }

    async function handleRemoveAvatar() {
        const token = localStorage.getItem('khedma_token');

        // If user selected a file locally without saving, just reset it
        if (pendingAvatarFile && !currentUser?.avatar_url) {
            pendingAvatarFile = null;
            document.getElementById('avatarInput').value = '';
            renderAvatar(null, currentUser?.name);
            return;
        }

        if (!confirm('Are you sure you want to remove your profile photo?')) return;

        try {
            const res = await fetch('/api/v1/profile/avatar', {
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            if (res.ok) {
                const data = await res.json();
                pendingAvatarFile = null;
                document.getElementById('avatarInput').value = '';
                if (currentUser) {
                    currentUser.avatar_url = null;
                }
                renderAvatar(null, currentUser?.name);

                // Update local storage and navbar
                const stored = localStorage.getItem('khedma_user');
                if (stored) {
                    const u = JSON.parse(stored);
                    u.avatar_url = null;
                    localStorage.setItem('khedma_user', JSON.stringify(u));
                    if (window.initHeaderState) window.initHeaderState();
                }

                showSuccess('Photo removed successfully.');
            } else {
                showGlobalError('Could not remove photo.');
            }
        } catch(e) {
            console.error(e);
            showGlobalError('Error removing photo.');
        }
    }

    function renderSkillsTags() {
        const container = document.getElementById('skillsTagsContainer');
        container.innerHTML = '';

        if (skillsList.length === 0) {
            container.innerHTML = '<span class="text-xs text-slate-400 italic">No skills added yet.</span>';
            return;
        }

        skillsList.forEach((skill, index) => {
            const pill = document.createElement('span');
            pill.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-50 text-amber-900 dark:bg-amber-950/50 dark:text-amber-200 border border-amber-200 dark:border-amber-800';
            pill.innerHTML = `
                <span>${escapeHtml(skill)}</span>
                <button type="button" onclick="removeSkill(${index})" class="hover:text-red-600 transition">
                    <i class="bi bi-x"></i>
                </button>
            `;
            container.appendChild(pill);
        });
    }

    function addSkill() {
        const input = document.getElementById('skillInput');
        const val = input.value.trim();
        if (val && !skillsList.includes(val)) {
            if (skillsList.length >= 20) {
                alert('Maximum 20 skills allowed.');
                return;
            }
            skillsList.push(val);
            renderSkillsTags();
            input.value = '';
        }
    }

    function removeSkill(index) {
        skillsList.splice(index, 1);
        renderSkillsTags();
    }

    async function handleProfileSubmit(e) {
        e.preventDefault();
        clearAllErrors();
        hideAlerts();

        const token = localStorage.getItem('khedma_token');
        const submitBtn = document.getElementById('submitBtn');
        const spinner = document.getElementById('submitSpinner');
        const btnText = document.getElementById('submitBtnText');

        submitBtn.disabled = true;
        spinner.classList.remove('hidden');
        btnText.textContent = 'Saving...';

        const formData = new FormData();
        formData.append('name', document.getElementById('name').value);
        formData.append('email', document.getElementById('email').value);

        const phoneVal = document.getElementById('phone').value;
        if (phoneVal) formData.append('phone', phoneVal);

        const avatarInput = document.getElementById('avatarInput');
        if (avatarInput.files.length > 0) {
            formData.append('avatar', avatarInput.files[0]);
        }

        const role = currentUser?.role;

        if (role === 'client') {
            formData.append('city', document.getElementById('city').value);
            formData.append('address', document.getElementById('address').value);
        } else if (role === 'provider') {
            formData.append('city', document.getElementById('city').value);
            formData.append('business_name', document.getElementById('business_name').value);
            formData.append('category', document.getElementById('category').value);
            formData.append('hourly_rate', document.getElementById('hourly_rate').value);
            formData.append('bio', document.getElementById('bio').value);

            // Append skills
            skillsList.forEach(s => formData.append('skills[]', s));
        }

        try {
            const res = await fetch('/api/v1/profile', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await res.json();

            if (res.ok) {
                currentUser = data.user;
                currentProfile = data.profile;

                // Update localStorage khedma_user
                localStorage.setItem('khedma_user', JSON.stringify(data.user));

                // Refresh navbar immediately
                if (window.initHeaderState) {
                    window.initHeaderState();
                }

                showSuccess('Profile updated successfully!');
                renderAvatar(data.user.avatar_url, data.user.name);
                pendingAvatarFile = null;
                avatarInput.value = '';

                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else if (res.status === 422) {
                showFieldErrors(data.errors || {});
                showGlobalError(data.message || 'Please correct the highlighted fields.');
            } else {
                showGlobalError(data.message || 'An error occurred while saving.');
            }
        } catch(err) {
            console.error('Save profile error:', err);
            showGlobalError('Network error. Could not save changes.');
        } finally {
            submitBtn.disabled = false;
            spinner.classList.add('hidden');
            btnText.textContent = 'Save Changes';
        }
    }

    function showFieldErrors(errors) {
        for (const [field, messages] of Object.entries(errors)) {
            const errDiv = document.getElementById(`error-${field}`);
            if (errDiv) {
                errDiv.textContent = messages[0];
                errDiv.classList.remove('hidden');
            }
            const input = document.getElementById(field);
            if (input) {
                input.classList.add('border-red-500', 'focus:ring-red-500');
            }
        }
    }

    function clearFieldError(field) {
        const errDiv = document.getElementById(`error-${field}`);
        if (errDiv) {
            errDiv.textContent = '';
            errDiv.classList.add('hidden');
        }
        const input = document.getElementById(field);
        if (input) {
            input.classList.remove('border-red-500', 'focus:ring-red-500');
        }
    }

    function clearAllErrors() {
        document.querySelectorAll('[id^="error-"]').forEach(el => {
            el.textContent = '';
            el.classList.add('hidden');
        });
        document.querySelectorAll('input, select, textarea').forEach(el => {
            el.classList.remove('border-red-500', 'focus:ring-red-500');
        });
    }

    function showSuccess(msg) {
        const alert = document.getElementById('successAlert');
        document.getElementById('successMessage').textContent = msg;
        alert.classList.remove('hidden');
        setTimeout(() => alert.classList.add('hidden'), 5000);
    }

    function showGlobalError(msg) {
        const alert = document.getElementById('globalErrorAlert');
        document.getElementById('globalErrorMessage').textContent = msg;
        alert.classList.remove('hidden');
    }

    function hideAlerts() {
        document.getElementById('successAlert').classList.add('hidden');
        document.getElementById('globalErrorAlert').classList.add('hidden');
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>
@endsection
