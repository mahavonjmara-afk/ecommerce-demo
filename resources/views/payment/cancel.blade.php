@extends('layouts.app')
@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-12">
    <div class="card p-10 max-w-lg w-full text-center" data-aos="zoom-in">
        <div class="w-20 h-20 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center mx-auto mb-4 text-4xl">⚠️</div>
        <h1 class="text-2xl font-bold text-secondary mb-2">Paiement annulé</h1>
        <p class="text-secondary/70 mb-6">Vous avez annulé le paiement. Votre commande est toujours en attente.</p>
        <a href="{{ route('client.dashboard') }}" class="btn-primary px-6 py-2.5 text-sm">Retourner au paiement</a>
    </div>
</div>
@endsection