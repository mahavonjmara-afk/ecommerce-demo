@extends('layouts.app')

@section('content')
<div class="py-8 md:py-12 bg-background/50 min-h-screen">
    <div class="container mx-auto px-4 max-w-5xl">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8 gap-4" data-aos="fade-up">
            <div>
                <a href="{{ route('client.orders.index') }}" class="text-sm text-secondary/60 hover:text-primary transition mb-1 inline-block">← Retour aux commandes</a>
                <h1 class="text-2xl font-bold text-secondary">Commande #{{ $order->id }}</h1>
            </div>
            <div class="flex gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium border {{ $order->status_badge_classes }}">
                    {{ $order->status_label }}
                </span>
                <a href="{{ route('client.orders.invoice', $order) }}" target="_blank" class="btn-primary px-4 py-2 text-sm flex items-center gap-2">
                    📥 Télécharger la facture
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- 🔹 GAUCHE : Détails & Suivi --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Timeline de Suivi --}}
                <div class="card p-6" data-aos="fade-up" data-aos-delay="100">
                    <h2 class="text-lg font-semibold text-secondary mb-6">🚚 Suivi de livraison</h2>
                    
                    @php
                        $steps = [
                            'pending'    => ['label' => 'Commande confirmée', 'icon' => '✅', 'desc' => 'Paiement validé'],
                            'processing' => ['label' => 'En préparation', 'icon' => '📦', 'desc' => 'Colis emballé'],
                            'shipped'    => ['label' => 'Expédié', 'icon' => '🚚', 'desc' => 'En transit'],
                            'delivered'  => ['label' => 'Livré', 'icon' => '🏠', 'desc' => 'Reçu avec succès'],
                        ];
                        $currentStatus = $order->status;
                        $statusOrder = ['pending', 'processing', 'shipped', 'delivered'];
                        $currentIndex = array_search($currentStatus, $statusOrder);
                        if ($currentIndex === false) $currentIndex = 0;
                    @endphp

                    <div class="relative">
                        <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-border"></div>
                        <div class="absolute left-4 top-0 h-1/2 w-0.5 bg-primary transition-all duration-500" style="height: {{ ($currentIndex / 3) * 100 }}%"></div>
                        
                        <div class="space-y-8 relative">
                            @foreach($statusOrder as $index => $stepKey)
                                @php $isCompleted = $index <= $currentIndex; @endphp
                                <div class="flex gap-4">
                                    <div class="relative z-10 w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold 
                                                {{ $isCompleted ? 'bg-primary text-white' : 'bg-gray-100 text-secondary/40 border border-border' }}">
                                        {{ $steps[$stepKey]['icon'] }}
                                    </div>
                                    <div class="pt-1">
                                        <h3 class="font-medium {{ $isCompleted ? 'text-secondary' : 'text-secondary/40' }}">
                                            {{ $steps[$stepKey]['label'] }}
                                        </h3>
                                        <p class="text-xs text-secondary/60">{{ $steps[$stepKey]['desc'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Articles commandés --}}
                <div class="card" data-aos="fade-up" data-aos-delay="200">
                    <div class="p-5 border-b border-border">
                        <h2 class="text-lg font-semibold text-secondary">Articles</h2>
                    </div>
                    <div class="divide-y divide-border">
                        @foreach($order->items as $item)
                        <div class="p-5 flex gap-4 items-center">
                            <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center text-2xl">📦</div>
                            <div class="flex-1">
                                <h3 class="font-medium text-secondary">{{ $item->product_name }}</h3>
                                <p class="text-xs text-secondary/50">Qté: {{ $item->quantity }} × {{ number_format($item->price, 2, ',', ' ') }} €</p>
                            </div>
                            <span class="font-semibold text-secondary">{{ number_format($item->price * $item->quantity, 2, ',', ' ') }} €</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- 🔹 DROITE : Infos Livraison & Paiement --}}
            <div class="space-y-6">
                <div class="card p-5 space-y-4" data-aos="fade-up" data-aos-delay="300">
                    <h2 class="text-lg font-semibold text-secondary border-b border-border pb-3">📍 Adresse de livraison</h2>
                    <div class="text-sm text-secondary/70 space-y-1">
                        <p class="font-medium text-secondary">{{ $order->email }}</p>
                        @if($order->phone) <p>📞 {{ $order->phone }}</p> @endif
                        <p>{{ $order->shipping_address }}</p>
                        <p>{{ $order->shipping_postal_code }} {{ $order->shipping_city }}</p>
                        <p>{{ $order->shipping_country }}</p>
                    </div>
                </div>

                <div class="card p-5 space-y-3" data-aos="fade-up" data-aos-delay="400">
                    <h2 class="text-lg font-semibold text-secondary border-b border-border pb-3">🧾 Récapitulatif</h2>
                    <div class="flex justify-between text-sm text-secondary/70">
                        <span>Sous-total HT</span>
                        <span>{{ number_format($order->subtotal / 1.2, 2, ',', ' ') }} €</span>
                    </div>
                    <div class="flex justify-between text-sm text-secondary/70">
                        <span>TVA (20%)</span>
                        <span>{{ number_format($order->tax, 2, ',', ' ') }} €</span>
                    </div>
                    <div class="flex justify-between text-sm text-secondary/70">
                        <span>Livraison</span>
                        <span>{{ number_format($order->delivery_cost, 2, ',', ' ') }} €</span>
                    </div>
                    <div class="flex justify-between text-base font-bold text-secondary pt-3 border-t border-border">
                        <span>Total TTC</span>
                        <span class="text-primary">{{ $order->formatted_total }}</span>
                    </div>
                    <div class="mt-4 p-3 bg-gray-50 rounded text-xs text-secondary/60">
                        <p>💳 Paiement : <span class="font-medium text-secondary">Carte bancaire</span></p>
                        <p class="mt-1">📅 Date : {{ $order->created_at->format('d/m/Y à H:i') }}</p>
                    </div>
                </div>

                @if($order->canBeCancelled())
                <button class="w-full py-3 text-sm font-medium text-danger border border-danger/30 rounded-btn hover:bg-danger/5 transition">
                    Annuler la commande
                </button>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection