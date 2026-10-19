<?php

namespace App\Http\Controllers;

use App\Models\RentInvoice;
use App\Services\MpesaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class MpesaController extends Controller
{
    /**
     * Receive the M-Pesa STK Push callback.
     *
     * M-Pesa calls this endpoint after the customer completes
     * or cancels the STK prompt.
     */
    public function callback(
        Request $request,
        MpesaService $mpesaService
    ): JsonResponse {
        $payload = $request->all();

        Log::info(
            'M-Pesa callback received.',
            [
                'payload' => $payload,
            ]
        );

        try {
            $mpesaService->handleCallback(
                $payload
            );

            /*
             * Acknowledge a successfully received and
             * processed callback to Daraja.
             */
            return response()->json([
                'ResultCode' => 0,
                'ResultDesc' =>
                    'Callback received successfully.',
            ]);
        } catch (Throwable $e) {
            /*
             * Do not expose internal application details
             * to Safaricom.
             */
            Log::error(
                'M-Pesa callback processing failed.',
                [
                    'error' =>
                        $e->getMessage(),

                    'exception' =>
                        get_class($e),

                    'payload' =>
                        $payload,
                ]
            );

            return response()->json([
                'ResultCode' => 1,
                'ResultDesc' =>
                    'Callback processing failed.',
            ], 500);
        }
    }

    /**
     * Initiate an M-Pesa STK Push.
     *
     * This endpoint creates a pending payment and asks
     * Safaricom to send the STK prompt to the tenant's phone.
     *
     * Final payment confirmation does NOT happen here.
     * The final status is determined by the Daraja callback.
     */
    public function stkPush(
        Request $request,
        MpesaService $mpesaService
    ): JsonResponse {
        $validated = $request->validate([
            'invoice_id' => [
                'required',
                'integer',
                'exists:rent_invoices,id',
            ],

            'amount' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'phone' => [
                'required',
                'string',
            ],
        ]);

        $invoice = RentInvoice::findOrFail(
            $validated['invoice_id']
        );

        try {
            $payment = $mpesaService->initiateStkPush(
                $invoice,
                (float) $validated['amount'],
                $validated['phone'],
                auth()->id()
            );

            return response()->json([
                'success' => true,

                'message' =>
                    'M-Pesa STK Push sent successfully.',

                'payment_id' =>
                    $payment->id,

                'invoice_id' =>
                    $invoice->id,

                'amount' =>
                    $payment->amount,

                'currency' =>
                    $payment->currency,

                'status' =>
                    $payment->status,

                'checkout_request_id' =>
                    $payment->transaction_reference,

                'merchant_request_id' =>
                    data_get(
                        $payment->metadata,
                        'merchant_request_id'
                    ),
            ]);
        } catch (Throwable $e) {
            Log::error(
                'M-Pesa STK Push endpoint failed.',
                [
                    'invoice_id' =>
                        $invoice->id,

                    'error' =>
                        $e->getMessage(),

                    'exception' =>
                        get_class($e),
                ]
            );

            return response()->json([
                'success' => false,

                'message' =>
                    $e->getMessage(),
            ], 422);
        }
    }
}