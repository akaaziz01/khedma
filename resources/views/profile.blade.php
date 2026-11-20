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

                <!-- Previous Work & Portfolio Photos Section -->
                <div class="border-t border-slate-100 dark:border-slate-800 pt-6 space-y-4">
                    <div class="flex items-center justify-between flex-wrap gap-3">
                        <div>
                            <h3 class="text-sm font-black text-slate-900 dark:text-white flex items-center gap-2">
                                <i class="bi bi-images text-blue-600"></i>
                                <span>Previous Work & Portfolio Photos</span>
                            </h3>
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5">
                                Upload real photos of your past installations, repairs, and projects to prove the quality of your craft.
                            </p>
                        </div>
                        <button type="button" onclick="openUploadPhotoModal()" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs transition flex items-center gap-1.5 shadow-sm">
                            <i class="bi bi-cloud-arrow-up-fill text-sm"></i>
                            <span>Upload Work Photo</span>
                        </button>
                    </div>

                    <!-- Live Portfolio Grid -->
                    <div id="portfolioGrid" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 pt-1">
                        <!-- Rendered dynamically -->
                    </div>

                    <!-- Empty state -->
                    <div id="portfolioEmptyState" class="hidden p-8 rounded-2xl bg-slate-50 dark:bg-slate-800/40 text-center border-2 border-dashed border-slate-200 dark:border-slate-700 space-y-2">
                        <div class="w-12 h-12 rounded-2xl bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center mx-auto text-xl">
                            <i class="bi bi-camera-fill"></i>
                        </div>
                        <p class="text-xs font-bold text-slate-700 dark:text-slate-300">No project photos uploaded yet</p>
                        <p class="text-[11px] text-slate-400 max-w-sm mx-auto">Upload photos of previous jobs to demonstrate quality and build client confidence.</p>
                        <button type="button" onclick="openUploadPhotoModal()" class="mt-2 inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-900 text-white dark:bg-white dark:text-slate-900 text-xs font-bold hover:opacity-90 transition">
                            <i class="bi bi-plus-lg"></i> Add First Project Photo
                        </button>
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

<!-- ========================================== -->
<!-- MODAL: UPLOAD WORK PHOTO                   -->
<!-- ========================================== -->
<div id="uploadPhotoModal" style="display: none;" class="fixed inset-0 z-50 bg-slate-900/60 dark:bg-black/70 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 max-w-lg w-full p-6 sm:p-8 space-y-5">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-950/70 text-amber-600 dark:text-amber-400 flex items-center justify-center text-lg">
                    <i class="bi bi-cloud-arrow-up-fill"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 dark:text-white">Upload Work Photo</h3>
                    <p class="text-[11px] text-slate-400">Prove the quality of your craft to clients</p>
                </div>
            </div>
            <button type="button" onclick="closeUploadPhotoModal()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <form id="uploadPhotoForm" onsubmit="submitPortfolioPhoto(event)" class="space-y-4">
            <!-- File Input / Preview -->
            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Project Photo *</label>
                <div id="photoDropArea" class="relative border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-amber-500 dark:hover:border-amber-400 rounded-2xl p-4 text-center cursor-pointer transition bg-slate-50 dark:bg-slate-800/40">
                    <input type="file" id="inputPhotoFile" accept="image/png,image/jpeg,image/webp,image/jpg" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" onchange="previewSelectedPhoto(this)">
                    <div id="photoPrompt">
                        <i class="bi bi-image text-3xl text-slate-400 mb-1 block"></i>
                        <span class="text-xs font-bold text-slate-700 dark:text-slate-200 block">Click to browse or drag & drop photo</span>
                        <span class="text-[10px] text-slate-400">PNG, JPG, or WEBP (Max 5MB)</span>
                    </div>
                    <div id="photoPreviewContainer" style="display: none;" class="relative">
                        <img id="photoPreviewImg" src="#" alt="Preview" class="max-h-48 mx-auto rounded-xl object-cover shadow-sm">
                        <button type="button" onclick="clearPhotoSelection(event)" class="absolute top-1 right-1 p-1 bg-red-600 text-white rounded-full text-xs hover:bg-red-700 shadow" title="Remove selection">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                </div>
                <!-- Alternate option: or via Image URL -->
                <div class="mt-2 flex items-center gap-2">
                    <span class="text-[10px] text-slate-400 font-bold uppercase shrink-0">or image URL:</span>
                    <input type="url" id="inputPhotoUrl" placeholder="https://images.unsplash.com/photo-..." class="flex-1 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs text-slate-900 dark:text-white">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Project / Work Title *</label>
                <input type="text" id="inputPhotoTitle" required placeholder="e.g. Bathroom Copper Piping & Drain Replacement" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-medium focus:ring-2 focus:ring-amber-500 text-slate-900 dark:text-white">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 mb-1.5">Quality Details & Description (Optional)</label>
                <textarea id="inputPhotoCaption" rows="2" placeholder="e.g. Fitted high-pressure valves and custom manifolds with 2-year warranty." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 text-xs font-medium focus:ring-2 focus:ring-amber-500 text-slate-900 dark:text-white"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                <button type="button" onclick="closeUploadPhotoModal()" class="px-4 py-2 text-xs font-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl transition">
                    Cancel
                </button>
                <button type="submit" id="btnSubmitPhoto" class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-slate-950 font-black text-xs rounded-xl shadow-md transition flex items-center gap-1.5">
                    <i class="bi bi-cloud-arrow-up-fill text-sm"></i>
                    <span>Upload & Publish</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- LIGHTBOX PREVIEW MODAL                     -->
<!-- ========================================== -->
<div id="profilePhotoLightbox" style="display: none;" class="fixed inset-0 z-50 bg-slate-950/90 backdrop-blur-md flex items-center justify-center p-4">
    <div class="relative max-w-3xl w-full bg-slate-900 rounded-3xl overflow-hidden shadow-2xl border border-slate-800 text-white space-y-4 p-4 sm:p-6">
        <div class="flex items-center justify-between pb-2 border-b border-slate-800">
            <div>
                <h4 id="profileLightboxTitle" class="font-extrabold text-base text-white">Project Photo</h4>
                <p id="profileLightboxCaption" class="text-xs text-slate-400 mt-0.5"></p>
            </div>
            <button type="button" onclick="closeProfileLightbox()" class="p-2 text-slate-400 hover:text-white rounded-xl bg-slate-800/80 transition">
                <i class="bi bi-x-lg text-sm"></i>
            </button>
        </div>
        <div class="max-h-[70vh] flex items-center justify-center overflow-hidden rounded-2xl bg-black/40">
            <img id="profileLightboxImg" src="#" alt="Project Photo" class="max-h-[70vh] w-auto max-w-full object-contain rounded-xl">
        </div>
    </div>
</div>

<script>
    let currentUser = null;
    let currentSkills = [];
    let currentPortfolio = [];

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

            currentPortfolio = Array.isArray(profile.portfolio_photos) ? [...profile.portfolio_photos] : [];
            renderPortfolioGrid();

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

    // ==========================================
    // PORTFOLIO & PREVIOUS WORK MANAGEMENT
    // ==========================================
    function renderPortfolioGrid() {
        const grid = document.getElementById('portfolioGrid');
        const empty = document.getElementById('portfolioEmptyState');
        if (!grid || !empty) return;

        if (currentPortfolio.length === 0) {
            grid.innerHTML = '';
            empty.classList.remove('hidden');
            return;
        }

        empty.classList.add('hidden');
        grid.innerHTML = currentPortfolio.map((item, idx) => {
            const photoId = item.id || idx;
            const url = item.url || '';
            const title = item.title || 'Work Photo';
            const caption = item.caption || '';
            return `
                <div class="group relative rounded-2xl overflow-hidden bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col justify-between">
                    <div class="relative aspect-4/3 overflow-hidden cursor-pointer bg-slate-200 dark:bg-slate-900" onclick="openProfileLightbox('${escapeHtml(url)}', '${escapeHtml(title)}', '${escapeHtml(caption)}')">
                        <img src="${url}" alt="${escapeHtml(title)}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300" onerror="this.src='https://placehold.co/600x400/png?text=Photo'">
                        <div class="absolute inset-0 bg-linear-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition flex items-end p-2.5">
                            <span class="text-white text-[11px] font-bold truncate flex items-center gap-1">
                                <i class="bi bi-zoom-in"></i> View Full
                            </span>
                        </div>
                    </div>
                    <div class="p-3 space-y-1">
                        <h4 class="text-xs font-extrabold text-slate-900 dark:text-white truncate" title="${escapeHtml(title)}">${escapeHtml(title)}</h4>
                        ${caption ? `<p class="text-[10px] text-slate-500 dark:text-slate-400 line-clamp-2" title="${escapeHtml(caption)}">${escapeHtml(caption)}</p>` : ''}
                        <div class="pt-2 flex justify-end">
                            <button type="button" onclick="confirmDeletePhoto('${photoId}')" class="text-[10px] font-bold text-red-600 hover:text-red-700 dark:text-red-400 flex items-center gap-1 px-2 py-1 rounded-lg hover:bg-red-50 dark:hover:bg-red-950/40 transition">
                                <i class="bi bi-trash"></i>
                                <span>Remove</span>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/'/g, '&#39;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function openUploadPhotoModal() {
        document.getElementById('uploadPhotoModal').style.display = 'flex';
        document.getElementById('uploadPhotoForm').reset();
        clearPhotoSelection();
    }

    function closeUploadPhotoModal() {
        document.getElementById('uploadPhotoModal').style.display = 'none';
    }

    function previewSelectedPhoto(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('photoPreviewImg').src = e.target.result;
                document.getElementById('photoPrompt').style.display = 'none';
                document.getElementById('photoPreviewContainer').style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }

    function clearPhotoSelection(e) {
        if (e) {
            e.stopPropagation();
            e.preventDefault();
        }
        document.getElementById('inputPhotoFile').value = '';
        document.getElementById('photoPreviewImg').src = '#';
        document.getElementById('photoPreviewContainer').style.display = 'none';
        document.getElementById('photoPrompt').style.display = 'block';
    }

    async function submitPortfolioPhoto(e) {
        e.preventDefault();
        const token = localStorage.getItem('khedma_token');
        if (!token) return;

        const fileInput = document.getElementById('inputPhotoFile');
        const urlInput = document.getElementById('inputPhotoUrl');
        const titleInput = document.getElementById('inputPhotoTitle');
        const captionInput = document.getElementById('inputPhotoCaption');

        const title = titleInput.value.trim();
        const caption = captionInput.value.trim();
        const photoUrl = urlInput.value.trim();

        if (!fileInput.files[0] && !photoUrl) {
            showAlert('error', 'Please select a photo file or enter an image URL.');
            return;
        }

        const formData = new FormData();
        if (fileInput.files[0]) {
            formData.append('photo', fileInput.files[0]);
        }
        if (photoUrl) {
            formData.append('photo_url', photoUrl);
        }
        formData.append('title', title);
        if (caption) {
            formData.append('caption', caption);
        }

        const btn = document.getElementById('btnSubmitPhoto');
        btn.disabled = true;
        btn.innerHTML = '<span class="inline-block animate-spin mr-1">⌛</span> Uploading...';

        try {
            const res = await fetch('/api/v1/provider/portfolio', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await res.json();
            if (res.ok) {
                currentPortfolio = data.portfolio_photos || [];
                renderPortfolioGrid();
                closeUploadPhotoModal();
                showAlert('success', 'Work photo published successfully!');
            } else {
                showAlert('error', data.message || 'Failed to upload photo.');
            }
        } catch (err) {
            console.error(err);
            showAlert('error', 'Network error occurred while uploading.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-cloud-arrow-up-fill text-sm"></i><span>Upload & Publish</span>';
        }
    }

    async function confirmDeletePhoto(id) {
        if (!confirm('Are you sure you want to remove this photo from your portfolio?')) {
            return;
        }

        const token = localStorage.getItem('khedma_token');
        if (!token) return;

        try {
            const res = await fetch('/api/v1/provider/portfolio/' + encodeURIComponent(id), {
                method: 'DELETE',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                }
            });

            const data = await res.json();
            if (res.ok) {
                currentPortfolio = data.portfolio_photos || [];
                renderPortfolioGrid();
                showAlert('success', 'Photo removed from portfolio.');
            } else {
                showAlert('error', data.message || 'Failed to delete photo.');
            }
        } catch (err) {
            console.error(err);
            showAlert('error', 'Network error occurred while removing photo.');
        }
    }

    function openProfileLightbox(url, title, caption) {
        document.getElementById('profileLightboxImg').src = url;
        document.getElementById('profileLightboxTitle').textContent = title || 'Work Photo';
        document.getElementById('profileLightboxCaption').textContent = caption || '';
        document.getElementById('profilePhotoLightbox').style.display = 'flex';
    }

    function closeProfileLightbox() {
        document.getElementById('profilePhotoLightbox').style.display = 'none';
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
