@extends('layouts.app')

@section('title', 'Sign In | Khedma')

@section('content')
<div class="py-12 px-4 sm:px-6 lg:px-8 max-w-md mx-auto">
    <!-- Admin Security Notice if visiting admin portal -->
    <div id="adminPortalBadge" class="hidden mb-6 p-3 rounded-2xl bg-slate-900 text-white text-xs flex items-center justify-between border border-slate-700 shadow-md">
        <div class="flex items-center gap-2">
            <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
            <span class="font-bold">Admin Portal Access</span>
        </div>
        <span class="text-[10px] text-slate-400">Authorized personnel only</span>
    </div>

    <div class="text-center mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight" id="loginHeading">
            Welcome Back
        </h1>
        <p class="mt-2 text-sm text-slate-600 dark:text-slate-400" id="loginSub">
            Sign in to access your Khedma client, provider, or administrator account.
        </p>
    </div>

    <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200 dark:border-slate-800 p-6 sm:p-8 transition-colors duration-200">
        <!-- Error alert -->
        <div id="errorAlert" class="hidden mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-900/40 text-red-800 dark:text-red-300 text-sm">
            <span id="errorMessage">Invalid credentials.</span>
        </div>

        <form id="loginForm" onsubmit="handleLogin(event)" class="space-y-5">
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5">
                    Email Address
                </label>
                <input type="email" id="email" name="email" required placeholder="name@example.com"
                    class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-xs font-semibold text-slate-700 dark:text-slate-300">
                        Password
                    </label>
                    <a href="/forgot-password" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700">
                        Forgot password?
                    </a>
                </div>
                <input type="password" id="password" name="password" required placeholder="••••••••"
                    class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>

            <button type="submit" id="submitBtn"
                class="w-full py-3 px-4 rounded-xl text-white font-bold text-sm transition-all shadow-md flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 shadow-blue-500/25">
                <span id="btnText">Sign In</span>
                <i id="btnSpinner" class="bi bi-arrow-repeat animate-spin hidden text-base"></i>
            </button>
        </form>

        <!-- Quick Demo Fill Section for Evaluators / Testers -->
        <div class="mt-8 pt-6 border-t border-slate-100 dark:border-slate-800">
            <span class="block text-center text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-3">
                Quick-Fill Demo Accounts
            </span>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs">
                <!-- Client Demo -->
                <button type="button" onclick="fillDemo('client@khedma.tn', 'Password123!')"
                    class="p-2 rounded-xl border border-blue-200 dark:border-blue-900/40 bg-blue-50/50 dark:bg-blue-950/30 hover:bg-blue-100/50 dark:hover:bg-blue-900/40 text-blue-700 dark:text-blue-300 font-medium transition text-left">
                    <div class="font-bold text-[11px] flex items-center gap-1"><i class="bi bi-person-fill"></i> Client</div>
                    <div class="text-[9px] text-slate-500 dark:text-slate-400 truncate">client@khedma.tn</div>
                </button>
                <!-- Provider Demo -->
                <button type="button" onclick="fillDemo('provider@khedma.tn', 'Password123!')"
                    class="p-2 rounded-xl border border-amber-200 dark:border-amber-900/40 bg-amber-50/50 dark:bg-amber-950/30 hover:bg-amber-100/50 dark:hover:bg-amber-900/40 text-amber-800 dark:text-amber-300 font-medium transition text-left">
                    <div class="font-bold text-[11px] flex items-center gap-1"><i class="bi bi-tools"></i> Provider</div>
                    <div class="text-[9px] text-slate-500 dark:text-slate-400 truncate">provider@khedma.tn</div>
                </button>
                <!-- Admin Demo -->
                <button type="button" onclick="fillDemo('admin@khedma.tn', 'AdminPass123!')"
                    class="p-2 rounded-xl border border-red-200 dark:border-red-900/40 bg-red-50/50 dark:bg-red-950/30 hover:bg-red-100/50 dark:hover:bg-red-900/40 text-red-800 dark:text-red-300 font-medium transition text-left">
                    <div class="font-bold text-[11px] flex items-center gap-1"><i class="bi bi-shield-shaded"></i> Admin</div>
                    <div class="text-[9px] text-slate-500 dark:text-slate-400 truncate">admin@khedma.tn</div>
                </button>
            </div>
        </div>
        
        <p class="text-center text-xs text-slate-500 dark:text-slate-400 mt-6">
            Don't have an account yet? 
            <a href="/register" class="font-bold text-blue-600 dark:text-blue-400 hover:text-blue-700 underline">Create one now</a>
        </p>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const isAdminPath = window.location.pathname.includes('/admin') || window.location.search.includes('admin=1');
        if (isAdminPath) {
            document.getElementById('adminPortalBadge').classList.remove('hidden');
            document.getElementById('loginHeading').textContent = 'Admin Portal Access';
            document.getElementById('loginSub').textContent = 'Secure administration area for authorized platform supervisors.';
            fillDemo('admin@khedma.tn', 'AdminPass123!');
        }
    });

    function fillDemo(email, password) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = password;
    }

    async function handleLogin(e) {
        e.preventDefault();
        const submitBtn = document.getElementById('submitBtn');
        const btnSpinner = document.getElementById('btnSpinner');
        const btnText = document.getElementById('btnText');
        const errorAlert = document.getElementById('errorAlert');
        const errorMessage = document.getElementById('errorMessage');

        errorAlert.classList.add('hidden');
        submitBtn.disabled = true;
        btnSpinner.classList.remove('hidden');
        btnText.textContent = 'Signing in...';

        try {
            const res = await fetch('/api/v1/auth/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    email: document.getElementById('email').value,
                    password: document.getElementById('password').value
                })
            });

            const data = await res.json();

            if (res.ok && data.access_token) {
                localStorage.setItem('khedma_token', data.access_token);
                localStorage.setItem('khedma_user', JSON.stringify(data.user));

                // If user is Admin, redirect directly to Admin Area!
                if (data.user.role === 'admin') {
                    window.location.href = '/admin/dashboard';
                } else {
                    const urlParams = new URLSearchParams(window.location.search);
                    const redirectUrl = urlParams.get('redirect');
                    if (redirectUrl && redirectUrl.startsWith('/')) {
                        window.location.href = redirectUrl;
                    } else {
                        window.location.href = '/dashboard';
                    }
                }
            } else {
                errorMessage.textContent = data.message || 'Invalid email or password.';
                errorAlert.classList.remove('hidden');
            }
        } catch (err) {
            errorMessage.textContent = 'An error occurred. Please verify your connection.';
            errorAlert.classList.remove('hidden');
        } finally {
            submitBtn.disabled = false;
            btnSpinner.classList.add('hidden');
            btnText.textContent = 'Sign In';
        }
    }
</script>
@endsection
