<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceProcessed extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public readonly Invoice $invoice)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invoice Processed',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice.invoice_processed',
        );
    }
}
