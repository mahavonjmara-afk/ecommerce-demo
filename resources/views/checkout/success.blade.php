@extends('layouts.app')
@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-12 bg-background/50">
    <div class="card p-8 md:p-10 max-w-lg w-full text-center" data-aos="zoom-in">
        <div class="w-16 h-16 bg-success/10 text-success rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">✅</div>
        <h1 class="text-2xl font-bold text-secondary mb-2">Merci pour votre commande !</h1>
        <p class="text-secondary/70 mb-6">Votre commande <span class="font-mono font-bold text-primary">#{{ $orderId }}</span> a été enregistrée avec succès. Vous recevrez un email de confirmation sous peu.</p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ url('/') }}" class="btn-primary px-6 py-2.5 text-sm">Retour à l'accueil</a>
            <a href="{{ url('/produits') }}" class="px-6 py-2.5 text-sm font-medium text-secondary bg-card border border-border rounded-btn hover:border-primary/30 transition">Continuer mes achats</a>
        </div>
    </div>
</div>
@endsection