<?php
namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class PaymentController extends Controller
{
    public function checkout(Order $order)
    {
        // 🔒 Vérifier que la commande appartient à l'utilisateur
        abort_unless($order->user_id === auth()->id(), 403);
        
        // Ne payer que les commandes en attente
        if ($order->status !== 'pending') {
            return redirect()->route('client.orders.show', $order)
                ->with('error', 'Cette commande a déjà été payée.');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => (int)($order->total * 100), // En centimes
                'currency' => 'eur',
                'payment_method_types' => ['card'],
                'metadata' => [
                    'order_id' => $order->id,
                    'user_id' => $order->user_id,
                ],
                'description' => "Commande #{$order->id} - E-Shop",
            ]);

            return view('payment.checkout', compact('order', 'paymentIntent'));
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur de connexion avec Stripe: ' . $e->getMessage());
        }
    }

    public function success(Request $request)
    {
        return view('payment.success');
    }

    public function cancel()
    {
        return view('payment.cancel');
    }

    /**
     * Gestion des webhooks Stripe
     */
    public function webhook(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        $endpointSecret = config('services.stripe.webhook_secret');

        $payload = @file_get_contents('php://input');
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (SignatureVerificationException $e) {
            abort(400, 'Signature webhook invalide');
        }

        // Traiter les événements
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $this->handlePaymentSuccess($paymentIntent);
                break;

            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                $this->handlePaymentFailed($paymentIntent);
                break;

            default:
                // Événements non gérés
                break;
        }

        return response()->json(['status' => 'success']);
    }

    private function handlePaymentSuccess($paymentIntent)
    {
        $orderId = $paymentIntent->metadata->order_id;
        $order = Order::find($orderId);

        if ($order && $order->status === 'pending') {
            $order->update([
                'status' => 'processing',
                'payment_intent_id' => $paymentIntent->id,
            ]);

            // 📧 Envoyer email de confirmation
            if ($order->user) {
                $order->user->notify(new \App\Notifications\OrderConfirmed($order));
            }

            // 📱 Notification temps réel
            if ($order->user) {
                $order->user->notify(new \App\Notifications\OrderStatusUpdated($order, 'processing'));
            }
        }
    }

    private function handlePaymentFailed($paymentIntent)
    {
        $orderId = $paymentIntent->metadata->order_id;
        $order = Order::find($orderId);

        if ($order) {
            $order->update(['status' => 'cancelled']);
        }
    }
}