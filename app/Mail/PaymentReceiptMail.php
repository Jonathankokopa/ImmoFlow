<?php

namespace App\Mail;

use App\Models\Payment;
use App\Services\PaymentReceiptDataService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Payment $payment
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Payment Receipt - KopsRent',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.payment-receipt',
        );
    }

    public function attachments(): array
    {
        $data = app(
            PaymentReceiptDataService::class
        )->build(
            $this->payment
        );

        $receipt = $this->payment
            ->fresh('receipt')
            ->receipt;

        $receiptNumber =
            $data['receipt']['number'];

        $pdf = Pdf::loadView(
            'receipts.pdf',
            compact('data')
        );

        $pdf->setPaper(
            'a4',
            'portrait'
        );

        /*
         * The PDF has been successfully generated.
         */
        if ($receipt && $receipt->pdf_generated_at === null) {
            $receipt->update([
                'pdf_generated_at' => now(),
            ]);
        }

        return [
            Attachment::fromData(
                fn () => $pdf->output(),
                $receiptNumber . '.pdf'
            )->withMime(
                'application/pdf'
            ),
        ];
    }
}