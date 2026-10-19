<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentReceipt extends Model
{
    protected $fillable = [
        'payment_id',
        'receipt_number',
        'language',
        'issued_at',
        'verification_token',
        'pdf_generated_at',
        'email_sent_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'pdf_generated_at' => 'datetime',
        'email_sent_at' => 'datetime',
    ];

    /**
     * Receipt belongs to the payment it documents.
     */
    public function payment(): BelongsTo
    {
        return $this->belongsTo(
            Payment::class
        );
    }

    /**
     * Determine whether the PDF has been generated.
     */
    public function hasPdf(): bool
    {
        return $this->pdf_generated_at !== null;
    }

    /**
     * Determine whether the receipt has been emailed.
     */
    public function hasBeenEmailed(): bool
    {
        return $this->email_sent_at !== null;
    }

    /**
     * Get the public verification URL.
     */
    public function getVerificationUrlAttribute(): string
    {
        return rtrim(config('app.url'), '/')
            . '/receipts/verify/'
            . $this->verification_token;
    }
}