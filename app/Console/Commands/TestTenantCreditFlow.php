<?php

namespace App\Console\Commands;

use App\Models\Lease;
use App\Models\Payment;
use App\Models\Property;
use App\Models\RentInvoice;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use App\Services\PaymentService;
use App\Services\TenantCreditService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Throwable;

class TestTenantCreditFlow extends Command
{
    protected $signature = 'test:tenant-credit
                            {--keep : Keep the temporary test data after the test}';

    protected $description =
        'Test tenant credit creation and automatic application to a future invoice.';

    public function __construct(
        private readonly PaymentService $paymentService,
        private readonly TenantCreditService $tenantCreditService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Starting tenant credit flow test...');
        $this->newLine();

        $tenant = null;
        $property = null;
        $unit = null;
        $lease = null;
        $firstInvoice = null;
        $secondInvoice = null;
        $payment = null;

        try {
            DB::transaction(function () use (
                &$tenant,
                &$property,
                &$unit,
                &$lease,
                &$firstInvoice,
                &$secondInvoice,
                &$payment
            ) {
                /*
                 * ---------------------------------------------------------
                 * 1. Find an existing user to own the temporary property.
                 * ---------------------------------------------------------
                 */
                $owner = User::query()->first();

                if (!$owner) {
                    throw new RuntimeException(
                        'No user exists. Create at least one user first.'
                    );
                }

                /*
                 * ---------------------------------------------------------
                 * 2. Create temporary tenant.
                 * ---------------------------------------------------------
                 */
                $unique = uniqid('credit_test_', true);

                $tenant = Tenant::create([
                    'full_name' =>
                        'TEST Credit Tenant',

                    'national_id' =>
                        'TEST-' . $unique,

                    'phone' =>
                        '+254700000000',

                    'email' =>
                        $unique . '@example.test',

                    'address' =>
                        'Development Test Address',

                    'emergency_contact_name' =>
                        'Test Emergency Contact',

                    'emergency_contact_phone' =>
                        '+254711111111',

                    'status' =>
                        'active',
                ]);

                /*
                 * ---------------------------------------------------------
                 * 3. Create temporary property.
                 * ---------------------------------------------------------
                 */
                $property = Property::create([
                    'owner_id' =>
                        $owner->id,

                    'name' =>
                        'TEST Credit Property',

                    'address' =>
                        'Development Test Property',

                    'city' =>
                        'Nairobi',

                    'description' =>
                        'Temporary property for tenant credit testing.',
                ]);

                /*
                 * ---------------------------------------------------------
                 * 4. Create temporary unit.
                 * ---------------------------------------------------------
                 */
                $unit = Unit::create([
                    'property_id' =>
                        $property->id,

                    'unit_number' =>
                        'TEST-' . strtoupper(
                            substr(
                                uniqid(),
                                -4
                            )
                        ),

                    'floor' =>
                        1,

                    'bedrooms' =>
                        2,

                    'bathrooms' =>
                        1,

                    'monthly_rent' =>
                        10000,

                    'currency' =>
                        'KES',

                    'status' =>
                        'occupied',
                ]);

                /*
                 * ---------------------------------------------------------
                 * 5. Create active lease.
                 * ---------------------------------------------------------
                 */
                $lease = Lease::create([
                    'tenant_id' =>
                        $tenant->id,

                    'unit_id' =>
                        $unit->id,

                    'start_date' =>
                        now()->startOfMonth()->toDateString(),

                    'end_date' =>
                        now()
                            ->addYear()
                            ->endOfMonth()
                            ->toDateString(),

                    'monthly_rent' =>
                        10000,

                    'currency' =>
                        'KES',

                    'security_deposit' =>
                        0,

                    'payment_due_day' =>
                        5,

                    'status' =>
                        'active',
                ]);

                /*
                 * ---------------------------------------------------------
                 * 6. Create first invoice.
                 *
                 * Invoice: KES 10,000
                 * Payment: KES 15,000
                 *
                 * Expected:
                 * - KES 10,000 allocated to invoice
                 * - KES 5,000 becomes tenant credit
                 * ---------------------------------------------------------
                 */
                $firstInvoice = RentInvoice::create([
                    'lease_id' =>
                        $lease->id,

                    'invoice_number' =>
                        'TEST-CREDIT-' .
                        strtoupper(
                            substr(
                                uniqid(),
                                -8
                            )
                        ),

                    'billing_period' =>
                        now()
                            ->startOfMonth()
                            ->toDateString(),

                    'invoice_date' =>
                        now()->toDateString(),

                    'due_date' =>
                        now()
                            ->addDays(5)
                            ->toDateString(),

                    'amount' =>
                        10000,

                    'currency' =>
                        'KES',

                    'amount_paid' =>
                        0,

                    'status' =>
                        RentInvoice::STATUS_UNPAID,

                    'notes' =>
                        'Temporary invoice for tenant credit testing.',
                ]);

                $payment = $this->paymentService
                    ->createManualPayment(
                        $firstInvoice,
                        [
                            'amount' =>
                                15000,

                            'payment_date' =>
                                now()->toDateString(),

                            'payment_method' =>
                                Payment::METHOD_MPESA,

                            'transaction_reference' =>
                                'TEST-CREDIT-' .
                                strtoupper(
                                    substr(
                                        uniqid(),
                                        -10
                                    )
                                ),

                            'notes' =>
                                'Temporary payment for tenant credit testing.',
                        ]
                    );

                /*
                 * ---------------------------------------------------------
                 * 7. Verify first invoice.
                 * ---------------------------------------------------------
                 */
                $firstInvoice->refresh();

                $this->assertEquals(
                    10000.00,
                    round(
                        $firstInvoice->getTotalPaid(),
                        2
                    ),
                    'First invoice should have KES 10,000 paid.'
                );

                $this->assertEquals(
                    0.00,
                    round(
                        $firstInvoice->outstanding_amount,
                        2
                    ),
                    'First invoice should have no outstanding balance.'
                );

                $this->assertEquals(
                    RentInvoice::STATUS_PAID,
                    $firstInvoice->status,
                    'First invoice should be marked paid.'
                );

                /*
                 * ---------------------------------------------------------
                 * 8. Verify KES 5,000 tenant credit was created.
                 * ---------------------------------------------------------
                 */
                $credit = $tenant->credits()
                    ->where(
                        'payment_id',
                        $payment->id
                    )
                    ->first();

                if (!$credit) {
                    throw new RuntimeException(
                        'Expected tenant credit was not created.'
                    );
                }

                $this->assertEquals(
                    5000.00,
                    round(
                        (float) $credit->amount,
                        2
                    ),
                    'Tenant credit should be KES 5,000.'
                );

                $this->assertEquals(
                    5000.00,
                    round(
                        (float) $credit->remaining_amount,
                        2
                    ),
                    'Tenant credit should initially have KES 5,000 remaining.'
                );

                $this->assertEquals(
                    TenantCredit::STATUS_AVAILABLE,
                    $credit->status,
                    'Tenant credit should initially be available.'
                );

                /*
                 * ---------------------------------------------------------
                 * 9. Create second invoice.
                 *
                 * Expected:
                 * - KES 5,000 credit automatically applied
                 * - KES 5,000 remains outstanding
                 * - Invoice becomes partially paid
                 * ---------------------------------------------------------
                 */
                $secondInvoice = RentInvoice::create([
                    'lease_id' =>
                        $lease->id,

                    'invoice_number' =>
                        'TEST-CREDIT-' .
                        strtoupper(
                            substr(
                                uniqid(),
                                -8
                            )
                        ),

                    'billing_period' =>
                        now()
                            ->addMonth()
                            ->startOfMonth()
                            ->toDateString(),

                    'invoice_date' =>
                        now()
                            ->addMonth()
                            ->startOfMonth()
                            ->toDateString(),

                    'due_date' =>
                        now()
                            ->addMonth()
                            ->startOfMonth()
                            ->addDays(5)
                            ->toDateString(),

                    'amount' =>
                        10000,

                    'currency' =>
                        'KES',

                    'amount_paid' =>
                        0,

                    'status' =>
                        RentInvoice::STATUS_UNPAID,

                    'notes' =>
                        'Temporary second invoice for tenant credit testing.',
                ]);

                $applied = $this->tenantCreditService
                    ->applyCreditToInvoice(
                        $secondInvoice
                    );

                /*
                 * ---------------------------------------------------------
                 * 10. Verify credit application.
                 * ---------------------------------------------------------
                 */
                $secondInvoice->refresh();

                $this->assertEquals(
                    5000.00,
                    round(
                        $applied,
                        2
                    ),
                    'Exactly KES 5,000 should have been applied.'
                );

                $this->assertEquals(
                    5000.00,
                    round(
                        $secondInvoice->getTotalPaid(),
                        2
                    ),
                    'Second invoice should have KES 5,000 paid.'
                );

                $this->assertEquals(
                    5000.00,
                    round(
                        $secondInvoice->outstanding_amount,
                        2
                    ),
                    'Second invoice should have KES 5,000 outstanding.'
                );

                $this->assertEquals(
                    RentInvoice::STATUS_PARTIALLY_PAID,
                    $secondInvoice->status,
                    'Second invoice should be partially paid.'
                );

                /*
                 * ---------------------------------------------------------
                 * 11. Verify credit was fully consumed.
                 * ---------------------------------------------------------
                 */
                $credit->refresh();

                $this->assertEquals(
                    0.00,
                    round(
                        (float) $credit->remaining_amount,
                        2
                    ),
                    'Tenant credit should have zero remaining balance.'
                );

                $this->assertEquals(
                    TenantCredit::STATUS_USED,
                    $credit->status,
                    'Tenant credit should be marked used.'
                );

                /*
                 * ---------------------------------------------------------
                 * 12. Verify credit allocation exists.
                 * ---------------------------------------------------------
                 */
                $allocation = $credit->allocations()
                    ->where(
                        'rent_invoice_id',
                        $secondInvoice->id
                    )
                    ->first();

                if (!$allocation) {
                    throw new RuntimeException(
                        'Expected tenant credit allocation was not created.'
                    );
                }

                $this->assertEquals(
                    5000.00,
                    round(
                        (float) $allocation->amount,
                        2
                    ),
                    'Credit allocation should be KES 5,000.'
                );
            });

            $this->newLine();
            $this->info(
                'Tenant credit flow test passed successfully.'
            );

            $this->newLine();

            $this->line(
                'First invoice: KES 10,000'
            );

            $this->line(
                'Payment received: KES 15,000'
            );

            $this->line(
                'Tenant credit created: KES 5,000'
            );

            $this->line(
                'Second invoice: KES 10,000'
            );

            $this->line(
                'Credit applied: KES 5,000'
            );

            $this->line(
                'Second invoice outstanding: KES 5,000'
            );

            $this->newLine();

            if ($this->option('keep')) {
                $this->warn(
                    'Temporary test data was kept because --keep was supplied.'
                );
            } else {
                $this->cleanupTestData(
                    $tenant?->id,
                    $property?->id,
                    $unit?->id,
                    $lease?->id,
                    $firstInvoice?->id,
                    $secondInvoice?->id,
                    $payment?->id
                );

                $this->info(
                    'Temporary test data cleaned up.'
                );
            }

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->newLine();

            $this->error(
                'Tenant credit flow test failed.'
            );

            $this->error(
                $e->getMessage()
            );

            $this->newLine();

            $this->line(
                'If this error references another required database field, '
                . 'we will adjust the test data to match your existing schema.'
            );

            return self::FAILURE;
        }
    }

    private function cleanupTestData(
        ?int $tenantId,
        ?int $propertyId,
        ?int $unitId,
        ?int $leaseId,
        ?int $firstInvoiceId,
        ?int $secondInvoiceId,
        ?int $paymentId
    ): void {
        DB::transaction(function () use (
            $tenantId,
            $propertyId,
            $unitId,
            $leaseId,
            $firstInvoiceId,
            $secondInvoiceId,
            $paymentId
        ) {
            /*
             * Delete dependent records first.
             */
            if ($secondInvoiceId) {
                DB::table('tenant_credit_allocations')
                    ->where(
                        'rent_invoice_id',
                        $secondInvoiceId
                    )
                    ->delete();
            }

            if ($firstInvoiceId) {
                DB::table('payment_allocations')
                    ->where(
                        'rent_invoice_id',
                        $firstInvoiceId
                    )
                    ->delete();
            }

            if ($paymentId) {
                DB::table('tenant_credits')
                    ->where(
                        'payment_id',
                        $paymentId
                    )
                    ->delete();

                DB::table('payment_allocations')
                    ->where(
                        'payment_id',
                        $paymentId
                    )
                    ->delete();

                DB::table('payments')
                    ->where(
                        'id',
                        $paymentId
                    )
                    ->delete();
            }

            if ($firstInvoiceId) {
                DB::table('rent_invoices')
                    ->where(
                        'id',
                        $firstInvoiceId
                    )
                    ->delete();
            }

            if ($secondInvoiceId) {
                DB::table('rent_invoices')
                    ->where(
                        'id',
                        $secondInvoiceId
                    )
                    ->delete();
            }

            if ($leaseId) {
                DB::table('leases')
                    ->where(
                        'id',
                        $leaseId
                    )
                    ->delete();
            }

            if ($unitId) {
                DB::table('units')
                    ->where(
                        'id',
                        $unitId
                    )
                    ->delete();
            }

            if ($propertyId) {
                DB::table('properties')
                    ->where(
                        'id',
                        $propertyId
                    )
                    ->delete();
            }

            if ($tenantId) {
                DB::table('tenants')
                    ->where(
                        'id',
                        $tenantId
                    )
                    ->delete();
            }
        });
    }

    private function assertEquals(
        float|string|int $expected,
        float|string|int $actual,
        string $message
    ): void {
        if ($expected != $actual) {
            throw new RuntimeException(
                $message
                . ' Expected: '
                . $expected
                . ', Actual: '
                . $actual
                . '.'
            );
        }
    }
}