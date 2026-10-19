<?php

namespace App\Console\Commands;

use App\Mail\RentInvoiceMail;
use App\Models\Lease;
use App\Models\RentInvoice;
use App\Services\TenantCreditService;
use App\Support\Currency;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcessRentRemindersAndPenalties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rent:process-reminders {--generate-invoices : Force generation of missing monthly invoices}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-generate monthly rent invoices, process daily rent reminders, check overdue status, apply 10% late penalties after the 5th, and email unpaid tenants.';

    public function __construct(
        private readonly TenantCreditService $tenantCreditService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $today = Carbon::now();
        $this->info("Processing automated rent workflow for date: {$today->toDateString()}");

        // 1. Auto-generate missing monthly invoices for active leases
        $this->autoGenerateMonthlyInvoices($today);

        // 2. Process reminders and penalties for unpaid invoices
        $this->processRemindersAndPenalties($today);

        return Command::SUCCESS;
    }

    /**
     * Automatically generate rent invoices for active leases for the current billing period.
     */
    private function autoGenerateMonthlyInvoices(Carbon $today): void
    {
        $currentBillingPeriod = $today->copy()->startOfMonth()->toDateString();
        $defaultDueDate = $today->copy()->startOfMonth()->addDays(4)->toDateString(); // 5th of the month

        $activeLeases = Lease::where('status', 'active')
            ->whereNotNull('monthly_rent')
            ->where('monthly_rent', '>', 0)
            ->whereIn('currency', Currency::SUPPORTED)
            ->get();

        $generatedCount = 0;

        foreach ($activeLeases as $lease) {
            // Check if invoice already exists for this billing period
            $exists = RentInvoice::where('lease_id', $lease->id)
                ->where('billing_period', $currentBillingPeriod)
                ->exists();

            if ($exists) {
                continue;
            }

            try {
                $invoice = null;

                DB::transaction(function () use ($lease, $currentBillingPeriod, $defaultDueDate, &$invoice) {
                    $invoiceNumber = $this->generateInvoiceNumber();

                    $invoice = RentInvoice::create([
                        'lease_id' => $lease->id,
                        'invoice_number' => $invoiceNumber,
                        'billing_period' => $currentBillingPeriod,
                        'invoice_date' => now()->toDateString(),
                        'due_date' => $defaultDueDate,
                        'amount' => round((float) $lease->monthly_rent, 2),
                        'penalty_amount' => 0,
                        'amount_paid' => 0,
                        'currency' => strtoupper((string) $lease->currency),
                        'status' => RentInvoice::STATUS_UNPAID,
                    ]);

                    $this->tenantCreditService->applyCreditToInvoice($invoice);
                });

                if ($invoice) {
                    $generatedCount++;
                    $this->info("Generated Invoice #{$invoice->invoice_number} for Lease #{$lease->id}");

                    // Immediately email newly created invoice to tenant
                    $tenant = $lease->tenant;
                    if ($tenant && $tenant->email) {
                        try {
                            Mail::to($tenant->email)->send(new RentInvoiceMail(
                                invoice: $invoice,
                                isReminder: false,
                                isOverdue: false
                            ));
                            $this->info("Emailed new Invoice #{$invoice->invoice_number} to {$tenant->email}");
                        } catch (\Throwable $e) {
                            Log::error("Failed to email new invoice #{$invoice->invoice_number}: " . $e->getMessage());
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::error("Failed to auto-generate invoice for lease #{$lease->id}: " . $e->getMessage());
            }
        }

        if ($generatedCount > 0) {
            $this->info("Auto-generated {$generatedCount} new monthly rent invoices.");
        }
    }

    /**
     * Process daily reminders and apply late penalties after the 5th of the month.
     */
    private function processRemindersAndPenalties(Carbon $today): void
    {
        $unpaidInvoices = RentInvoice::with(['lease.tenant', 'lease.unit.property'])
            ->whereIn('status', [
                RentInvoice::STATUS_UNPAID,
                RentInvoice::STATUS_PARTIALLY_PAID,
                RentInvoice::STATUS_OVERDUE,
            ])
            ->get();

        $remindersSent = 0;
        $penaltiesApplied = 0;

        foreach ($unpaidInvoices as $invoice) {
            // Re-verify if invoice has been fully paid or cancelled
            if ($invoice->isFullyPaid() || $invoice->status === RentInvoice::STATUS_PAID || $invoice->status === RentInvoice::STATUS_CANCELLED) {
                continue;
            }

            $tenant = $invoice->lease?->tenant;
            if (!$tenant || !$tenant->email) {
                continue;
            }

            /*
             * Late Penalty Logic:
             * After the 5th (i.e. day > 5) or if current date is after due date,
             * apply 10% late penalty if not already applied.
             */
            $isOverdue = false;

            if ($today->day > 5 || ($invoice->due_date && $today->startOfDay()->gt($invoice->due_date->copy()->startOfDay()))) {
                $isOverdue = true;

                if ((float) $invoice->penalty_amount <= 0) {
                    $baseRent = (float) $invoice->amount;
                    $penalty = round($baseRent * 0.10, 2);

                    $invoice->penalty_amount = $penalty;
                    $invoice->amount = round($baseRent + $penalty, 2);
                    $invoice->status = RentInvoice::STATUS_OVERDUE;
                    $invoice->save();

                    $penaltiesApplied++;
                    $this->info("Applied 10% penalty ({$penalty} {$invoice->currency}) to Invoice #{$invoice->invoice_number}. New Amount: {$invoice->amount}");
                } else {
                    if ($invoice->status !== RentInvoice::STATUS_OVERDUE) {
                        $invoice->status = RentInvoice::STATUS_OVERDUE;
                        $invoice->save();
                    }
                }
            }

            /*
             * Send daily reminder email to unpaid tenant
             */
            try {
                Mail::to($tenant->email)->send(new RentInvoiceMail(
                    invoice: $invoice,
                    isReminder: true,
                    isOverdue: $isOverdue
                ));

                $remindersSent++;
                $this->info("Sent daily reminder for Invoice #{$invoice->invoice_number} to {$tenant->email}");
            } catch (\Throwable $e) {
                Log::error("Failed to send reminder email for Invoice #{$invoice->invoice_number}: " . $e->getMessage());
            }
        }

        $this->info("Reminders process complete. Reminders sent: {$remindersSent}, Penalties applied: {$penaltiesApplied}.");
    }

    /**
     * Generate next invoice number.
     */
    private function generateInvoiceNumber(): string
    {
        $year = now()->year;

        $latestInvoice = RentInvoice::query()
            ->where('invoice_number', 'like', "INV-{$year}-%")
            ->orderByDesc('invoice_number')
            ->lockForUpdate()
            ->first();

        if (!$latestInvoice) {
            $nextNumber = 1;
        } else {
            $lastNumber = (int) substr(
                $latestInvoice->invoice_number,
                strrpos($latestInvoice->invoice_number, '-') + 1
            );
            $nextNumber = $lastNumber + 1;
        }

        return sprintf('INV-%d-%06d', $year, $nextNumber);
    }
}
