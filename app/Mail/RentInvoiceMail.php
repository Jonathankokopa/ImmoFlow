<?php

namespace App\Mail;

use App\Models\RentInvoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RentInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public RentInvoice $invoice,
        public bool $isReminder = false,
        public bool $isOverdue = false
    ) {
    }

    public function envelope(): Envelope
    {
        $tenantName = $this->invoice->lease?->tenant?->full_name ?? 'Tenant';
        $invoiceNum = $this->invoice->invoice_number;

        if ($this->isOverdue || (float) $this->invoice->penalty_amount > 0) {
            $subject = "OVERDUE RENT REMINDER (10% Late Fee Applied) - Invoice #{$invoiceNum}";
        } elseif ($this->isReminder) {
            $subject = "RENT REMINDER: Invoice #{$invoiceNum} - KopsRent";
        } else {
            $subject = "New Rent Invoice #{$invoiceNum} - KopsRent";
        }

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.rent-invoice',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
