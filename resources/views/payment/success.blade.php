@extends('layouts.app')
@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-12">
    <div class="card p-10 max-w-lg w-full text-center" data-aos="zoom-in">
        <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4 text-4xl">✅</div>
        <h1 class="text-2xl font-bold text-secondary mb-2">Paiement réussi !</h1>
        <p class="text-secondary/70 mb-6">Votre commande a été confirmée. Vous recevrez un email de confirmation sous peu.</p>
        <a href="{{ route('client.dashboard') }}" class="btn-primary px-6 py-2.5 text-sm">Voir ma commande</a>
    </div>
</div>
@endsection