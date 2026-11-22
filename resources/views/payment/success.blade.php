@extends('layouts.app')

@section('title', 'Paiement Réussi - KHEDMA')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white dark:bg-slate-900 rounded-3xl p-8 border border-slate-200/80 dark:border-slate-800 shadow-xl text-center space-y-6">
        
        <!-- Animated Success Icon -->
        <div class="w-20 h-20 bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 rounded-full flex items-center justify-center mx-auto text-4xl shadow-inner animate-bounce">
            <i class="bi bi-check-circle-fill"></i>
        </div>

        <div>
            <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-emerald-50 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-900/40">
                Paiement Confirmé
            </span>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white mt-3">
                Merci ! Votre paiement a été validé
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                Votre transaction Stripe a été traitée avec succès. La réservation est désormais confirmée auprès de votre prestataire.
            </p>
        </div>

        <!-- Booking Details Card -->
        <div id="paymentDetailsCard" class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 text-left space-y-2.5">
            <div class="flex justify-between items-center text-xs">
                <span class="text-slate-400 font-semibold">Réservation N°</span>
                <span id="bookingNumber" class="font-mono font-bold text-slate-900 dark:text-white">Chargement...</span>
            </div>
            <div class="flex justify-between items-center text-xs">
                <span class="text-slate-400 font-semibold">Montant payé</span>
                <span id="bookingAmount" class="font-bold text-emerald-600 dark:text-emerald-400 text-sm">--</span>
            </div>
            <div class="flex justify-between items-center text-xs">
                <span class="text-slate-400 font-semibold">Moyen de paiement</span>
                <span class="font-semibold text-slate-700 dark:text-slate-300 flex items-center gap-1">
                    <i class="bi bi-credit-card-2-front-fill text-blue-600"></i> Carte Bancaire (Stripe)
                </span>
            </div>
            <div class="flex justify-between items-center text-xs border-t border-slate-200/60 dark:border-slate-700/60 pt-2">
                <span class="text-slate-400 font-semibold">Statut</span>
                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300">
                    Payé & Confirmé
                </span>
            </div>
        </div>

        <div class="pt-2 space-y-3">
            <a href="/dashboard" class="w-full inline-flex items-center justify-center gap-2 py-3 px-6 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-lg shadow-blue-500/25 transition">
                <i class="bi bi-speedometer2"></i>
                <span>Aller au tableau de bord</span>
            </a>
            <a href="/services" class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-6 rounded-2xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs transition">
                <span>Découvrir d'autres services</span>
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', async () => {
        const urlParams = new URLSearchParams(window.location.search);
        const bookingId = urlParams.get('booking_id');
        const token = localStorage.getItem('khedma_token');

        if (bookingId && token) {
            try {
                const res = await fetch(`/api/v1/client/bookings/${bookingId}/payment-status`, {
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Accept': 'application/json'
                    }
                });
                if (res.ok) {
                    const data = await res.json();
                    document.getElementById('bookingNumber').textContent = data.booking_number || ('#' + bookingId);
                    document.getElementById('bookingAmount').textContent = Number(data.total_price).toFixed(2) + ' EUR';
                }
            } catch (err) {
                console.warn('Status sync notice:', err);
            }
        }
    });
</script>
@endsection
