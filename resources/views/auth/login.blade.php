@extends('layouts.app')

@section('title', 'Sign In | Khedma')

@section('content')
<div class="py-12 px-4 sm:px-6 lg:px-8 max-w-md mx-auto">
    <div class="text-center mb-8">
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">
            Welcome Back
        </h1>
        <p class="mt-2 text-sm text-slate-600">
            Sign in to access your Khedma client or provider account.
        </p>
    </div>

    <div class="bg-white rounded-2xl shadow-xl shadow-slate-200/50 border border-slate-200 p-6 sm:p-8">
        <!-- Error alert -->
        <div id="errorAlert" class="hidden mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm">
            <span id="errorMessage">Invalid credentials.</span>
        </div>

        <form id="loginForm" onsubmit="handleLogin(event)" class="space-y-5">
            <div>
                <label for="email" class="block text-xs font-semibold text-slate-700 mb-1.5">
                    Email Address
                </label>
                <input type="email" id="email" name="email" required placeholder="name@example.com"
                    class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-xs font-semibold text-slate-700">
                        Password
                    </label>
                </div>
                <input type="password" id="password" name="password" required placeholder="••••••••"
                    class="w-full px-3.5 py-2.5 rounded-lg border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>

            <button type="submit" id="submitBtn"
                class="w-full py-3 px-4 rounded-xl text-white font-bold text-sm transition-all shadow-md flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 shadow-blue-500/25">
                <span id="btnText">Sign In</span>
                <svg id="btnSpinner" class="hidden w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
            </button>
        </form>

        <!-- Quick Demo Fill Section for Evaluators / Testers -->
        <div class="mt-8 pt-6 border-t border-slate-100">
            <span class="block text-center text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">
                Quick-Fill Demo Accounts
            </span>
            <div class="grid grid-cols-2 gap-2 text-xs">
                <button type="button" onclick="fillDemo('client@khedma.ma', 'Password123!')"
                    class="p-2.5 rounded-lg border border-blue-200 bg-blue-50/50 hover:bg-blue-100/50 text-blue-700 font-medium transition text-left">
                    <div class="font-bold">👤 Demo Client</div>
                    <div class="text-[10px] text-slate-500 truncate">client@khedma.ma</div>
                </button>
                <button type="button" onclick="fillDemo('provider@khedma.ma', 'Password123!')"
                    class="p-2.5 rounded-lg border border-amber-200 bg-amber-50/50 hover:bg-amber-100/50 text-amber-800 font-medium transition text-left">
                    <div class="font-bold">🛠️ Demo Provider</div>
                    <div class="text-[10px] text-slate-500 truncate">provider@khedma.ma</div>
                </button>
            </div>
        </div>

        <p class="text-center text-xs text-slate-500 mt-6">
            Don't have an account yet? 
            <a href="/register" class="font-bold text-blue-600 hover:text-blue-700 underline">Create one now</a>
        </p>
    </div>
</div>
@endsection

@section('scripts')
<script>
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
                window.location.href = '/dashboard';
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
