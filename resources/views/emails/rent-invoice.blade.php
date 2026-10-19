<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent Invoice</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f6f8; font-family:Arial, Helvetica, sans-serif; color:#333333;">

    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#f4f6f8; padding:30px 10px;">
        <tr>
            <td align="center">
                <table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:600px; background-color:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 4px 15px rgba(0,0,0,0.06);">
                    
                    {{-- Header --}}
                    <tr>
                        <td style="padding:28px 32px; background: {{ ($isOverdue || (float)$invoice->penalty_amount > 0) ? 'linear-gradient(135deg, #dc3545, #b02a37)' : ($isReminder ? 'linear-gradient(135deg, #0d6efd, #0b5ed7)' : 'linear-gradient(135deg, #198754, #146c43)') }}; color:#ffffff;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td>
                                        <h1 style="margin:0; font-size:22px; font-weight:700; letter-spacing:-0.5px;">
                                            @if($isOverdue || (float)$invoice->penalty_amount > 0)
                                                Overdue Rent Notice
                                            @elseif($isReminder)
                                                Rent Reminder Notice
                                            @else
                                                Rent Invoice Issued
                                            @endif
                                        </h1>
                                        <p style="margin:6px 0 0; font-size:13px; opacity:0.9;">
                                            Invoice #{{ $invoice->invoice_number }}
                                        </p>
                                    </td>
                                    <td align="right" valign="top">
                                        <span style="display:inline-block; padding:5px 12px; background:rgba(255,255,255,0.2); border-radius:20px; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px;">
                                            @if($isOverdue || (float)$invoice->penalty_amount > 0)
                                                Late Fee Applied
                                            @elseif($isReminder)
                                                Payment Due
                                            @else
                                                New Invoice
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:32px;">
                            
                            <p style="font-size:15px; margin-top:0; margin-bottom:20px;">
                                Dear <strong>{{ $invoice->lease?->tenant?->full_name ?? 'Tenant' }}</strong>,
                            </p>

                            @if($isOverdue || (float)$invoice->penalty_amount > 0)
                                <div style="background-color:#fff5f5; border-left:4px solid #dc3545; padding:14px 16px; margin-bottom:24px; border-radius:0 8px 8px 0;">
                                    <p style="margin:0; font-size:14px; color:#b02a37; font-weight:600;">
                                        ⚠️ Important: Your rent is overdue.
                                    </p>
                                    <p style="margin:4px 0 0; font-size:13px; color:#5c1d24;">
                                        A 10% late penalty of <strong>{{ \App\Support\Currency::format((float)$invoice->penalty_amount, $invoice->currency) }}</strong> has been added to your invoice as per property terms. Please settle the total amount due immediately.
                                    </p>
                                </div>
                            @elseif($isReminder)
                                <div style="background-color:#f0f7ff; border-left:4px solid #0d6efd; padding:14px 16px; margin-bottom:24px; border-radius:0 8px 8px 0;">
                                    <p style="margin:0; font-size:13px; color:#084298;">
                                        This is a gentle reminder that your rent payment for <strong>{{ $invoice->formatted_billing_period }}</strong> is due on or before <strong>{{ $invoice->due_date ? $invoice->due_date->format('d M Y') : 'the due date' }}</strong>.
                                    </p>
                                </div>
                            @else
                                <p style="font-size:14px; color:#4a5568; line-height:1.5; margin-bottom:24px;">
                                    Your new rent invoice for <strong>{{ $invoice->formatted_billing_period }}</strong> has been generated and is ready for payment.
                                </p>
                            @endif

                            {{-- Invoice Details Card --}}
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; margin-bottom:24px; overflow:hidden;">
                                <tr>
                                    <td style="padding:16px 20px; border-bottom:1px solid #e2e8f0; font-size:13px; color:#64748b; font-weight:600; text-transform:uppercase; letter-spacing:0.5px;">
                                        Invoice Summary
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:20px;">
                                        <table border="0" cellpadding="6" cellspacing="0" width="100%" style="font-size:14px; color:#334155;">
                                            <tr>
                                                <td width="40%" style="color:#64748b;">Invoice Number:</td>
                                                <td width="60%" style="font-weight:600;">{{ $invoice->invoice_number }}</td>
                                            </tr>
                                            <tr>
                                                <td style="color:#64748b;">Billing Period:</td>
                                                <td style="font-weight:600;">{{ $invoice->formatted_billing_period }}</td>
                                            </tr>
                                            <tr>
                                                <td style="color:#64748b;">Property / Unit:</td>
                                                <td>
                                                    {{ $invoice->lease?->unit?->property?->name ?? 'Property' }} - Unit {{ $invoice->lease?->unit?->unit_number ?? 'N/A' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="color:#64748b;">Due Date:</td>
                                                <td style="font-weight:600; color: {{ ($isOverdue || (float)$invoice->penalty_amount > 0) ? '#dc3545' : '#334155' }};">
                                                    {{ $invoice->due_date ? $invoice->due_date->format('d M Y') : 'N/A' }}
                                                </td>
                                            </tr>
                                            <tr><td colspan="2"><hr style="border:0; border-top:1px dashed #cbd5e1; margin:6px 0;"></td></tr>
                                            <tr>
                                                <td style="color:#64748b;">Base Monthly Rent:</td>
                                                <td>{{ \App\Support\Currency::format((float)$invoice->amount - (float)$invoice->penalty_amount, $invoice->currency) }}</td>
                                            </tr>
                                            @if((float)$invoice->penalty_amount > 0)
                                                <tr>
                                                    <td style="color:#dc3545; font-weight:600;">Late Penalty (10%):</td>
                                                    <td style="color:#dc3545; font-weight:600;">+ {{ \App\Support\Currency::format((float)$invoice->penalty_amount, $invoice->currency) }}</td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td style="color:#64748b; font-weight:600;">Total Amount Due:</td>
                                                <td style="font-weight:700; font-size:16px; color:#0f172a;">{{ $invoice->formatted_amount }}</td>
                                            </tr>
                                            @if((float)$invoice->amount_paid > 0)
                                                <tr>
                                                    <td style="color:#198754;">Amount Paid So Far:</td>
                                                    <td style="color:#198754; font-weight:600;">- {{ $invoice->formatted_paid }}</td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td style="color:#b02a37; font-weight:700;">Balance Remaining:</td>
                                                <td style="color:#b02a37; font-weight:700; font-size:17px;">{{ $invoice->formatted_outstanding }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <p style="font-size:13px; color:#64748b; line-height:1.5; margin-bottom:24px;">
                                Daily automated reminders will be sent until your full payment is recorded. Once payment is received, reminders will automatically stop.
                            </p>

                            {{-- Footer Note --}}
                            <p style="font-size:14px; margin-top:24px; margin-bottom:4px;">
                                Thank you,
                            </p>
                            <p style="font-size:15px; font-weight:700; color:#0f172a; margin:0;">
                                KopsRent Management
                            </p>

                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:20px 32px; background-color:#f1f5f9; border-top:1px solid #e2e8f0; text-align:center; font-size:12px; color:#94a3b8;">
                            This is an automated notification from KopsRent Real Estate System. Please do not reply directly to this email.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>
</html>
