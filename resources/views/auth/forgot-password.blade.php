@extends('layouts.app')

@section('title', 'Forgot Password | Khedma')

@section('content')
<div class="py-16 px-4 sm:px-6 lg:px-8 max-w-md mx-auto">
    <div class="text-center mb-8">
        <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center mx-auto mb-4 font-bold">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
        </div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
            Reset Your Password
        </h1>
        <p class="mt-2 text-xs text-slate-500 max-w-sm mx-auto">
            Enter the email associated with your client or provider account, and we'll help you securely reset your credentials.
        </p>
    </div>

    <div class="bg-white rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200 p-6 sm:p-8 space-y-5">
        <!-- Error alert -->
        <div id="errorAlert" class="hidden p-4 rounded-2xl bg-red-50 border border-red-200 text-red-800 text-xs">
            <span id="errorMessage">Could not find account.</span>
        </div>

        <!-- Success alert -->
        <div id="successAlert" class="hidden p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-900 text-xs space-y-3">
            <div class="flex items-center gap-2 font-bold">
                <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                <span>Password reset token generated!</span>
            </div>
            <p id="successMessage" class="text-emerald-800"></p>
            <div class="pt-2 border-t border-emerald-200">
                <a id="directResetLink" href="#" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition shadow-xs">
                    <span>Click here to set new password →</span>
                </a>
            </div>
        </div>

        <form id="forgotForm" onsubmit="handleForgotPassword(event)" class="space-y-4">
            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">
                    Account Email Address
                </label>
                <input type="email" id="email" name="email" required placeholder="client@khedma.ma"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 focus:border-transparent transition">
            </div>

            <button type="submit" id="submitBtn"
                class="w-full py-3 px-4 rounded-xl text-white font-extrabold text-xs transition-all shadow-md flex items-center justify-center gap-2 bg-amber-600 hover:bg-amber-700 shadow-amber-600/20">
                <span id="btnText">Send Password Reset Link</span>
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
    async function handleForgotPassword(e) {
        e.preventDefault();
        const email = document.getElementById('email').value;
        const submitBtn = document.getElementById('submitBtn');
        const btnSpinner = document.getElementById('btnSpinner');
        const btnText = document.getElementById('btnText');
        const errorAlert = document.getElementById('errorAlert');
        const errorMessage = document.getElementById('errorMessage');
        const successAlert = document.getElementById('successAlert');
        const successMessage = document.getElementById('successMessage');
        const directResetLink = document.getElementById('directResetLink');

        errorAlert.classList.add('hidden');
        successAlert.classList.add('hidden');
        submitBtn.disabled = true;
        btnSpinner.classList.remove('hidden');
        btnText.textContent = 'Generating reset token...';

        try {
            const res = await fetch('/api/v1/auth/forgot-password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ email })
            });

            const data = await res.json();

            if (res.ok) {
                successMessage.textContent = data.message;
                directResetLink.href = data.reset_url;
                successAlert.classList.remove('hidden');
            } else {
                errorMessage.textContent = data.errors?.email ? data.errors.email[0] : (data.message || 'Error requesting reset.');
                errorAlert.classList.remove('hidden');
            }
        } catch (err) {
            errorMessage.textContent = 'Network error. Please try again.';
            errorAlert.classList.remove('hidden');
        } finally {
            submitBtn.disabled = false;
            btnSpinner.classList.add('hidden');
            btnText.textContent = 'Send Password Reset Link';
        }
    }
</script>
@endsection
