@extends('layouts.app')

@section('content')
<div class="py-12 bg-background/50 min-h-screen">
    <div class="container mx-auto px-4 max-w-3xl">
        <div class="mb-8" data-aos="fade-up">
            <h1 class="text-2xl font-bold text-secondary mb-2">💳 Paiement sécurisé</h1>
            <p class="text-sm text-secondary/60">Commande #{{ $order->id }} - {{ $order->formatted_total }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Formulaire de paiement --}}
            <div class="card p-6" data-aos="fade-up" data-aos-delay="100">
                <form id="payment-form" class="space-y-4">
                    @csrf
                    <div>
                        <label class="input-label">Nom sur la carte</label>
                        <input type="text" id="cardholder-name" class="input-field" placeholder="Jean Dupont" required>
                    </div>
                    
                    <div>
                        <label class="input-label">Détails de la carte</label>
                        <div id="card-element" class="p-3 border border-border rounded-btn bg-card"></div>
                        <div id="card-errors" class="text-danger text-xs mt-2" role="alert"></div>
                    </div>

                    <button type="submit" id="submit-button" class="btn-primary w-full py-3 text-sm font-semibold flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        Payer {{ $order->formatted_total }}
                    </button>

                    <p class="text-xs text-center text-secondary/50 mt-4">
                        🔒 Paiement sécurisé par Stripe. Vos données sont cryptées.
                    </p>
                </form>
            </div>

            {{-- Récapitulatif --}}
            <div class="space-y-4" data-aos="fade-up" data-aos-delay="200">
                <div class="card p-5">
                    <h3 class="font-semibold text-secondary mb-3">Récapitulatif</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between text-secondary/70">
                            <span>Sous-total</span>
                            <span>{{ number_format($order->subtotal / 1.2, 2, ',', ' ') }} €</span>
                        </div>
                        <div class="flex justify-between text-secondary/70">
                            <span>TVA (20%)</span>
                            <span>{{ number_format($order->tax, 2, ',', ' ') }} €</span>
                        </div>
                        <div class="flex justify-between text-secondary/70">
                            <span>Livraison</span>
                            <span>{{ number_format($order->delivery_cost, 2, ',', ' ') }} €</span>
                        </div>
                        <div class="flex justify-between font-bold text-secondary pt-2 border-t border-border">
                            <span>Total</span>
                            <span class="text-primary">{{ $order->formatted_total }}</span>
                        </div>
                    </div>
                </div>

                <div class="card p-5 bg-blue-50 border-blue-200">
                    <h4 class="font-medium text-blue-900 mb-2">📍 Livraison</h4>
                    <p class="text-sm text-blue-800">{{ $order->shipping_address }}</p>
                    <p class="text-sm text-blue-800">{{ $order->shipping_postal_code }} {{ $order->shipping_city }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Stripe.js --}}
<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe('{{ config('services.stripe.key') }}');
    const elements = stripe.elements();
    const cardElement = elements.create('card', {
        style: {
            base: {
                fontSize: '14px',
                color: '#333',
                '::placeholder': { color: '#94a3b8' },
            },
        },
    });
    cardElement.mount('#card-element');

    const form = document.getElementById('payment-form');
    const submitButton = document.getElementById('submit-button');

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        submitButton.disabled = true;
        submitButton.innerHTML = 'Traitement en cours...';

        const { paymentIntent, error } = await stripe.confirmCardPayment(
            '{{ $paymentIntent->client_secret }}',
            {
                payment_method: {
                    card: cardElement,
                    billing_details: {
                        name: document.getElementById('cardholder-name').value,
                    },
                },
            }
        );

        if (error) {
            document.getElementById('card-errors').textContent = error.message;
            submitButton.disabled = false;
            submitButton.innerHTML = 'Payer {{ $order->formatted_total }}';
        } else if (paymentIntent.status === 'succeeded') {
            window.location.href = '{{ route('payment.success') }}';
        }
    });
</script>
@endsection