<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\RentInvoice;
use App\Models\TenantCredit;
use App\Models\User;
use App\Support\Currency;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class TenantPortalController extends Controller
{
    /**
     * Tenant dashboard.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $this->ensureTenant($user);

        $tenant = $user->tenant;

        abort_unless($tenant, 404);

        /*
        |--------------------------------------------------------------------------
        | Load tenant leases
        |--------------------------------------------------------------------------
        */

        $tenant->load([
            'leases.unit.property',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Get active lease
        |--------------------------------------------------------------------------
        |
        | The tenant portal should only use the currently active lease.
        |
        */

        $activeLease = $tenant->leases
            ->filter(function ($lease) {
                return strtolower((string) $lease->status) === 'active';
            })
            ->sortByDesc('id')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | Current month
        |--------------------------------------------------------------------------
        */

        $currentMonth = Carbon::now()->startOfMonth();

        $currentMonthName = $currentMonth->format('F Y');

        /*
        |--------------------------------------------------------------------------
        | Current month's invoice
        |--------------------------------------------------------------------------
        |
        | The invoice is the financial source of truth.
        |
        | We do NOT create a fake invoice if one does not exist.
        |
        */

        $currentInvoice = null;

        if ($activeLease) {
            $currentInvoice = RentInvoice::query()
                ->where('lease_id', $activeLease->id)
                ->whereDate(
                    'billing_period',
                    $currentMonth->toDateString()
                )
                ->first();
        }

        /*
        |--------------------------------------------------------------------------
        | Current invoice currency
        |--------------------------------------------------------------------------
        |
        | There is deliberately NO:
        |
        | ?? 'USD'
        | ?? 'KES'
        | ?: 'USD'
        |
        | If there is no invoice or the currency is invalid,
        | the dashboard currency remains null.
        |
        */

        $dashboardCurrency = null;

        if ($currentInvoice) {
            $invoiceCurrency = strtoupper(
                (string) $currentInvoice->currency
            );

            if (in_array(
                $invoiceCurrency,
                Currency::SUPPORTED,
                true
            )) {
                $dashboardCurrency = $invoiceCurrency;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Current invoice amounts
        |--------------------------------------------------------------------------
        */

        $currentInvoiceAmount = 0.00;

        $currentInvoicePaid = 0.00;

        $currentInvoiceOutstanding = 0.00;

        if ($currentInvoice) {
            $currentInvoiceAmount = round(
                (float) $currentInvoice->amount,
                2
            );

            /*
             * RentInvoice calculates this from actual payment
             * allocations and tenant-credit allocations.
             */
            $currentInvoicePaid = round(
                (float) $currentInvoice->total_paid,
                2
            );

            $currentInvoiceOutstanding = round(
                (float) $currentInvoice->outstanding_amount,
                2
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Tenant credit / overpayment
        |--------------------------------------------------------------------------
        |
        | When a tenant pays more than the invoice amount,
        | the excess is stored as tenant credit.
        |
        | Credits are NEVER mixed between currencies.
        |
        */

        $tenantCreditAmount = 0.00;

        if ($dashboardCurrency !== null) {
            $tenantCreditAmount = TenantCredit::query()
                ->where('tenant_id', $tenant->id)
                ->where('currency', $dashboardCurrency)
                ->whereIn('status', [
                    TenantCredit::STATUS_AVAILABLE,
                    TenantCredit::STATUS_PARTIALLY_USED,
                ])
                ->sum('remaining_amount');

            $tenantCreditAmount = round(
                (float) $tenantCreditAmount,
                2
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Recent payments
        |--------------------------------------------------------------------------
        |
        | There is no Tenant::payments() relationship.
        |
        | Payment ownership is determined through:
        |
        | Payment
        |   -> RentInvoice
        |       -> Lease
        |           -> Tenant
        |
        */

        $recentPayments = Payment::query()
            ->with([
                'rentInvoice.lease.tenant',
            ])
            ->whereHas(
                'rentInvoice.lease',
                function ($query) use ($tenant) {
                    $query->where(
                        'tenant_id',
                        $tenant->id
                    );
                }
            )
            ->latest('payment_date')
            ->latest('created_at')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Open maintenance requests
        |--------------------------------------------------------------------------
        |
        | If your Tenant model has maintenanceRequests(), use it.
        | Otherwise the dashboard simply shows no open requests.
        |
        */

        $openMaintenance = collect();

        if (method_exists($tenant, 'maintenanceRequests')) {
            $openMaintenance = $tenant
                ->maintenanceRequests()
                ->whereNotIn('status', [
                    'completed',
                    'closed',
                    'cancelled',
                ])
                ->latest()
                ->limit(5)
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | Next payment due
        |--------------------------------------------------------------------------
        */

        $nextPaymentDue = null;

        if ($currentInvoice) {
            $nextPaymentDue = $currentInvoice->due_date;
        }

        /*
        |--------------------------------------------------------------------------
        | Dashboard view
        |--------------------------------------------------------------------------
        */

        return view('portal.dashboard', [
            'user' => $user,

            'tenant' => $tenant,

            'activeLease' => $activeLease,

            'currentMonth' => $currentMonth,

            'currentMonthName' => $currentMonthName,

            'currentInvoice' => $currentInvoice,

            'dashboardCurrency' => $dashboardCurrency,

            'currentInvoiceAmount' => $currentInvoiceAmount,

            'currentInvoicePaid' => $currentInvoicePaid,

            'currentInvoiceOutstanding' =>
                $currentInvoiceOutstanding,

            'tenantCreditAmount' =>
                $tenantCreditAmount,

            'recentPayments' => $recentPayments,

            'openMaintenance' => $openMaintenance,

            'nextPaymentDue' => $nextPaymentDue,
        ]);
    }

    /**
     * Show tenancy information.
     */
    public function tenancy(Request $request): View
    {
        $user = $request->user();

        $this->ensureTenant($user);

        $tenant = $user->tenant;

        abort_unless($tenant, 404);

        $tenant->load([
            'leases.unit.property',
        ]);

        return view('portal.tenancy', [
            'user' => $user,
            'tenant' => $tenant,
        ]);
    }

    /**
     * Show tenant profile.
     */
    public function profile(Request $request): View
    {
        $user = $request->user();

        $this->ensureTenant($user);

        $tenant = $user->tenant;

        abort_unless($tenant, 404);

        return view('portal.profile', [
            'user' => $user,
            'tenant' => $tenant,
        ]);
    }

    /**
     * Update tenant profile.
     *
     * Tenant can update contact information,
     * but cannot change their role, tenant_id,
     * active status, or password here.
     */
    public function updateProfile(
        Request $request
    ): RedirectResponse {
        $user = $request->user();

        $this->ensureTenant($user);

        $tenant = $user->tenant;

        abort_unless($tenant, 404);

        $validated = $request->validate([
            'full_name' => [
                'required',
                'string',
                'max:255',
            ],

            'phone' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'email',
                'max:255',
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'emergency_contact_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'emergency_contact_phone' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Check email uniqueness
        |--------------------------------------------------------------------------
        */

        $emailExists = User::query()
            ->where(
                'email',
                $validated['email']
            )
            ->where(
                'id',
                '!=',
                $user->id
            )
            ->exists();

        if ($emailExists) {
            return back()
                ->withInput()
                ->withErrors([
                    'email' =>
                        'This email address is already being used by another account.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Update tenant
        |--------------------------------------------------------------------------
        */

        $tenant->update([
            'full_name' =>
                $validated['full_name'],

            'phone' =>
                $validated['phone'],

            'email' =>
                $validated['email'],

            'address' =>
                $validated['address'] ?? null,

            'emergency_contact_name' =>
                $validated['emergency_contact_name'] ?? null,

            'emergency_contact_phone' =>
                $validated['emergency_contact_phone'] ?? null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Keep portal login synchronized
        |--------------------------------------------------------------------------
        */

        $user->update([
            'name' =>
                $validated['full_name'],

            'email' =>
                $validated['email'],
        ]);

        return back()->with(
            'success',
            'Your profile has been updated successfully.'
        );
    }

    /**
     * Upload or replace tenant profile photo.
     */
    public function updatePhoto(
        Request $request
    ): RedirectResponse {
        $user = $request->user();

        $this->ensureTenant($user);

        $request->validate([
            'profile_photo' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Delete old photo
        |--------------------------------------------------------------------------
        */

        if ($user->profile_photo) {
            Storage::disk('public')->delete(
                $user->profile_photo
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Store new photo
        |--------------------------------------------------------------------------
        */

        $path = $request
            ->file('profile_photo')
            ->store(
                'profile-photos',
                'public'
            );

        /*
        |--------------------------------------------------------------------------
        | Save path
        |--------------------------------------------------------------------------
        */

        $user->update([
            'profile_photo' => $path,
        ]);

        return back()->with(
            'success',
            'Your profile picture has been updated successfully.'
        );
    }

    /**
     * Remove tenant profile photo.
     */
    public function removePhoto(
        Request $request
    ): RedirectResponse {
        $user = $request->user();

        $this->ensureTenant($user);

        if ($user->profile_photo) {
            Storage::disk('public')->delete(
                $user->profile_photo
            );
        }

        $user->update([
            'profile_photo' => null,
        ]);

        return back()->with(
            'success',
            'Your profile picture has been removed.'
        );
    }

    /**
     * Update tenant light/dark appearance preference.
     */
    public function updateAppearance(
        Request $request
    ): JsonResponse {
        $user = $request->user();

        $this->ensureTenant($user);

        $validated = $request->validate([
            'appearance' => [
                'required',
                'in:light,dark',
            ],
        ]);

        $user->update([
            'appearance' =>
                $validated['appearance'],
        ]);

        return response()->json([
            'success' => true,
            'appearance' =>
                $user->appearance,
        ]);
    }

    /**
     * Show password change page.
     */
    public function password(
        Request $request
    ): View {
        $user = $request->user();

        $this->ensureTenant($user);

        return view('portal.password', [
            'user' => $user,
        ]);
    }

    /**
     * Change tenant password.
     */
    public function updatePassword(
        Request $request
    ): RedirectResponse {
        $user = $request->user();

        $this->ensureTenant($user);

        $validated = $request->validate([
            'current_password' => [
                'required',
                'current_password:web',
            ],

            'password' => [
                'required',
                'confirmed',
                Password::defaults(),
            ],
        ]);

        $user->update([
            'password' =>
                $validated['password'],
        ]);

        return back()->with(
            'success',
            'Your password has been changed successfully.'
        );
    }

    /**
     * Ensure the authenticated user is an active tenant.
     */
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