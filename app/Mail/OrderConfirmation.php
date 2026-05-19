<?php
namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function build()
    {
        // Générer le PDF
        $pdf = Pdf::loadView('emails.invoices.pdf', ['order' => $this->order]);
        
        return $this->subject("Confirmation de commande #{$this->order->id}")
                   ->view('emails.orders.confirmation')
                   ->attachData($pdf->output(), "facture-{$this->order->id}.pdf", ['mime' => 'application/pdf'])
                   ->with(['order' => $this->order]);
    }
}