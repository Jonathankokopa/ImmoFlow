<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\PaymentReceiptDataService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class PaymentReceiptController extends Controller
{
    public function show(
        Payment $payment,
        PaymentReceiptDataService $receiptDataService
    ) {
        $data = $receiptDataService->build($payment);

        return view(
            'receipts.show',
            compact('data')
        );
    }

    public function download(
        Payment $payment,
        PaymentReceiptDataService $receiptDataService
    ): Response {
        $data = $receiptDataService->build($payment);

        $pdf = Pdf::loadView(
            'receipts.pdf',
            compact('data')
        );

        $pdf->setPaper('a4', 'portrait');

        $receiptNumber = $data['receipt']['number'];

        return $pdf->download(
            $receiptNumber . '.pdf'
        );
    }
}