@extends('layouts.app')

@section('content')
<div class="py-8 md:py-12 bg-background/50 min-h-screen">
    <div class="container mx-auto px-4 max-w-6xl">
        <h1 class="text-2xl font-bold text-secondary mb-6" data-aos="fade-up">Finaliser votre commande</h1>
        
        {{-- ✅ Ligne corrigée : @js() sans {{ }} --}}
        <form method="POST" action="{{ route('checkout.store') }}" 
              x-data="checkoutForm(@js($cart), @js($subtotal), @js($tax), @js($total))"
              class="grid grid-cols-1 lg:grid-cols-3 gap-6" data-aos="fade-up" data-aos-delay="100">
            @csrf

            {{-- 🔹 GAUCHE : FORMULAIRES --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Adresse --}}
                <div class="card p-5 md:p-6">
                    <h2 class="text-lg font-semibold text-secondary mb-4 flex items-center gap-2">📍 Adresse de livraison</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="input-label">Adresse complète</label>
                            <input type="text" name="shipping_address" x-model="form.address" class="input-field @error('shipping_address') border-danger @enderror" placeholder="12 rue de la Paix" required>
                            @error('shipping_address') <p class="text-xs text-danger mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="input-label">Code postal</label>
                            <input type="text" name="shipping_postal_code" x-model="form.postalCode" class="input-field @error('shipping_postal_code') border-danger @enderror" placeholder="75001" required>
                        </div>
                        <div>
                            <label class="input-label">Ville</label>
                            <input type="text" name="shipping_city" x-model="form.city" class="input-field @error('shipping_city') border-danger @enderror" placeholder="Paris" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="input-label">Email de contact</label>
                            <input type="email" name="email" x-model="form.email" class="input-field @error('email') border-danger @enderror" placeholder="vous@email.com" required>
                        </div>
                        <div>
                            <label class="input-label">Téléphone (optionnel)</label>
                            <input type="tel" name="phone" x-model="form.phone" class="input-field" placeholder="06 12 34 56 78">
                        </div>
                        <div>
                            <label class="input-label">Pays</label>
                            <select name="shipping_country" x-model="form.country" class="input-field">
                                <option value="France" selected>🇫🇷 France</option>
                                <option value="Belgique">🇧🇪 Belgique</option>
                                <option value="Suisse">🇨🇭 Suisse</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Livraison --}}
                <div class="card p-5 md:p-6">
                    <h2 class="text-lg font-semibold text-secondary mb-4">🚚 Mode de livraison</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        @foreach([
                            ['id' => 'standard', 'name' => 'Standard', 'delay' => '3-5 jours', 'price' => 4.90, 'icon' => '📦'],
                            ['id' => 'express', 'name' => 'Express', 'delay' => '24-48h', 'price' => 9.90, 'icon' => '🚀'],
                            ['id' => 'relay', 'name' => 'Point relais', 'delay' => '4-6 jours', 'price' => 3.50, 'icon' => '🏪']
                        ] as $method)
                        <label class="cursor-pointer border border-border rounded-btn p-4 transition hover:border-primary/50 flex flex-col items-center text-center"
                               :class="delivery === '{{ $method['id'] }}' ? 'border-primary bg-primary/5 ring-1 ring-primary/20' : 'bg-card'">
                            <input type="radio" name="delivery_method" value="{{ $method['id'] }}" class="hidden" :checked="delivery === '{{ $method['id'] }}'" @change="delivery = '{{ $method['id'] }}'; deliveryCost = {{ $method['price'] }}">
                            <div class="text-2xl mb-2">{{ $method['icon'] }}</div>
                            <div class="text-sm font-medium text-secondary">{{ $method['name'] }}</div>
                            <div class="text-xs text-secondary/60 mt-1">{{ $method['delay'] }}</div>
                            <div class="text-sm font-bold text-primary mt-2">{{ number_format($method['price'], 2, ',', ' ') }} €</div>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- 🔹 DROITE : RÉCAPITULATIF (STICKY) --}}
            <div class="lg:col-span-1">
                <div class="card p-5 md:p-6 sticky top-24 space-y-4">
                    <h2 class="text-lg font-semibold text-secondary">📦 Votre commande</h2>
                    
                    <div class="space-y-3 max-h-60 overflow-y-auto pr-1">
                        @foreach($cart as $item)
                        <div class="flex gap-3 items-center">
                            <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" class="w-12 h-12 object-cover rounded bg-gray-100">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-secondary truncate">{{ $item['name'] }}</p>
                                <p class="text-xs text-secondary/60">Qté: {{ $item['qty'] }} × {{ number_format($item['price'], 2, ',', ' ') }} €</p>
                            </div>
                            <span class="text-sm font-medium text-secondary">{{ number_format($item['price'] * $item['qty'], 2, ',', ' ') }} €</span>
                        </div>
                        @endforeach
                    </div>

                    <div class="border-t border-border pt-4 space-y-2 text-sm">
                        <div class="flex justify-between text-secondary/70"><span>Sous-total HT</span><span>{{ number_format($subtotal / 1.2, 2, ',', ' ') }} €</span></div>
                        <div class="flex justify-between text-secondary/70"><span>TVA (20%)</span><span>{{ number_format($tax, 2, ',', ' ') }} €</span></div>
                        <div class="flex justify-between text-secondary/70"><span>Livraison</span><span x-text="formatPrice(deliveryCost)">4,90 €</span></div>
                        <div class="flex justify-between text-base font-bold text-secondary pt-3 border-t border-border">
                            <span>Total TTC</span>
                            <span x-text="formatPrice(totalTTC)">0,00 €</span>
                        </div>
                    </div>

                    <button type="submit" class="btn-primary w-full py-2.5 text-sm font-semibold flex items-center justify-center gap-2" :disabled="processing">
                        <svg x-show="processing" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="processing ? 'Traitement en cours...' : 'Confirmer et payer'"></span>
                    </button>
                    
                    <p class="text-xs text-center text-secondary/50 mt-2">🔒 Paiement sécurisé. Vos données sont protégées.</p>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function checkoutForm(cart, subtotal, tax, total) {
        return {
            delivery: 'standard',
            deliveryCost: 4.90,
            processing: false,
            form: { email: '', phone: '', address: '', city: '', postalCode: '', country: 'France' },
            get totalTTC() { return total + this.deliveryCost; },
            formatPrice(price) {
                return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(price);
            }
        }
    }
</script>
@endsection