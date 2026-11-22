@extends('layouts.app')

@section('title', 'Report an Issue | Khedma')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
    <div class="mb-6">
        <p class="text-xs font-extrabold uppercase tracking-wider text-blue-600 dark:text-blue-400">KHEDMA SUPPORT</p>
        <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-900 dark:text-white">Report an issue</h1>
        <p class="mt-2 text-sm text-slate-500 dark:text-slate-400">Tell our team what happened. Reports are reviewed privately by the Khedma team.</p>
    </div>

    <form id="reportForm" class="space-y-5 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900 sm:p-8">
        <div id="reportMessage" class="hidden rounded-xl p-4 text-sm" role="status" aria-live="polite"></div>

        <div>
            <label for="reportType" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">What are you reporting?</label>
            <select id="reportType" name="type" required class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                <option value="issue">A platform issue</option>
                <option id="clientReportOption" value="client" hidden>A client I worked with</option>
            </select>
        </div>

        <div id="bookingField" class="hidden">
            <label for="reportBooking" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">Related booking</label>
            <select id="reportBooking" name="booking_id" class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
                <option value="">Choose a booking</option>
            </select>
            <p id="bookingHelp" class="mt-1 text-xs text-slate-500 dark:text-slate-400">Client reports are limited to your own bookings.</p>
        </div>

        <div>
            <label for="reportSubject" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">Subject</label>
            <input id="reportSubject" name="subject" type="text" minlength="3" maxlength="160" required placeholder="Briefly summarize the issue"
                class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white">
        </div>

        <div>
            <label for="reportDetails" class="mb-1.5 block text-sm font-bold text-slate-700 dark:text-slate-200">Details</label>
            <textarea id="reportDetails" name="details" rows="6" minlength="10" maxlength="2000" required placeholder="Include what happened and any steps that will help us investigate."
                class="w-full resize-y rounded-xl border border-slate-300 bg-white px-3.5 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 dark:border-slate-700 dark:bg-slate-800 dark:text-white"></textarea>
            <p class="mt-1 text-right text-xs text-slate-400">10–2000 characters</p>
        </div>

        <div class="flex items-center justify-between border-t border-slate-100 pt-5 dark:border-slate-800">
            <a href="/dashboard" class="text-sm font-bold text-slate-500 hover:text-slate-800 dark:hover:text-white">Back to dashboard</a>
            <button id="submitReportButton" type="submit" class="rounded-xl bg-blue-600 px-5 py-3 text-sm font-extrabold text-white shadow-sm transition hover:bg-blue-700 disabled:cursor-not-allowed disabled:opacity-60">
                Submit report
            </button>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', initializeReportForm);

    function initializeReportForm() {
        const token = localStorage.getItem('khedma_token');
        const user = JSON.parse(localStorage.getItem('khedma_user') || '{}');
        if (!token || !user.id) {
            window.location.href = '/login?redirect=' + encodeURIComponent(window.location.pathname + window.location.search);
            return;
        }

        const clientOption = document.getElementById('clientReportOption');
        const typeSelect = document.getElementById('reportType');
        const requestedType = new URLSearchParams(window.location.search).get('type');
        if (user.role === 'provider') {
            clientOption.hidden = false;
            if (requestedType === 'client') typeSelect.value = 'client';
        }

        typeSelect.addEventListener('change', () => updateReportType(token));
        document.getElementById('reportForm').addEventListener('submit', submitReport);
        updateReportType(token);
    }

    async function updateReportType(token) {
        const isClientReport = document.getElementById('reportType').value === 'client';
        const bookingField = document.getElementById('bookingField');
        bookingField.classList.toggle('hidden', !isClientReport);
        if (!isClientReport || bookingField.dataset.loaded === 'true') return;

        const bookingSelect = document.getElementById('reportBooking');
        const help = document.getElementById('bookingHelp');
        try {
            const response = await fetch('/api/v1/provider/dashboard', {
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
            });
            if (!response.ok) throw new Error('Could not load your bookings.');

            const data = await response.json();
            const bookings = data.bookings || [];
            bookings.forEach(booking => {
                const option = document.createElement('option');
                option.value = booking.id;
                option.textContent = `#${booking.booking_number || booking.id} — ${booking.client?.name || 'Client'} — ${booking.service_title || 'Service'}`;
                bookingSelect.appendChild(option);
            });

            const requestedBooking = new URLSearchParams(window.location.search).get('booking_id');
            if (requestedBooking) bookingSelect.value = requestedBooking;
            if (bookings.length === 0) help.textContent = 'You need a booking with a client before you can report them.';
            bookingField.dataset.loaded = 'true';
        } catch (error) {
            help.textContent = error.message || 'Could not load your bookings. Please refresh and try again.';
        }
    }

    async function submitReport(event) {
        event.preventDefault();
        const form = event.currentTarget;
        const token = localStorage.getItem('khedma_token');
        const type = document.getElementById('reportType').value;
        const payload = {
            type,
            subject: document.getElementById('reportSubject').value.trim(),
            details: document.getElementById('reportDetails').value.trim()
        };
        if (type === 'client') {
            payload.booking_id = document.getElementById('reportBooking').value;
            if (!payload.booking_id) {
                showReportMessage('Choose the booking related to this client report.', false);
                return;
            }
        }

        const button = document.getElementById('submitReportButton');
        button.disabled = true;
        button.textContent = 'Submitting...';
        try {
            const response = await fetch('/api/v1/reports', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(payload)
            });
            const data = await response.json();
            if (!response.ok) {
                const firstError = Object.values(data.errors || {})[0]?.[0];
                throw new Error(firstError || data.message || 'Could not submit your report.');
            }

            form.reset();
            document.getElementById('reportType').value = 'issue';
            updateReportType(token);
            showReportMessage(data.message, true);
        } catch (error) {
            showReportMessage(error.message || 'Network error. Please try again.', false);
        } finally {
            button.disabled = false;
            button.textContent = 'Submit report';
        }
    }

    function showReportMessage(message, success) {
        const element = document.getElementById('reportMessage');
        element.className = success
            ? 'rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-semibold text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-300'
            : 'rounded-xl border border-red-200 bg-red-50 p-4 text-sm font-semibold text-red-800 dark:border-red-900 dark:bg-red-950/40 dark:text-red-300';
        element.textContent = message;
        element.classList.remove('hidden');
    }
</script>
@endsection
