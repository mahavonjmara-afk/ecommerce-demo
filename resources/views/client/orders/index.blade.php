@extends('layouts.app')

@section('content')
<div class="py-8 md:py-12 bg-background/50 min-h-screen">
    <div class="container mx-auto px-4 max-w-6xl">
        <div class="mb-8" data-aos="fade-up">
            <h1 class="text-2xl md:text-3xl font-bold text-secondary mb-2">📦 Historique des commandes</h1>
            <p class="text-sm text-secondary/60">Retrouvez le détail et le suivi de toutes vos commandes</p>
        </div>

        <div class="card overflow-hidden" data-aos="fade-up" data-aos-delay="100">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-secondary/70 uppercase text-xs">
                        <tr>
                            <th class="px-6 py-4">Commande</th>
                            <th class="px-6 py-4">Date</th>
                            <th class="px-6 py-4">Statut</th>
                            <th class="px-6 py-4 text-right">Total</th>
                            <th class="px-6 py-4 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        @forelse($orders as $order)
                        <tr class="hover:bg-background/50 transition">
                            <td class="px-6 py-4 font-mono font-semibold text-primary">#{{ $order->id }}</td>
                            <td class="px-6 py-4 text-secondary/60">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $order->status_badge_classes }}">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-secondary">{{ $order->formatted_total }}</td>
                            <td class="px-6 py-4 text-center flex justify-center gap-2">
                                <a href="{{ route('client.orders.show', $order) }}" class="text-xs font-medium text-primary hover:underline">Voir</a>
                                <span class="text-border">|</span>
                                <a href="{{ route('client.orders.invoice', $order) }}" class="text-xs font-medium text-secondary/60 hover:text-primary transition" target="_blank">Facture PDF</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-secondary/60">Aucune commande trouvée.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($orders->hasPages())
            <div class="p-4 border-t border-border bg-gray-50/50">{{ $orders->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection