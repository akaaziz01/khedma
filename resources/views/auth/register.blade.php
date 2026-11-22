@extends('layouts.app')

@section('title', 'Create Account | Khedma')

@section('content')
<div class="py-12 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight sm:text-4xl">
            Join Khedma
        </h1>
        <p class="mt-2 text-base text-slate-600 dark:text-slate-400">
            Create an account to book top local pros or offer your services to thousands of clients across Tunisia.
        </p>
    </div>

    <!-- Main Card -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200 dark:border-slate-800 overflow-hidden transition-colors duration-200">
        <!-- Step 1: Role Selector -->
        <div class="p-6 sm:p-8 bg-slate-50/70 dark:bg-slate-800/40 border-b border-slate-200 dark:border-slate-800">
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-3">
                Step 1: Choose Your Account Type
            </label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Client Card -->
                <button type="button" id="roleClientBtn" onclick="selectRole('client')"
                    class="role-card relative text-left p-5 rounded-xl border-2 transition-all flex items-start gap-4 cursor-pointer bg-white dark:bg-slate-900 border-blue-600 ring-2 ring-blue-500/20 shadow-sm">
                    <div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center shrink-0">
                        <i class="bi bi-person-fill text-2xl"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-900 dark:text-white text-base">I am a Client</span>
                            <span id="clientBadge" class="text-[10px] font-bold uppercase tracking-wider bg-blue-600 text-white px-2 py-0.5 rounded-full">Selected</span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            I want to find, book, and hire verified local service providers for plumbing, repairs, cleaning, and more.
                        </p>
                    </div>
                </button>

                <!-- Provider Card -->
                <button type="button" id="roleProviderBtn" onclick="selectRole('provider')"
                    class="role-card relative text-left p-5 rounded-xl border-2 transition-all flex items-start gap-4 cursor-pointer bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700">
                    <div class="w-12 h-12 rounded-xl bg-amber-100 dark:bg-amber-950/60 text-amber-700 dark:text-amber-400 flex items-center justify-center shrink-0">
                        <i class="bi bi-tools text-2xl"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-900 dark:text-white text-base">I am a Provider</span>
                            <span id="providerBadge" class="hidden text-[10px] font-bold uppercase tracking-wider bg-amber-600 text-white px-2 py-0.5 rounded-full">Selected</span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                            I am a tradesperson, craftsman, or business owner offering professional services to grow my client base.
                        </p>
                    </div>
                </button>
            </div>
        </div>

        <!-- Alert messages -->
        <div id="errorAlert" class="hidden mx-6 sm:mx-8 mt-6 p-4 rounded-xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-900/40 text-red-800 dark:text-red-300 text-sm">
            <div class="font-semibold flex items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill text-red-600"></i>
                <span>Please fix the following issues:</span>
            </div>
            <ul id="errorList" class="mt-2 list-disc list-inside text-xs space-y-1"></ul>
        </div>

        <div id="successAlert" class="hidden mx-6 sm:mx-8 mt-6 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-900/40 text-emerald-800 dark:text-emerald-300 text-sm">
            <div class="font-semibold flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span id="successText">Account created successfully! Redirecting...</span>
            </div>
        </div>

        <!-- Step 2: Form -->
        <form id="registrationForm" onsubmit="handleRegistration(event)" class="p-6 sm:p-8 space-y-6">
            <input type="hidden" id="selectedRole" name="role" value="client">

            <div class="border-b border-slate-100 pb-2">
                <h2 class="text-lg font-bold text-slate-900" id="formHeaderTitle">Client Account Details</h2>
                <p class="text-xs text-slate-500" id="formHeaderSubtitle">Fill in your information to start booking services.</p>
            </div>

            <!-- Basic Info Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" required placeholder="e.g. Fatima Zahra"
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>

                <div>
                    <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email" name="email" required placeholder="name@example.com"
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Password (min. 8 characters) <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="password" name="password" required minlength="8" placeholder="••••••••"
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Confirm Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" placeholder="••••••••"
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="phone" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Phone Number <span id="phoneRequiredMark" class="text-slate-400 font-normal">(Optional)</span>
                    </label>
                    <input type="tel" id="phone" name="phone" placeholder="+216 20 000 000"
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>

                <div>
                    <label for="city" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        City / Governorate (Gouvernorat)
                    </label>
                    <select id="city" name="city"
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white transition">
                        <option value="">Select your city in Tunisia...</option>
                        <option value="Tunis">Tunis (تونس)</option>
                        <option value="Ariana">Ariana (أريانة)</option>
                        <option value="Ben Arous">Ben Arous (بن عروس)</option>
                        <option value="Manouba">Manouba (منوبة)</option>
                        <option value="La Marsa">La Marsa (المرسى)</option>
                        <option value="Sousse">Sousse (سوسة)</option>
                        <option value="Sfax">Sfax (صفاقس)</option>
                        <option value="Monastir">Monastir (المنستير)</option>
                        <option value="Nabeul">Nabeul / Hammamet (نابل)</option>
                        <option value="Bizerte">Bizerte (بنزرت)</option>
                        <option value="Kairouan">Kairouan (القيروان)</option>
                        <option value="Djerba">Djerba (جربة)</option>
                        <option value="Gabès">Gabès (قابس)</option>
                        <option value="Mahdia">Mahdia (المهدية)</option>
                    </select>
                </div>
            </div>

            <!-- Client Specific Fields -->
            <div id="clientFields" class="space-y-4">
                <div>
                    <label for="address" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Street Address (Optional)
                    </label>
                    <input type="text" id="address" name="address" placeholder="e.g. Résidence Les Pins, Les Berges du Lac 2"
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
                </div>
            </div>

            <!-- Provider Specific Fields -->
            <div id="providerFields" class="hidden space-y-5 pt-2 border-t border-dashed border-slate-200">
                <div class="bg-amber-50/50 p-4 rounded-xl border border-amber-200/60 mb-2">
                    <span class="text-xs font-bold text-amber-800 uppercase tracking-wider block mb-1">Provider Professional Profile</span>
                    <p class="text-xs text-amber-700">These details will be showcased to clients when they look for your services.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="category" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Primary Category <span class="text-red-500">*</span>
                        </label>
                        <select id="category" name="category"
                            class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent bg-white transition">
                            <option value="">Select your craft / trade...</option>
                            <option value="Plumbing">Plumbing (Plomberie)</option>
                            <option value="Electricity">Electricity (Électricité)</option>
                            <option value="HVAC & AC">Air Conditioning & HVAC (Climatisation)</option>
                            <option value="Carpentry">Carpentry & Woodwork (Menuiserie)</option>
                            <option value="Painting">Painting & Decor (Peinture)</option>
                            <option value="Cleaning">Home & Office Cleaning (Nettoyage)</option>
                            <option value="Appliance Repair">Home Appliance Repair (Électroménager)</option>
                            <option value="IT & Tech Support">IT & Network Support (Informatique)</option>
                        </select>
                    </div>

                    <div>
                        <label for="business_name" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Business / Trade Name (Optional)
                        </label>
                        <input type="text" id="business_name" name="business_name" placeholder="e.g. Express Plomberie Tunis"
                            class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="hourly_rate" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Estimated Base Rate (TND / hr)
                        </label>
                        <div class="relative">
                            <input type="number" id="hourly_rate" name="hourly_rate" min="0" step="5" placeholder="35"
                                class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent pr-14 transition">
                            <span class="absolute right-3.5 top-2.5 text-xs font-bold text-slate-400">TND</span>
                        </div>
                    </div>

                    <div>
                        <label for="skillsInput" class="block text-xs font-semibold text-slate-700 mb-1.5">
                            Key Skills (comma separated)
                        </label>
                        <input type="text" id="skillsInput" placeholder="leak repair, pipe fitting, water heaters"
                            class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition">
                    </div>
                </div>

                <div>
                    <label for="bio" class="block text-xs font-semibold text-slate-700 mb-1.5">
                        Bio & Experience Summary
                    </label>
                    <textarea id="bio" name="bio" rows="3" placeholder="Briefly describe your experience, certifications, and what you offer to clients..."
                        class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition"></textarea>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4">
                <button type="submit" id="submitBtn"
                    class="w-full py-3.5 px-4 rounded-xl text-white font-bold text-base transition-all shadow-md flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 shadow-blue-500/25">
                    <span id="btnText">Create Client Account</span>
                    <svg id="btnSpinner" class="hidden w-5 h-5 animate-spin text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                </button>
            </div>

            <p class="text-center text-xs text-slate-500 pt-2">
                Already have an account? 
                <a href="/login" class="font-bold text-blue-600 hover:text-blue-700 underline">Sign In here</a>
            </p>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let currentRole = 'client';

    function selectRole(role) {
        currentRole = role;
        document.getElementById('selectedRole').value = role;

        const clientBtn = document.getElementById('roleClientBtn');
        const providerBtn = document.getElementById('roleProviderBtn');
        const clientBadge = document.getElementById('clientBadge');
        const providerBadge = document.getElementById('providerBadge');
        const clientFields = document.getElementById('clientFields');
        const providerFields = document.getElementById('providerFields');
        const phoneRequiredMark = document.getElementById('phoneRequiredMark');
        const formHeaderTitle = document.getElementById('formHeaderTitle');
        const formHeaderSubtitle = document.getElementById('formHeaderSubtitle');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const phoneInput = document.getElementById('phone');
        const categoryInput = document.getElementById('category');

        if (role === 'client') {
            clientBtn.className = 'role-card relative text-left p-5 rounded-xl border-2 transition-all flex items-start gap-4 cursor-pointer bg-white border-blue-600 ring-2 ring-blue-500/20 shadow-sm';
            providerBtn.className = 'role-card relative text-left p-5 rounded-xl border-2 transition-all flex items-start gap-4 cursor-pointer bg-white border-slate-200 hover:border-slate-300';
            clientBadge.classList.remove('hidden');
            providerBadge.classList.add('hidden');
            clientFields.classList.remove('hidden');
            providerFields.classList.add('hidden');
            phoneRequiredMark.textContent = '(Optional)';
            phoneInput.required = false;
            categoryInput.required = false;

            formHeaderTitle.textContent = 'Client Account Details';
            formHeaderSubtitle.textContent = 'Fill in your information to start booking services.';
            submitBtn.className = 'w-full py-3.5 px-4 rounded-xl text-white font-bold text-base transition-all shadow-md flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 shadow-blue-500/25';
            btnText.textContent = 'Create Client Account';
        } else {
            providerBtn.className = 'role-card relative text-left p-5 rounded-xl border-2 transition-all flex items-start gap-4 cursor-pointer bg-white border-amber-600 ring-2 ring-amber-500/20 shadow-sm';
            clientBtn.className = 'role-card relative text-left p-5 rounded-xl border-2 transition-all flex items-start gap-4 cursor-pointer bg-white border-slate-200 hover:border-slate-300';
            providerBadge.classList.remove('hidden');
            clientBadge.classList.add('hidden');
            clientFields.classList.add('hidden');
            providerFields.classList.remove('hidden');
            phoneRequiredMark.textContent = '*';
            phoneRequiredMark.className = 'text-red-500 font-semibold';
            phoneInput.required = true;
            categoryInput.required = true;

            formHeaderTitle.textContent = 'Provider Account Details';
            formHeaderSubtitle.textContent = 'Provide your business & service information to start receiving job leads.';
            submitBtn.className = 'w-full py-3.5 px-4 rounded-xl text-white font-bold text-base transition-all shadow-md flex items-center justify-center gap-2 bg-amber-600 hover:bg-amber-700 shadow-amber-500/25';
            btnText.textContent = 'Create Provider Account';
        }
    }

    // Auto-select from URL parameter ?role=provider
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const role = urlParams.get('role');
        if (role === 'provider') {
            selectRole('provider');
        }
    });

    async function handleRegistration(e) {
        e.preventDefault();
        const submitBtn = document.getElementById('submitBtn');
        const btnSpinner = document.getElementById('btnSpinner');
        const btnText = document.getElementById('btnText');
        const errorAlert = document.getElementById('errorAlert');
        const errorList = document.getElementById('errorList');
        const successAlert = document.getElementById('successAlert');

        errorAlert.classList.add('hidden');
        errorList.innerHTML = '';
        successAlert.classList.add('hidden');

        // Prepare payload
        const password = document.getElementById('password').value;
        const passwordConfirm = document.getElementById('password_confirmation').value;

        if (password !== passwordConfirm) {
            errorList.innerHTML = '<li>Password confirmation does not match.</li>';
            errorAlert.classList.remove('hidden');
            return;
        }

        const payload = {
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            password: password,
            password_confirmation: passwordConfirm,
            phone: document.getElementById('phone').value || null,
            city: document.getElementById('city').value || null,
        };

        let endpoint = '/api/v1/auth/register/client';

        if (currentRole === 'client') {
            payload.address = document.getElementById('address').value || null;
        } else {
            endpoint = '/api/v1/auth/register/provider';
            payload.category = document.getElementById('category').value;
            payload.business_name = document.getElementById('business_name').value || null;
            payload.bio = document.getElementById('bio').value || null;
            payload.hourly_rate = document.getElementById('hourly_rate').value ? parseFloat(document.getElementById('hourly_rate').value) : null;
            
            const skillsRaw = document.getElementById('skillsInput').value;
            payload.skills = skillsRaw ? skillsRaw.split(',').map(s => s.trim()).filter(s => s.length > 0) : [];
        }

        // Set UI loading
        submitBtn.disabled = true;
        btnSpinner.classList.remove('hidden');
        btnText.textContent = 'Creating account...';

        try {
            const res = await fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(payload)
            });

            const data = await res.json();

            if (res.ok && data.access_token) {
                // Save token & user in localStorage
                localStorage.setItem('khedma_token', data.access_token);
                localStorage.setItem('khedma_user', JSON.stringify(data.user));

                // Also establish session via web bridge if needed
                document.getElementById('successText').textContent = `${data.message} Returning you to Khedma...`;
                successAlert.classList.remove('hidden');

                setTimeout(() => {
                    window.location.href = getSafePostAuthRedirect();
                }, 1000);
            } else {
                // Show errors
                if (data.errors) {
                    for (const [field, messages] of Object.entries(data.errors)) {
                        messages.forEach(msg => {
                            const li = document.createElement('li');
                            li.textContent = msg;
                            errorList.appendChild(li);
                        });
                    }
                } else {
                    const li = document.createElement('li');
                    li.textContent = data.message || 'Registration failed. Please check your inputs.';
                    errorList.appendChild(li);
                }
                errorAlert.classList.remove('hidden');
            }
        } catch (err) {
            const li = document.createElement('li');
            li.textContent = 'Network or server error occurred. Please try again.';
            errorList.appendChild(li);
            errorAlert.classList.remove('hidden');
        } finally {
            submitBtn.disabled = false;
            btnSpinner.classList.add('hidden');
            btnText.textContent = currentRole === 'client' ? 'Create Client Account' : 'Create Provider Account';
        }
    }
</script>
@endsection
