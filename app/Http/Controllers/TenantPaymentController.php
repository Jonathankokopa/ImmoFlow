<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenantPaymentController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $this->ensureTenant($user);

        $payments = Payment::query()
            ->whereHas('rentInvoice.lease', function ($query) use ($user) {
                $query->where(
                    'tenant_id',
                    $user->tenant_id
                );
            })
            ->with([
                'rentInvoice.lease.unit.property',
            ])
            ->latest('payment_date')
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('portal.payments.index', [
            'user' => $user,
            'tenant' => $user->tenant,
            'payments' => $payments,
        ]);
    }

    public function show(
        Request $request,
        Payment $payment
    ): View {
        $user = $request->user();

        $this->ensureTenant($user);

        abort_unless(
            $payment->rentInvoice &&
            $payment->rentInvoice->lease &&
            (int) $payment->rentInvoice->lease->tenant_id ===
                (int) $user->tenant_id,
            404
        );

        $payment->load([
            'rentInvoice.lease.tenant',
            'rentInvoice.lease.unit.property',
            'allocations',
            'tenantCredits',
            'receipt',
        ]);

        return view('portal.payments.show', [
            'user' => $user,
            'tenant' => $user->tenant,
            'payment' => $payment,
        ]);
    }

    private function ensureTenant($user): void
    {
        abort_unless(
            $user &&
            $user->is_active &&
            $user->isRole('tenant') &&
            $user->tenant_id,
            403
        );
    }
}