<?php
namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\VonageMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Broadcasting\PrivateChannel;

class OrderStatusUpdated extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    public function __construct(public Order $order, public string $newStatus) {}

    public function via($notifiable): array
    {
        $channels = ['database', 'broadcast'];
        if ($notifiable->phone) {
            $channels[] = 'vonage'; // SMS si numéro renseigné
        }
        return $channels;
    }

    public function toDatabase($notifiable): array
    {
        return [
            'order_id' => $this->order->id,
            'status' => $this->newStatus,
            'message' => "Votre commande #{$this->order->id} est maintenant : " . Order::getStatusLabels()[$this->newStatus],
            'created_at' => now()->toIso8601String(),
        ];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'order_id' => $this->order->id,
            'status' => $this->newStatus,
            'status_label' => Order::getStatusLabels()[$this->newStatus],
            'message' => " Commande #{$this->order->id} → " . Order::getStatusLabels()[$this->newStatus],
        ]);
    }

    public function toVonage($notifiable): VonageMessage
    {
        $shortUrl = url("/mon-compte/commande/{$this->order->id}");
        return (new VonageMessage())
            ->content("E-Shop: Votre commande #{$this->order->id} est passée à '" . Order::getStatusLabels()[$this->newStatus] . "'. Suivi: {$shortUrl}");
    }

    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel("user.{$this->order->user_id}");
    }

    public function broadcastAs(): string
    {
        return 'OrderStatusUpdated';
    }
}