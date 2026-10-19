<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\RentInvoice;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;

class MpesaService
{
    public function __construct(
        private readonly PaymentService $paymentService
    ) {
    }

    /**
     * Initiate an M-Pesa STK Push.
     *
     * The payment is first created as pending.
     * Completion happens only after the M-Pesa callback confirms success.
     */
    public function initiateStkPush(
        RentInvoice $invoice,
        float $amount,
        string $phone,
        ?int $recordedBy = null
    ): Payment {
        if (strtoupper($invoice->currency) !== 'KES') {
            throw ValidationException::withMessages([
                'currency' => 'M-Pesa payments must use KES.',
            ]);
        }

        $amount = round($amount, 2);

        if ($amount <= 0) {
            throw ValidationException::withMessages([
                'amount' => 'M-Pesa payment amount must be greater than zero.',
            ]);
        }

        /*
         * Daraja STK Push expects a whole-number amount.
         */
        if ($amount != floor($amount)) {
            throw ValidationException::withMessages([
                'amount' => 'M-Pesa payment amounts must be whole Kenyan Shillings.',
            ]);
        }

        $phone = $this->normalizeKenyanPhone($phone);

        /*
         * Create the local pending payment before contacting Safaricom.
         */
        $payment = $this->paymentService->createPendingPayment(
            $invoice,
            $amount,
            Payment::PROVIDER_MPESA,
            Payment::METHOD_MPESA,
            null,
            [
                'phone_number' => $phone,
                'provider' => 'mpesa',
                'environment' => config(
                    'services.mpesa.environment',
                    'sandbox'
                ),
                'initiated_at' => now()->toIso8601String(),
            ],
            $recordedBy
        );

        try {
            $accessToken = $this->getAccessToken();

            $timestamp = now()->format('YmdHis');

            $shortcode = (string) config(
                'services.mpesa.shortcode'
            );

            $passkey = (string) config(
                'services.mpesa.passkey'
            );

            if ($shortcode === '' || $passkey === '') {
                throw new \RuntimeException(
                    'M-Pesa shortcode or passkey is not configured.'
                );
            }

            /*
             * Daraja password:
             *
             * Base64(BusinessShortCode + Passkey + Timestamp)
             */
            $password = base64_encode(
                $shortcode
                . $passkey
                . $timestamp
            );

            $accountReference = $this->makeAccountReference($invoice);

            /*
             * Daraja TransactionDesc maximum length is 13 characters.
             */
            $transactionDescription = 'Rent Payment';

            $callbackUrl = (string) config(
                'services.mpesa.callback_url'
            );

            if ($callbackUrl === '') {
                throw new \RuntimeException(
                    'M-Pesa callback URL is not configured.'
                );
            }

            $payload = [
                'BusinessShortCode' => $shortcode,
                'Password' => $password,
                'Timestamp' => $timestamp,
                'TransactionType' => config(
                    'services.mpesa.transaction_type',
                    'CustomerPayBillOnline'
                ),
                'Amount' => (int) $amount,
                'PartyA' => $phone,
                'PartyB' => $shortcode,
                'PhoneNumber' => $phone,
                'CallBackURL' => $callbackUrl,
                'AccountReference' => $accountReference,
                'TransactionDesc' => $transactionDescription,
            ];

            $response = Http::timeout(
                (int) config(
                    'services.mpesa.timeout',
                    30
                )
            )
                ->withToken($accessToken)
                ->acceptJson()
                ->asJson()
                ->post(
                    config('services.mpesa.stk_url'),
                    $payload
                );

            if (!$response->successful()) {
                $this->markInitiationFailed(
                    $payment,
                    $response
                );

                /*
                 * Log the complete Daraja response body.
                 *
                 * Do not log the request payload because it contains
                 * the generated Daraja password.
                 */
                Log::error('M-Pesa STK Push HTTP failure.', [
                    'payment_id' => $payment->id,
                    'invoice_id' => $invoice->id,
                    'http_status' => $response->status(),
                    'response_body' => $response->body(),
                ]);

                throw new \RuntimeException(
                    'M-Pesa STK Push request failed with HTTP status '
                    . $response->status()
                    . '.'
                );
            }

            $responseData = $response->json();

            /*
             * Daraja normally returns ResponseCode = 0 when accepted.
             *
             * Accepted does not mean the payment is completed.
             */
            if (
                (string) ($responseData['ResponseCode'] ?? '')
                !== '0'
            ) {
                $this->paymentService->failPayment(
                    $payment,
                    [
                        'mpesa_initiation_failed' => true,
                        'response_code' =>
                            $responseData['ResponseCode'] ?? null,
                        'response_description' =>
                            $responseData['ResponseDescription'] ?? null,
                        'daraja_response' => $responseData,
                    ]
                );

                throw new \RuntimeException(
                    $responseData['ResponseDescription']
                    ?? 'M-Pesa STK Push request was rejected.'
                );
            }

            $merchantRequestId =
                $responseData['MerchantRequestID'] ?? null;

            $checkoutRequestId =
                $responseData['CheckoutRequestID'] ?? null;

            if (!$checkoutRequestId) {
                $this->paymentService->failPayment(
                    $payment,
                    [
                        'mpesa_initiation_failed' => true,
                        'error' =>
                            'Daraja did not return CheckoutRequestID.',
                        'daraja_response' => $responseData,
                    ]
                );

                throw new \RuntimeException(
                    'M-Pesa did not return a CheckoutRequestID.'
                );
            }

            /*
             * Temporarily use CheckoutRequestID as the transaction reference.
             * On successful callback, replace it with the M-Pesa receipt.
             */
            $payment->transaction_reference = $checkoutRequestId;

            $payment->metadata = array_merge(
                $payment->metadata ?? [],
                [
                    'provider' => 'mpesa',
                    'merchant_request_id' => $merchantRequestId,
                    'checkout_request_id' => $checkoutRequestId,
                    'phone_number' => $phone,
                    'account_reference' => $accountReference,
                    'request_timestamp' => $timestamp,
                    'transaction_description' =>
                        $transactionDescription,
                    'daraja_response' => $responseData,
                    'stk_push_accepted_at' =>
                        now()->toIso8601String(),
                ]
            );

            $payment->save();

            return $payment->fresh();
        } catch (Throwable $e) {
            /*
             * If the payment is still pending, mark it failed.
             * Do not overwrite a completed/final payment.
             */
            if (
                $payment->fresh()->status ===
                Payment::STATUS_PENDING
            ) {
                try {
                    $this->paymentService->failPayment(
                        $payment,
                        [
                            'mpesa_exception' => true,
                            'error' => $e->getMessage(),
                        ]
                    );
                } catch (Throwable $failureException) {
                    Log::error(
                        'Failed to mark M-Pesa payment as failed.',
                        [
                            'payment_id' => $payment->id,
                            'error' => $failureException->getMessage(),
                        ]
                    );
                }
            }

            Log::error(
                'M-Pesa STK Push initiation failed.',
                [
                    'payment_id' => $payment->id,
                    'invoice_id' => $invoice->id,
                    'error' => $e->getMessage(),
                ]
            );

            throw $e;
        }
    }

    /**
     * Handle the M-Pesa STK callback.
     */
    public function handleCallback(array $payload): void
    {
        $callback = data_get(
            $payload,
            'Body.stkCallback'
        );

        if (!is_array($callback)) {
            Log::warning(
                'M-Pesa callback received without stkCallback.',
                ['payload' => $payload]
            );

            return;
        }

        $merchantRequestId =
            $callback['MerchantRequestID'] ?? null;

        $checkoutRequestId =
            $callback['CheckoutRequestID'] ?? null;

        $resultCode =
            isset($callback['ResultCode'])
                ? (int) $callback['ResultCode']
                : null;

        $resultDescription =
            $callback['ResultDesc'] ?? null;

        if (!$checkoutRequestId) {
            Log::warning(
                'M-Pesa callback missing CheckoutRequestID.',
                ['payload' => $payload]
            );

            return;
        }

        $payment = Payment::query()
            ->where(
                'provider',
                Payment::PROVIDER_MPESA
            )
            ->where(
                'transaction_reference',
                $checkoutRequestId
            )
            ->first();

        if (!$payment) {
            $payment = Payment::query()
                ->where(
                    'provider',
                    Payment::PROVIDER_MPESA
                )
                ->where(
                    'metadata->checkout_request_id',
                    $checkoutRequestId
                )
                ->first();
        }

        if (!$payment) {
            Log::warning(
                'M-Pesa callback could not be matched to a payment.',
                [
                    'merchant_request_id' => $merchantRequestId,
                    'checkout_request_id' => $checkoutRequestId,
                ]
            );

            return;
        }

        /*
         * Idempotency: final payments ignore duplicate callbacks.
         */
        if ($payment->isFinal()) {
            Log::info(
                'Ignoring duplicate M-Pesa callback for final payment.',
                [
                    'payment_id' => $payment->id,
                    'status' => $payment->status,
                    'checkout_request_id' => $checkoutRequestId,
                ]
            );

            return;
        }

        $callbackMetadata = [
            'callback_received_at' =>
                now()->toIso8601String(),
            'callback_merchant_request_id' =>
                $merchantRequestId,
            'callback_checkout_request_id' =>
                $checkoutRequestId,
            'callback_result_code' =>
                $resultCode,
            'callback_result_description' =>
                $resultDescription,
            'daraja_callback' =>
                $payload,
        ];

        if ($resultCode === 0) {
            $this->processSuccessfulCallback(
                $payment,
                $callback,
                $callbackMetadata
            );

            return;
        }

        if ($resultCode === 1032) {
            $this->paymentService->cancelPayment(
                $payment,
                array_merge(
                    $callbackMetadata,
                    [
                        'callback_outcome' => 'cancelled',
                    ]
                )
            );

            return;
        }

        $this->paymentService->failPayment(
            $payment,
            array_merge(
                $callbackMetadata,
                [
                    'callback_outcome' => 'failed',
                ]
            )
        );
    }

    /**
     * Process a successful M-Pesa callback.
     */
    private function processSuccessfulCallback(
        Payment $payment,
        array $callback,
        array $callbackMetadata
    ): void {
        $metadataItems = data_get(
            $callback,
            'CallbackMetadata.Item',
            []
        );

        $callbackValues = $this->mapCallbackMetadata(
            $metadataItems
        );

        $amount = $callbackValues['Amount'] ?? null;
        $receipt = $callbackValues['MpesaReceiptNumber'] ?? null;
        $transactionDate =
            $callbackValues['TransactionDate'] ?? null;
        $phone = $callbackValues['PhoneNumber'] ?? null;

        if (!$receipt) {
            Log::error(
                'M-Pesa success callback missing receipt number.',
                [
                    'payment_id' => $payment->id,
                    'callback' => $callback,
                ]
            );

            $this->paymentService->failPayment(
                $payment,
                array_merge(
                    $callbackMetadata,
                    [
                        'callback_outcome' => 'failed_validation',
                        'callback_validation_error' =>
                            'Missing MpesaReceiptNumber.',
                    ]
                )
            );

            return;
        }

        if (
            $amount === null ||
            round((float) $amount, 2)
            !== round((float) $payment->amount, 2)
        ) {
            Log::error(
                'M-Pesa callback amount does not match payment.',
                [
                    'payment_id' => $payment->id,
                    'expected_amount' => $payment->amount,
                    'callback_amount' => $amount,
                ]
            );

            $this->paymentService->failPayment(
                $payment,
                array_merge(
                    $callbackMetadata,
                    [
                        'callback_outcome' => 'failed_validation',
                        'callback_validation_error' =>
                            'Callback amount does not match payment amount.',
                        'callback_amount' => $amount,
                    ]
                )
            );

            return;
        }

        $expectedPhone = data_get(
            $payment->metadata,
            'phone_number'
        );

        if ($phone && $expectedPhone) {
            try {
                $callbackPhone = $this->normalizeKenyanPhone(
                    (string) $phone
                );

                $storedPhone = $this->normalizeKenyanPhone(
                    (string) $expectedPhone
                );

                if ($callbackPhone !== $storedPhone) {
                    Log::error(
                        'M-Pesa callback phone number does not match payment.',
                        [
                            'payment_id' => $payment->id,
                            'expected_phone' => $storedPhone,
                            'callback_phone' => $callbackPhone,
                        ]
                    );

                    $this->paymentService->failPayment(
                        $payment,
                        array_merge(
                            $callbackMetadata,
                            [
                                'callback_outcome' =>
                                    'failed_validation',
                                'callback_validation_error' =>
                                    'Callback phone number does not match.',
                                'callback_phone' => $callbackPhone,
                            ]
                        )
                    );

                    return;
                }
            } catch (Throwable $e) {
                $this->paymentService->failPayment(
                    $payment,
                    array_merge(
                        $callbackMetadata,
                        [
                            'callback_outcome' =>
                                'failed_validation',
                            'callback_validation_error' =>
                                'Invalid callback phone number.',
                            'callback_phone' => $phone,
                        ]
                    )
                );

                return;
            }
        }

        $metadata = array_merge(
            $callbackMetadata,
            [
                'callback_outcome' => 'completed',
                'mpesa_receipt_number' => $receipt,
                'callback_amount' => $amount,
                'callback_phone_number' => $phone,
                'transaction_date' => $transactionDate,
            ]
        );

        $this->paymentService->completePayment(
            $payment,
            $receipt,
            $metadata
        );
    }

    /**
     * Get a Daraja OAuth access token.
     */
    private function getAccessToken(): string
    {
        $consumerKey = (string) config(
            'services.mpesa.consumer_key'
        );

        $consumerSecret = (string) config(
            'services.mpesa.consumer_secret'
        );

        if (
            $consumerKey === '' ||
            $consumerSecret === ''
        ) {
            throw new \RuntimeException(
                'M-Pesa consumer key or consumer secret is not configured.'
            );
        }

        $oauthUrl = config(
            'services.mpesa.oauth_url'
        );

        if (!$oauthUrl) {
            throw new \RuntimeException(
                'M-Pesa OAuth URL is not configured.'
            );
        }

        $response = Http::timeout(
            (int) config(
                'services.mpesa.timeout',
                30
            )
        )
            ->withBasicAuth(
                $consumerKey,
                $consumerSecret
            )
            ->acceptJson()
            ->get(
                $oauthUrl,
                [
                    'grant_type' => 'client_credentials',
                ]
            );

        if (!$response->successful()) {
            Log::error('M-Pesa OAuth request failed.', [
                'http_status' => $response->status(),
                'response_body' => $response->body(),
            ]);

            throw new \RuntimeException(
                'Unable to obtain M-Pesa OAuth token. HTTP status '
                . $response->status()
                . '.'
            );
        }

        $accessToken = $response->json('access_token');

        if (
            !is_string($accessToken) ||
            trim($accessToken) === ''
        ) {
            throw new \RuntimeException(
                'M-Pesa OAuth response did not contain an access token.'
            );
        }

        return $accessToken;
    }

    /**
     * Normalize a Kenyan mobile number to 254XXXXXXXXX.
     */
    private function normalizeKenyanPhone(string $phone): string
    {
        $phone = trim($phone);

        $phone = preg_replace(
            '/[^0-9+]/',
            '',
            $phone
        );

        if (!$phone) {
            throw ValidationException::withMessages([
                'phone' => 'A valid Kenyan mobile number is required.',
            ]);
        }

        if (str_starts_with($phone, '+254')) {
            $phone = substr($phone, 1);
        }

        if (
            str_starts_with($phone, '0') &&
            strlen($phone) === 10
        ) {
            $phone = '254' . substr($phone, 1);
        }

        if (
            !preg_match(
                '/^254(?:7|1)[0-9]{8}$/',
                $phone
            )
        ) {
            throw ValidationException::withMessages([
                'phone' =>
                    'Enter a valid Kenyan mobile number, for example 0712345678.',
            ]);
        }

        return $phone;
    }

    /**
     * Generate a Daraja-compatible account reference.
     */
    private function makeAccountReference(RentInvoice $invoice): string
    {
        return 'KR' . str_pad(
            (string) $invoice->id,
            10,
            '0',
            STR_PAD_LEFT
        );
    }

    /**
     * Convert CallbackMetadata.Item into an associative array.
     */
    private function mapCallbackMetadata(array $items): array
    {
        $values = [];

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            if (!isset($item['Name'])) {
                continue;
            }

            $values[$item['Name']] = $item['Value'] ?? null;
        }

        return $values;
    }

    /**
     * Mark an STK initiation failure.
     */
    private function markInitiationFailed(
        Payment $payment,
        Response $response
    ): void {
        try {
            $this->paymentService->failPayment(
                $payment,
                [
                    'mpesa_initiation_failed' => true,
                    'http_status' => $response->status(),
                    'daraja_response' => $response->json(),
                ]
            );
        } catch (Throwable $e) {
            Log::error(
                'Unable to mark failed M-Pesa initiation.',
                [
                    'payment_id' => $payment->id,
                    'error' => $e->getMessage(),
                ]
            );
        }
    }
}