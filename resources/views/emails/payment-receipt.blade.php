<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>Payment Receipt</title>
</head>

<body style="margin:0; padding:0; background:#f4f6f8; font-family:Arial, Helvetica, sans-serif;">

    <div style="max-width:600px; margin:40px auto; background:#ffffff; border-radius:8px; padding:32px;">

        <h2 style="margin-top:0;">
            Payment Received
        </h2>

        <p>
            Dear {{ $payment->receipt?->payment?->rentInvoice?->lease?->tenant?->full_name ?? 'Tenant' }},
        </p>

        <p>
            Your payment has been successfully received and recorded.
        </p>

        <p>
            Your official payment receipt is attached to this email as a PDF.
        </p>

        <p>
            You can also verify the authenticity of your receipt using the
            verification information and QR code contained in the receipt.
        </p>

        <p style="margin-top:30px;">
            Thank you.
        </p>

        <p>
            <strong>KopsRent</strong>
        </p>

    </div>

</body>
</html>