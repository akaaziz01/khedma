@extends('layouts.app')

@section('title', 'My Profile | Khedma')

@section('content')
<div class="py-10 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto space-y-8">

    <!-- Loading State -->
    <div id="profileLoading" class="py-20 text-center">
        <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-amber-600 border-t-transparent mb-3"></div>
        <p class="text-xs font-bold text-slate-500">Loading your profile information...</p>
    </div>

    <!-- Main Profile Container -->
    <div id="profileContainer" class="hidden space-y-8">
        
        <!-- Profile Hero Banner -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm relative overflow-hidden">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
                <div class="flex items-center gap-5">
                    <div id="userAvatarHero" class="w-18 h-18 sm:w-20 sm:h-20 rounded-2xl bg-gradient-to-tr from-amber-500 to-orange-600 text-white font-black text-3xl flex items-center justify-center shadow-lg shadow-amber-500/20 shrink-0">
                        U
                    </div>
                    <div class="space-y-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 id="userHeroName" class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white tracking-tight">
                                User Name
                            </h1>
                            <span id="userHeroRole" class="px-2.5 py-0.5 rounded-full text-xs font-extrabold uppercase bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300">
                                Provider
                            </span>
                            <span id="userHeroVerified" class="hidden inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-black bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300">
                                <i class="bi bi-patch-check-fill text-emerald-600"></i>
                                <span>Verified</span>
                            </span>
                        </div>
                        <p id="userHeroEmail" class="text-xs text-slate-400 font-medium">user@khedma.tn</p>
                        <p id="userHeroJoined" class="text-[11px] text-slate-400">Member since November 2026</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <a id="btnViewPublicProfile" href="#" target="_blank" class="hidden w-full sm:w-auto px-4 py-2.5 rounded-xl bg-blue-50 dark:bg-blue-950/40 text-blue-700 dark:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-900/40 text-xs font-bold transition flex items-center justify-center gap-1.5 border border-blue-200/60 dark:border-blue-900/60">
                        <i class="bi bi-eye"></i>
                        <span>View Public Profile</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Alert Notice -->
        <div id="alertFeedback" class="hidden p-4 rounded-2xl text-xs font-bold transition-all"></div>

        <!-- Edit Profile Form -->
        <form id="profileForm" onsubmit="saveProfileInfo(event)" class="space-y-8">
            
            <!-- SECTION 1: Personal Details -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-6">
                <div>
                    <h2 class="text-base font-black text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="bi bi-person-fill text-amber-500"></i>
                        <span>Personal Information</span>
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Your core contact and account credentials.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Full Name *</label>
                        <input type="text" id="inputName" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-amber-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Email Address</label>
                        <input type="email" id="inputEmail" disabled class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800/50 text-xs text-slate-400 font-medium cursor-not-allowed">
                        <span class="text-[10px] text-slate-400 mt-1 block">Account identity registered via Sanctum</span>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Phone Number</label>
                        <input type="text" id="inputPhone" placeholder="+216 20 000 000" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-amber-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">City / Governorate</label>
                        <select id="inputCity" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-amber-500">
                            <option value="Tunis">Tunis</option>
                            <option value="Ariana">Ariana</option>
                            <option value="Ben Arous">Ben Arous</option>
                            <option value="Manouba">Manouba</option>
                            <option value="Sousse">Sousse</option>
                            <option value="Sfax">Sfax</option>
                            <option value="Bizerte">Bizerte</option>
                            <option value="Nabeul">Nabeul</option>
                            <option value="Monastir">Monastir</option>
                            <option value="Kairouan">Kairouan</option>
                            <option value="Gabes">Gabes</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: Provider Specific Settings -->
            <div id="providerSpecificSection" class="hidden bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-6">
                <div>
                    <h2 class="text-base font-black text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="bi bi-tools text-amber-500"></i>
                        <span>Professional & Trade Profile</span>
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Details presented publicly to clients searching for your trade.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Business / Commercial Name</label>
                        <input type="text" id="inputBusinessName" placeholder="e.g. Hassan HVAC Solutions" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-amber-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Trade / Primary Category *</label>
                        <select id="inputCategory" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-amber-500">
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
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Standard Hourly Rate (TND) *</label>
                        <div class="relative">
                            <input type="number" step="0.5" min="0" id="inputHourlyRate" placeholder="40.00" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-amber-500">
                            <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">TND / hr</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Years of Experience *</label>
                        <div class="relative">
                            <input type="number" min="0" max="60" id="inputExperienceYears" placeholder="5" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-amber-500">
                            <span class="absolute right-3.5 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Years</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                        <div>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">Available for New Bookings</span>
                            <span class="text-[10px] text-slate-400">Shows green active status to potential clients</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="inputIsAvailable" class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer dark:bg-slate-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Professional Bio & Experience</label>
                    <textarea id="inputBio" rows="3" placeholder="Briefly introduce your expertise, years in business, and quality guarantees..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-amber-500"></textarea>
                </div>

                <!-- Skills Management -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Skills & Specializations</label>
                    <div class="flex items-center gap-2 mb-2">
                        <input type="text" id="inputNewSkill" placeholder="Add a skill (e.g. Copper soldering)..." class="flex-1 px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs">
                        <button type="button" onclick="addSkill()" class="px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300 transition">
                            <i class="bi bi-plus-lg"></i> Add
                        </button>
                    </div>
                    <div id="skillsPillsContainer" class="flex flex-wrap gap-2 pt-1">
                        <!-- Dynamic Skills Pills -->
                    </div>
                </div>
            </div>

            <!-- SECTION 3: Client Specific Details -->
            <div id="clientSpecificSection" class="hidden bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
                <div>
                    <h2 class="text-base font-black text-slate-900 dark:text-white flex items-center gap-2">
                        <i class="bi bi-geo-alt-fill text-blue-500"></i>
                        <span>Service Address Details</span>
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Used by providers when you request on-site home services.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Street Address</label>
                    <input type="text" id="inputClientAddress" placeholder="e.g. Avenue Habib Bourguiba, Appt 4B" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs text-slate-900 dark:text-white font-medium focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end">
                <button type="submit" id="btnSaveProfile" class="px-6 py-3 rounded-2xl bg-amber-600 hover:bg-amber-700 text-white font-extrabold text-xs shadow-md shadow-amber-500/20 transition flex items-center gap-2">
                    <i class="bi bi-check-lg text-sm"></i>
                    <span>Save Profile Changes</span>
                </button>
            </div>
        </form>

        <!-- SECTION 4: Change Password Box -->
        <div class="bg-white dark:bg-slate-900 rounded-3xl p-6 sm:p-8 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-6">
            <div>
                <h2 class="text-base font-black text-slate-900 dark:text-white flex items-center gap-2">
                    <i class="bi bi-shield-lock-fill text-slate-600 dark:text-slate-400"></i>
                    <span>Security & Password</span>
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Update your password to keep your account safe.</p>
            </div>

            <form id="passwordForm" onsubmit="updateUserPassword(event)" class="space-y-4 max-w-xl">
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Current Password *</label>
                    <input type="password" id="inputCurrentPassword" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-medium focus:ring-2 focus:ring-amber-500">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">New Password *</label>
                        <input type="password" id="inputNewPassword" minlength="8" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-medium focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Confirm New Password *</label>
                        <input type="password" id="inputConfirmPassword" minlength="8" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-medium focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>

                <button type="submit" id="btnUpdatePassword" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs transition">
                    Update Password
                </button>
            </form>
        </div>

    </div>
</div>

<script>
    let currentUser = null;
    let currentSkills = [];

    document.addEventListener('DOMContentLoaded', () => {
        fetchUserProfile();
    });

    async function fetchUserProfile() {
        const token = localStorage.getItem('khedma_token');
        if (!token) {
            window.location.href = '/login?redirect=/profile';
            return;
        }

        try {
            const res = await fetch('/api/v1/auth/user', {
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            if (!res.ok) {
                window.location.href = '/login?redirect=/profile';
                return;
            }

            const data = await res.json();
            currentUser = data.user;

            populateUserData(currentUser);

            document.getElementById('profileLoading').classList.add('hidden');
            document.getElementById('profileContainer').classList.remove('hidden');
        } catch (e) {
            console.error('Error fetching profile:', e);
        }
    }

    function populateUserData(user) {
        document.getElementById('userAvatarHero').textContent = user.name ? user.name.charAt(0).toUpperCase() : 'U';
        document.getElementById('userHeroName').textContent = user.name;
        document.getElementById('userHeroEmail').textContent = user.email;
        document.getElementById('userHeroRole').textContent = user.role;
        
        if (user.is_verified) {
            document.getElementById('userHeroVerified').classList.remove('hidden');
        }

        document.getElementById('inputName').value = user.name || '';
        document.getElementById('inputEmail').value = user.email || '';
        document.getElementById('inputPhone').value = user.phone || '';

        if (user.role === 'provider') {
            document.getElementById('providerSpecificSection').classList.remove('hidden');
            const profile = user.provider_profile || {};
            
            document.getElementById('inputBusinessName').value = profile.business_name || '';
            if (profile.category) document.getElementById('inputCategory').value = profile.category;
            document.getElementById('inputHourlyRate').value = profile.hourly_rate || '';
            document.getElementById('inputExperienceYears').value = profile.experience_years ?? 0;
            if (profile.city) document.getElementById('inputCity').value = profile.city;
            document.getElementById('inputIsAvailable').checked = profile.is_available !== false;
            document.getElementById('inputBio').value = profile.bio || '';

            currentSkills = Array.isArray(profile.skills) ? [...profile.skills] : [];
            renderSkillsPills();

            const pubLink = document.getElementById('btnViewPublicProfile');
            pubLink.href = '/providers/' + user.id;
            pubLink.classList.remove('hidden');
        } else if (user.role === 'client') {
            document.getElementById('clientSpecificSection').classList.remove('hidden');
            const profile = user.client_profile || {};
            if (profile.city) document.getElementById('inputCity').value = profile.city;
            document.getElementById('inputClientAddress').value = profile.address || '';
        }
    }

    function renderSkillsPills() {
        const container = document.getElementById('skillsPillsContainer');
        if (currentSkills.length === 0) {
            container.innerHTML = '<span class="text-xs text-slate-400 italic">No specific skills added yet.</span>';
            return;
        }

        container.innerHTML = currentSkills.map((skill, index) => `
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 text-xs font-bold border border-amber-200/60 dark:border-amber-900/40">
                <span>${skill}</span>
                <button type="button" onclick="removeSkill(${index})" class="text-amber-500 hover:text-amber-700 p-0.5">
                    <i class="bi bi-x"></i>
                </button>
            </span>
        `).join('');
    }

    function addSkill() {
        const input = document.getElementById('inputNewSkill');
        const skill = input.value.trim();
        if (skill && !currentSkills.includes(skill)) {
            currentSkills.push(skill);
            input.value = '';
            renderSkillsPills();
        }
    }

    function removeSkill(index) {
        currentSkills.splice(index, 1);
        renderSkillsPills();
    }

    async function saveProfileInfo(e) {
        e.preventDefault();
        const token = localStorage.getItem('khedma_token');
        if (!token) return;

        const btn = document.getElementById('btnSaveProfile');
        btn.disabled = true;
        btn.textContent = 'Saving...';

        const payload = {
            name: document.getElementById('inputName').value,
            phone: document.getElementById('inputPhone').value,
            city: document.getElementById('inputCity').value,
        };

        if (currentUser.role === 'provider') {
            payload.business_name = document.getElementById('inputBusinessName').value;
            payload.category = document.getElementById('inputCategory').value;
            payload.hourly_rate = parseFloat(document.getElementById('inputHourlyRate').value) || 0;
            payload.experience_years = parseInt(document.getElementById('inputExperienceYears').value) || 0;
            payload.is_available = document.getElementById('inputIsAvailable').checked;
            payload.bio = document.getElementById('inputBio').value;
            payload.skills = currentSkills;
        } else if (currentUser.role === 'client') {
            payload.address = document.getElementById('inputClientAddress').value;
        }

        try {
            const res = await fetch('/api/v1/profile', {
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
                showAlert('success', 'Profile updated successfully!');
                // Update header info in localStorage
                localStorage.setItem('khedma_user', JSON.stringify({
                    id: data.user.id,
                    name: data.user.name,
                    email: data.user.email,
                    role: data.user.role,
                    phone: data.user.phone
                }));
                
                // Re-sync header
                if (typeof initHeaderState === 'function') {
                    initHeaderState();
                }

                populateUserData(data.user);
            } else {
                showAlert('error', data.message || 'Failed to update profile.');
            }
        } catch (err) {
            console.error(err);
            showAlert('error', 'Network error occurred while saving profile.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-lg text-sm"></i><span>Save Profile Changes</span>';
        }
    }

    async function updateUserPassword(e) {
        e.preventDefault();
        const token = localStorage.getItem('khedma_token');
        if (!token) return;

        const currentPass = document.getElementById('inputCurrentPassword').value;
        const newPass = document.getElementById('inputNewPassword').value;
        const confirmPass = document.getElementById('inputConfirmPassword').value;

        if (newPass !== confirmPass) {
            showAlert('error', 'New passwords do not match.');
            return;
        }

        const btn = document.getElementById('btnUpdatePassword');
        btn.disabled = true;
        btn.textContent = 'Updating...';

        try {
            const res = await fetch('/api/v1/profile/password', {
                method: 'PUT',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    current_password: currentPass,
                    password: newPass,
                    password_confirmation: confirmPass
                })
            });

            const data = await res.json();
            if (res.ok) {
                showAlert('success', 'Password updated successfully!');
                document.getElementById('passwordForm').reset();
            } else {
                showAlert('error', data.message || 'Failed to update password.');
            }
        } catch (err) {
            console.error(err);
            showAlert('error', 'Network error occurred while changing password.');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Update Password';
        }
    }

    function showAlert(type, message) {
        const el = document.getElementById('alertFeedback');
        el.className = type === 'success' 
            ? 'p-4 rounded-2xl text-xs font-bold bg-emerald-50 text-emerald-800 dark:bg-emerald-950/60 dark:text-emerald-300 border border-emerald-200 block'
            : 'p-4 rounded-2xl text-xs font-bold bg-red-50 text-red-800 dark:bg-red-950/60 dark:text-red-300 border border-red-200 block';
        el.textContent = message;
        el.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

        setTimeout(() => {
            el.className = 'hidden';
        }, 5000);
    }
</script>
@endsection
