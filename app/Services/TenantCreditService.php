<?php

namespace App\Services;

use App\Models\RentInvoice;
use App\Models\TenantCredit;
use App\Models\TenantCreditAllocation;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TenantCreditService
{
    /**
     * Apply available tenant credit to an invoice.
     *
     * Only credits belonging to the invoice tenant
     * and matching the invoice currency are eligible.
     */
    public function applyCreditToInvoice(
        RentInvoice $invoice
    ): float {
        return DB::transaction(function () use ($invoice) {
            $invoice = RentInvoice::lockForUpdate()
                ->findOrFail($invoice->id);

            if (
                $invoice->status ===
                RentInvoice::STATUS_CANCELLED
            ) {
                return 0.0;
            }

            $tenantId = $invoice->lease()
                ->value('tenant_id');

            if (!$tenantId) {
                throw ValidationException::withMessages([
                    'invoice' =>
                        'The invoice does not belong to a valid tenant.',
                ]);
            }

            $outstanding = $invoice->outstanding_amount;

            if ($outstanding <= 0) {
                return 0.0;
            }

            $remaining = round(
                $outstanding,
                2
            );

            $credits = TenantCredit::query()
                ->where('tenant_id', $tenantId)
                ->where('currency', $invoice->currency)
                ->whereIn('status', [
                    TenantCredit::STATUS_AVAILABLE,
                    TenantCredit::STATUS_PARTIALLY_USED,
                ])
                ->where('remaining_amount', '>', 0)
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            $totalApplied = 0.0;

            foreach ($credits as $credit) {
                if ($remaining <= 0) {
                    break;
                }

                $creditRemaining = round(
                    (float) $credit->remaining_amount,
                    2
                );

                if ($creditRemaining <= 0) {
                    continue;
                }

                $allocationAmount = round(
                    min(
                        $remaining,
                        $creditRemaining
                    ),
                    2
                );

                if ($allocationAmount <= 0) {
                    continue;
                }

                TenantCreditAllocation::create([
                    'tenant_credit_id' =>
                        $credit->id,

                    'rent_invoice_id' =>
                        $invoice->id,

                    'amount' =>
                        $allocationAmount,
                ]);

                $credit->remaining_amount = round(
                    $creditRemaining - $allocationAmount,
                    2
                );

                if (
                    $credit->remaining_amount <= 0
                ) {
                    $credit->remaining_amount = 0;

                    $credit->status =
                        TenantCredit::STATUS_USED;
                } else {
                    $credit->status =
                        TenantCredit::STATUS_PARTIALLY_USED;
                }

                $credit->save();

                $remaining = round(
                    $remaining - $allocationAmount,
                    2
                );

                $totalApplied = round(
                    $totalApplied + $allocationAmount,
                    2
                );
            }

            if ($totalApplied > 0) {
                $invoice->recalculatePaymentStatus();
            }

            return $totalApplied;
        });
    }

    /**
     * Get the tenant's available credit in a currency.
     */
    public function getAvailableCredit(
        int $tenantId,
        string $currency
    ): float {
        return round(
            (float) TenantCredit::query()
                ->where('tenant_id', $tenantId)
                ->where('currency', $currency)
                ->whereIn('status', [
                    TenantCredit::STATUS_AVAILABLE,
                    TenantCredit::STATUS_PARTIALLY_USED,
                ])
                ->where('remaining_amount', '>', 0)
                ->sum('remaining_amount'),
            2
        );
    }
}