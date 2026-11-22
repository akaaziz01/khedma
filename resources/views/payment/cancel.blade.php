@extends('layouts.app')

@section('title', 'Paiement Annulé - KHEDMA')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white dark:bg-slate-900 rounded-3xl p-8 border border-slate-200/80 dark:border-slate-800 shadow-xl text-center space-y-6">
        
        <!-- Cancel Icon -->
        <div class="w-20 h-20 bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 rounded-full flex items-center justify-center mx-auto text-4xl shadow-inner">
            <i class="bi bi-x-circle-fill"></i>
        </div>

        <div>
            <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-amber-50 dark:bg-amber-950/80 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-900/40">
                Paiement non finalisé
            </span>
            <h1 class="text-2xl font-black text-slate-900 dark:text-white mt-3">
                Vous avez annulé le paiement
            </h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed">
                Aucun montant n'a été débité de votre carte. Votre réservation reste enregistrée et vous pouvez réessayer à tout moment depuis votre tableau de bord.
            </p>
        </div>

        <div class="pt-2 space-y-3">
            <a href="/dashboard" class="w-full inline-flex items-center justify-center gap-2 py-3 px-6 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs shadow-lg shadow-blue-500/25 transition">
                <i class="bi bi-arrow-left"></i>
                <span>Retourner à mes réservations</span>
            </a>
            <a href="/services" class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-6 rounded-2xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs transition">
                <span>Explorer les services</span>
            </a>
        </div>
    </div>
</div>
@endsection
