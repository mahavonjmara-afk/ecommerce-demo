@extends('layouts.app')

@section('content')
<div class="py-8 md:py-12 bg-background/50 min-h-screen">
    <div class="container mx-auto px-4 max-w-6xl">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4" data-aos="fade-up">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-secondary mb-1">👤 Mon Espace</h1>
                <p class="text-sm text-secondary/60">Bienvenue, {{ auth()->user()->name }}</p>
            </div>
            <a href="{{ route('client.orders.index') }}" class="btn-primary px-5 py-2.5 text-sm">Voir toutes mes commandes</a>
        </div>

        {{-- Stats Rapides --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8" data-aos="fade-up" data-aos-delay="100">
            <div class="card p-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-primary/10 text-primary rounded-full flex items-center justify-center text-2xl">📦</div>
                <div>
                    <p class="text-xs text-secondary/60 uppercase tracking-wide">Commandes totales</p>
                    <p class="text-xl font-bold text-secondary">{{ auth()->user()->orders->count() }}</p>
                </div>
            </div>
            <div class="card p-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center text-2xl">💰</div>
                <div>
                    <p class="text-xs text-secondary/60 uppercase tracking-wide">Total dépensé</p>
                    <p class="text-xl font-bold text-green-600">{{ number_format($totalSpent, 2, ',', ' ') }} €</p>
                </div>
            </div>
            <div class="card p-5 flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-2xl"></div>
                <div>
                    <p class="text-xs text-secondary/60 uppercase tracking-wide">Points fidélité</p>
                    <p class="text-xl font-bold text-blue-600">{{ floor($totalSpent) }} pts</p>
                </div>
            </div>
        </div>

        {{-- Dernières Commandes --}}
        <div class="card" data-aos="fade-up" data-aos-delay="200">
            <div class="p-5 border-b border-border flex justify-between items-center">
                <h2 class="text-lg font-semibold text-secondary">Commandes récentes</h2>
                <a href="{{ route('client.orders.index') }}" class="text-sm text-primary hover:underline">Tout voir →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-secondary/70 uppercase text-xs">
                        <tr>
                            <th class="px-5 py-3">N° Commande</th>
                            <th class="px-5 py-3">Date</th>
                            <th class="px-5 py-3">Statut</th>
                            <th class="px-5 py-3 text-right">Total</th>
                            <th class="px-5 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($recentOrders as $order)
                        <tr class="hover:bg-background/50 transition">
                            <td class="px-5 py-4 font-mono font-semibold text-primary">#{{ $order->id }}</td>
                            <td class="px-5 py-4 text-secondary/60">{{ $order->created_at->format('d/m/Y') }}</td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $order->status_badge_classes }}">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right font-bold text-secondary">{{ $order->formatted_total }}</td>
                            <td class="px-5 py-4 text-center">
                                <a href="{{ route('client.orders.show', $order) }}" class="text-xs font-medium text-primary hover:underline border border-primary/30 px-3 py-1 rounded hover:bg-primary/5 transition">Détails</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-5 py-10 text-center text-secondary/60">
                                <div class="text-4xl mb-2">🛒</div>
                                Aucune commande pour le moment. <a href="{{ url('/produits') }}" class="text-primary hover:underline">Commencer vos achats</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection