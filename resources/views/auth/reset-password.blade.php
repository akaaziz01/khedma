@extends('layouts.app')

@section('title', 'Set New Password | Khedma')

@section('content')
<div class="py-16 px-4 sm:px-6 lg:px-8 max-w-md mx-auto">
    <div class="text-center mb-8">
        <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center mx-auto mb-4 font-bold">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
            Set New Password
        </h1>
        <p class="mt-2 text-xs text-slate-500">
            Please enter your new password to secure and restore access to your account.
        </p>
    </div>

    <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200 p-6 sm:p-8 space-y-5">
        <!-- Error alert -->
        <div id="errorAlert" class="hidden p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs">
            <span id="errorMessage">Invalid or expired reset token.</span>
        </div>

        <!-- Success alert -->
        <div id="successAlert" class="hidden p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs space-y-2">
            <div class="font-bold flex items-center gap-1.5">
                <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>Password Successfully Updated!</span>
            </div>
            <p>Your password has been changed. Redirecting to your account...</p>
        </div>

        <form id="resetForm" onsubmit="handleResetPassword(event)" class="space-y-4">
            <input type="hidden" id="resetToken" name="token" value="">

            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">
                    Account Email Address
                </label>
                <input type="email" id="email" name="email" required placeholder="client@khedma.tn"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 mb-1.5">
                    New Password (min. 8 characters)
                </label>
                <input type="password" id="password" name="password" required minlength="8" placeholder="••••••••"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1.5">
                    Confirm New Password
                </label>
                <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" placeholder="••••••••"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition">
            </div>

            <button type="submit" id="submitBtn"
                class="w-full py-3.5 px-4 rounded-xl text-white font-extrabold text-xs transition-all shadow-md flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 shadow-blue-500/20">
                <span id="btnText">Update & Save New Password</span>
                <svg id="btnSpinner" class="hidden w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
            </button>
        </form>

        <div class="pt-4 border-t border-slate-100 text-center">
            <a href="/login" class="text-xs font-bold text-slate-600 hover:text-slate-900 transition flex items-center justify-center gap-1">
                <span>←</span> Back to Sign In
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const token = urlParams.get('token');
        const email = urlParams.get('email');

        if (token) {
            document.getElementById('resetToken').value = token;
        }
        if (email) {
            document.getElementById('email').value = email;
        }
    });

    async function handleResetPassword(e) {
        e.preventDefault();
        const submitBtn = document.getElementById('submitBtn');
        const btnSpinner = document.getElementById('btnSpinner');
        const btnText = document.getElementById('btnText');
        const errorAlert = document.getElementById('errorAlert');
        const errorMessage = document.getElementById('errorMessage');
        const successAlert = document.getElementById('successAlert');

        errorAlert.classList.add('hidden');
        successAlert.classList.add('hidden');

        const token = document.getElementById('resetToken').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const passwordConfirm = document.getElementById('password_confirmation').value;

        if (!token) {
            errorMessage.textContent = 'Missing reset token. Please request a new password reset link.';
            errorAlert.classList.remove('hidden');
            return;
        }

        if (password !== passwordConfirm) {
            errorMessage.textContent = 'Passwords do not match.';
            errorAlert.classList.remove('hidden');
            return;
        }

        submitBtn.disabled = true;
        btnSpinner.classList.remove('hidden');
        btnText.textContent = 'Updating password...';

        try {
            const res = await fetch('/api/v1/auth/reset-password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    token: token,
                    email: email,
                    password: password,
                    password_confirmation: passwordConfirm
                })
            });

            const data = await res.json();

            if (res.ok) {
                successAlert.classList.remove('hidden');
                if (data.access_token) {
                    localStorage.setItem('khedma_token', data.access_token);
                    localStorage.setItem('khedma_user', JSON.stringify(data.user));
                }
                setTimeout(() => {
                    window.location.href = '/dashboard';
                }, 1500);
            } else {
                errorMessage.textContent = data.errors?.password ? data.errors.password[0] : (data.message || 'Failed to reset password.');
                errorAlert.classList.remove('hidden');
            }
        } catch (err) {
            errorMessage.textContent = 'Network error. Please try again.';
            errorAlert.classList.remove('hidden');
        } finally {
            submitBtn.disabled = false;
            btnSpinner.classList.add('hidden');
            btnText.textContent = 'Update & Save New Password';
        }
    }
</script>
@endsection
