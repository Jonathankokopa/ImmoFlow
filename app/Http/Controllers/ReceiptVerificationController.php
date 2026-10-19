<?php

namespace App\Http\Controllers;

use App\Models\PaymentReceipt;
use Illuminate\Contracts\View\View;

class ReceiptVerificationController extends Controller
{
    /**
     * Display a public receipt verification page.
     *
     * The verification token is intentionally used instead
     * of exposing the payment or receipt database ID.
     */
    public function show(string $token): View
    {
        $receipt = PaymentReceipt::query()
            ->with([
                'payment.rentInvoice.lease.tenant',
                'payment.rentInvoice.lease.unit.property',
            ])
            ->where('verification_token', $token)
            ->firstOrFail();

        $payment = $receipt->payment;

        return view(
            'receipts.verify',
            compact(
                'receipt',
                'payment'
            )
        );
    }
}