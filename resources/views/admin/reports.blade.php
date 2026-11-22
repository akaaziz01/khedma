@extends('layouts.admin')

@section('title', 'Reports & Issues | KHEDMA Admin')

@section('content')
<div class="mx-auto max-w-7xl space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-extrabold uppercase tracking-wider text-red-600 dark:text-red-400">Platform moderation</p>
            <h1 class="mt-1 text-2xl font-black tracking-tight text-slate-900 dark:text-white">Reports & Issues</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">Review client reports and platform issue submissions.</p>
        </div>
        <a href="/admin/dashboard" class="w-fit rounded-xl border border-slate-200 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 dark:border-slate-700 dark:text-slate-300 dark:hover:bg-slate-800">Back to overview</a>
    </div>

    <div id="reportsLoading" class="rounded-2xl border border-slate-200 bg-white py-14 text-center text-sm text-slate-500 dark:border-slate-800 dark:bg-slate-900">
        Loading reports...
    </div>
    <div id="reportsUnauthorized" class="hidden rounded-2xl border border-red-200 bg-red-50 p-8 text-center text-sm font-semibold text-red-700 dark:border-red-900 dark:bg-red-950/30 dark:text-red-300">
        You need administrator access to review reports.
        <a href="/login?admin=1" class="ml-1 underline">Sign in as an administrator</a>
    </div>
    <div id="reportsError" class="hidden rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-900 dark:bg-red-950/30 dark:text-red-300" role="alert"></div>

    <div id="reportsContent" class="hidden overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[980px] text-left text-sm">
                <thead class="border-b border-slate-200 bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-500 dark:border-slate-800 dark:bg-slate-800/60 dark:text-slate-400">
                    <tr>
                        <th class="px-5 py-3">Report</th>
                        <th class="px-5 py-3">Reporter / Client</th>
                        <th class="px-5 py-3">Details</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3">Admin notes</th>
                    </tr>
                </thead>
                <tbody id="reportsTableBody" class="divide-y divide-slate-100 text-slate-700 dark:divide-slate-800 dark:text-slate-200"></tbody>
            </table>
        </div>
        <p id="reportsEmpty" class="hidden p-10 text-center text-sm text-slate-500 dark:text-slate-400">There are no reports yet.</p>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const reportStatuses = [
        ['open', 'Open'],
        ['in_review', 'In review'],
        ['resolved', 'Resolved'],
        ['dismissed', 'Dismissed']
    ];

    document.addEventListener('DOMContentLoaded', loadReports);

    async function loadReports() {
        const token = localStorage.getItem('khedma_token');
        const user = JSON.parse(localStorage.getItem('khedma_user') || '{}');
        if (!token || user.role !== 'admin') {
            document.getElementById('reportsLoading').classList.add('hidden');
            document.getElementById('reportsUnauthorized').classList.remove('hidden');
            return;
        }

        try {
            const response = await fetch('/api/v1/admin/reports', {
                headers: { 'Authorization': 'Bearer ' + token, 'Accept': 'application/json' }
            });
            if (response.status === 401 || response.status === 403) {
                document.getElementById('reportsLoading').classList.add('hidden');
                document.getElementById('reportsUnauthorized').classList.remove('hidden');
                return;
            }
            if (!response.ok) throw new Error('Could not load reports.');

            const data = await response.json();
            renderReports(data.data || []);
            document.getElementById('reportsLoading').classList.add('hidden');
            document.getElementById('reportsContent').classList.remove('hidden');
        } catch (error) {
            document.getElementById('reportsLoading').classList.add('hidden');
            const errorElement = document.getElementById('reportsError');
            errorElement.textContent = error.message || 'Could not load reports.';
            errorElement.classList.remove('hidden');
        }
    }

    function renderReports(reports) {
        const body = document.getElementById('reportsTableBody');
        body.replaceChildren();
        document.getElementById('reportsEmpty').classList.toggle('hidden', reports.length > 0);

        reports.forEach(report => {
            const row = document.createElement('tr');
            row.className = 'align-top';
            row.append(
                createCell(`#${report.id} · ${report.type === 'client' ? 'Client report' : 'Platform issue'}\n${report.subject}`),
                createCell(`By: ${report.reporter?.name || 'Deleted account'}\nClient: ${report.reported_client?.name || '—'}${report.booking?.booking_number ? `\nBooking: #${report.booking.booking_number}` : ''}`),
                createCell(report.details),
                createStatusCell(report.status),
                createNotesCell(report.admin_notes || '')
            );
            body.appendChild(row);
        });
    }

    function createCell(text) {
        const cell = document.createElement('td');
        cell.className = 'whitespace-pre-line px-5 py-4 text-xs leading-relaxed';
        cell.textContent = text;
        return cell;
    }

    function createStatusCell(status) {
        const cell = document.createElement('td');
        cell.className = 'px-5 py-4';
        const select = document.createElement('select');
        select.className = 'rounded-lg border border-slate-300 bg-white px-2 py-2 text-xs dark:border-slate-700 dark:bg-slate-800';
        reportStatuses.forEach(([value, label]) => {
            const option = document.createElement('option');
            option.value = value;
            option.textContent = label;
            select.appendChild(option);
        });
        select.value = status;
        cell.appendChild(select);
        return cell;
    }

    function createNotesCell(notes) {
        const cell = document.createElement('td');
        cell.className = 'px-5 py-4';
        const textarea = document.createElement('textarea');
        textarea.rows = 3;
        textarea.maxLength = 2000;
        textarea.placeholder = 'Internal notes for the review team';
        textarea.className = 'w-56 rounded-lg border border-slate-300 bg-white px-2.5 py-2 text-xs dark:border-slate-700 dark:bg-slate-800';
        textarea.value = notes;

        const button = document.createElement('button');
        button.type = 'button';
        button.textContent = 'Save';
        button.className = 'mt-2 block rounded-lg bg-slate-900 px-3 py-2 text-xs font-bold text-white hover:bg-slate-700 dark:bg-blue-600 dark:hover:bg-blue-500';
        button.addEventListener('click', () => updateReport(button));

        cell.append(textarea, button);
        return cell;
    }

    async function updateReport(button) {
        const row = button.closest('tr');
        const reportId = row.cells[0].textContent.match(/^#(\d+)/)?.[1];
        const status = row.cells[3].querySelector('select').value;
        const adminNotes = row.cells[4].querySelector('textarea').value.trim();
        const token = localStorage.getItem('khedma_token');
        button.disabled = true;
        button.textContent = 'Saving...';
        try {
            const response = await fetch(`/api/v1/admin/reports/${reportId}`, {
                method: 'PATCH',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ status, admin_notes: adminNotes })
            });
            const data = await response.json();
            if (!response.ok) throw new Error(data.message || 'Could not update this report.');
            button.textContent = 'Saved';
        } catch (error) {
            button.textContent = error.message || 'Save failed';
        } finally {
            button.disabled = false;
            setTimeout(() => { button.textContent = 'Save'; }, 2500);
        }
    }
</script>
@endsection
