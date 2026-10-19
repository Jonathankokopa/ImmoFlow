<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OwnerDashboardController;
use App\Http\Controllers\LeaseController;
use App\Http\Controllers\MaintenanceCategoryController;
use App\Http\Controllers\MaintenanceExpenseController;
use App\Http\Controllers\MaintenanceRequestController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentReceiptController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\PropertyTypeController;
use App\Http\Controllers\ReceiptVerificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RentInvoiceController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SupportRequestController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\TenantMaintenanceController;
use App\Http\Controllers\TenantNotificationController;
use App\Http\Controllers\TenantPaymentController;
use App\Http\Controllers\TenantPortalController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::view('/', 'welcome')
    ->name('welcome');


/*
|--------------------------------------------------------------------------
| Payment Provider Callbacks / Webhooks
|--------------------------------------------------------------------------
|
| These routes intentionally live outside authenticated middleware.
|
| Provider callbacks cannot use the application's normal user session.
| Provider-specific authentication/signature validation belongs inside
| the corresponding controller/service.
|
|--------------------------------------------------------------------------
*/

// M-Pesa Daraja callback
//
// Keep your currently working callback route/controller here.
//
// Example:
//
// Route::post(
//     '/payments/mpesa/callback',
//     [MpesaController::class, 'callback']
// )->name('payments.mpesa.callback');


// Airtel Money callback
//
// Route::post(
//     '/payments/airtel-money/callback',
//     [AirtelMoneyController::class, 'callback']
// )->name('payments.airtel-money.callback');


/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/dashboard',
        [DashboardController::class, 'index']
    )->name('dashboard');

    Route::get(
        '/owner/dashboard',
        [OwnerDashboardController::class, 'index']
    )->name('owner.dashboard');

    /*
    |--------------------------------------------------------------------------
    | Language Switcher Route
    |--------------------------------------------------------------------------
    */

    Route::get('/lang/{locale}', function (string $locale, \Illuminate\Http\Request $request) {
        if (in_array($locale, ['en', 'fr', 'sw'], true)) {
            if ($request->user()) {
                $request->user()->update(['language' => $locale]);
            }
            $request->session()->put('language', $locale);
            app()->setLocale($locale);
        }
        return redirect()->back();
    })->name('lang.switch');


    /*
    |--------------------------------------------------------------------------
    | Tenant Portal
    |--------------------------------------------------------------------------
    |
    | Only users with role "tenant" can access these routes.
    |
    | The authenticated user's tenant_id determines which tenant
    | information is displayed.
    |
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:tenant')->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Tenant Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/portal',
            [TenantPortalController::class, 'index']
        )->name('portal');


        /*
        |--------------------------------------------------------------------------
        | My Tenancy
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/portal/tenancy',
            [TenantPortalController::class, 'tenancy']
        )->name('portal.tenancy');


        /*
        |--------------------------------------------------------------------------
        | Maintenance
        |--------------------------------------------------------------------------
        |
        | Tenant-facing maintenance portal.
        |
        | These routes use the existing maintenance database structure.
        | They do not replace or modify the existing staff maintenance
        | controllers.
        |
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/portal/maintenance',
            [TenantMaintenanceController::class, 'index']
        )->name('portal.maintenance');

        Route::get(
            '/portal/maintenance/create',
            [TenantMaintenanceController::class, 'create']
        )->name('portal.maintenance.create');

        Route::post(
            '/portal/maintenance',
            [TenantMaintenanceController::class, 'store']
        )->name('portal.maintenance.store');

        Route::get(
            '/portal/maintenance/{maintenanceRequest}',
            [TenantMaintenanceController::class, 'show']
        )->name('portal.maintenance.show');


        /*
        |--------------------------------------------------------------------------
        | Payments
        |--------------------------------------------------------------------------
        |
        | Tenant-facing payment history.
        |
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/portal/payments',
            [TenantPaymentController::class, 'index']
        )->name('portal.payments');

        Route::get(
            '/portal/payments/{payment}',
            [TenantPaymentController::class, 'show']
        )->name('portal.payments.show');


        /*
        |--------------------------------------------------------------------------
        | My Profile
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/portal/profile',
            [TenantPortalController::class, 'profile']
        )->name('portal.profile');

        Route::put(
            '/portal/profile',
            [TenantPortalController::class, 'updateProfile']
        )->name('portal.profile.update');


        /*
        |--------------------------------------------------------------------------
        | Profile Photo
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/portal/profile/photo',
            [TenantPortalController::class, 'updatePhoto']
        )->name('portal.profile.photo');

        Route::delete(
            '/portal/profile/photo',
            [TenantPortalController::class, 'removePhoto']
        )->name('portal.profile.photo.remove');


        /*
        |--------------------------------------------------------------------------
        | Dark / Light Mode
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/portal/appearance',
            [TenantPortalController::class, 'updateAppearance']
        )->name('portal.appearance');


        /*
        |--------------------------------------------------------------------------
        | Change Password
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/portal/password',
            [TenantPortalController::class, 'password']
        )->name('portal.password');

        Route::put(
            '/portal/password',
            [TenantPortalController::class, 'updatePassword']
        )->name('portal.password.update');


        /*
        |--------------------------------------------------------------------------
        | Tenant Notifications
        |--------------------------------------------------------------------------
        |
        | Tenants have their own notification area.
        |
        | IMPORTANT:
        | The mark-all route is named
        | portal.notifications.mark-all-as-read
        | because this is the route name used by the tenant notification UI.
        |
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/portal/notifications',
            [TenantNotificationController::class, 'index']
        )->name('portal.notifications');

        Route::get(
            '/portal/notifications/{id}/read',
            [TenantNotificationController::class, 'read']
        )->name('portal.notifications.read');

        Route::post(
            '/portal/notifications/mark-all-read',
            [TenantNotificationController::class, 'markAllAsRead']
        )->name('portal.notifications.mark-all-as-read');

    });


    /*
    |--------------------------------------------------------------------------
    | Administrator / Staff Notifications
    |--------------------------------------------------------------------------
    |
    | These are separate from the tenant notification portal.
    |
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/notifications',
        [NotificationController::class, 'index']
    )->name('notifications.index');

    Route::get(
        '/notifications/{id}/read',
        [NotificationController::class, 'readAndRedirect']
    )->name('notifications.read');

    Route::post(
        '/notifications/mark-all-read',
        [NotificationController::class, 'markAllAsRead']
    )->name('notifications.mark-all-read');


    /*
    |--------------------------------------------------------------------------
    | User Management
    |--------------------------------------------------------------------------
    |
    | Administrator only.
    |
    | Tenant portal accounts are NOT created here.
    | They are created from the tenant profile.
    |
    |--------------------------------------------------------------------------
    */

    Route::middleware('role:administrator')->group(function () {

        Route::resource(
            'users',
            UserController::class
        )->except(['show']);

    });


    /*
    |--------------------------------------------------------------------------
    | Property & Portfolio Management
    |--------------------------------------------------------------------------
    |
    | Administrator
    | Property Manager
    | Reception Staff
    | Owner
    |
    |--------------------------------------------------------------------------
    */

    Route::middleware(
        'role:administrator,property_manager,reception_staff,owner'
    )->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Owners Management (Restricted to Admins/Managers)
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'owners',
            OwnerController::class
        );


        /*
        |--------------------------------------------------------------------------
        | Properties
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'properties',
            PropertyController::class
        );


        /*
        |--------------------------------------------------------------------------
        | Property Photos
        |--------------------------------------------------------------------------
        */

        Route::delete(
            'properties/{property}/photos/{photo}',
            [PropertyController::class, 'deletePhoto']
        )->name('properties.photos.destroy');

        Route::patch(
            'properties/{property}/photos/{photo}/cover',
            [PropertyController::class, 'setCoverPhoto']
        )->name('properties.photos.cover');


        /*
        |--------------------------------------------------------------------------
        | Units
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'units',
            UnitController::class
        );


        /*
        |--------------------------------------------------------------------------
        | Tenants
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'tenants',
            TenantController::class
        );


        /*
        |--------------------------------------------------------------------------
        | Tenant & Owner Portal Accounts
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/tenants/{tenant}/portal-account',
            [TenantController::class, 'createPortalAccount']
        )->name('tenants.portal-account');

        Route::post(
            '/tenants/{tenant}/toggle-portal',
            [TenantController::class, 'togglePortalAccess']
        )->name('tenants.toggle-portal');

        Route::middleware('role:administrator')->group(function () {

            Route::post(
                '/owners/{owner}/portal-account',
                [OwnerController::class, 'createPortalAccount']
            )->name('owners.portal-account');

            Route::post(
                '/owners/{owner}/toggle-portal',
                [OwnerController::class, 'togglePortalAccess']
            )->name('owners.toggle-portal');

        });


        /*
        |--------------------------------------------------------------------------
        | Leases
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/leases/{lease}/checkout',
            [LeaseController::class, 'checkout']
        )->name('leases.checkout');

        Route::resource(
            'leases',
            LeaseController::class
        );

    });


    /*
    |--------------------------------------------------------------------------
    | Property Types
    |--------------------------------------------------------------------------
    |
    | Administrator
    | Property Manager
    |
    |--------------------------------------------------------------------------
    */

    Route::middleware(
        'role:administrator,property_manager'
    )->group(function () {

        Route::resource(
            'property-types',
            PropertyTypeController::class
        );

    });


    /*
    |--------------------------------------------------------------------------
    | Finance
    |--------------------------------------------------------------------------
    |
    | Administrator
    | Accountant
    | Owner
    | Property Manager
    |
    |--------------------------------------------------------------------------
    */

    Route::middleware(
        'role:administrator,accountant,owner,property_manager'
    )->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Rent Invoices
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'rent-invoices',
            RentInvoiceController::class
        );

        Route::post(
            'rent-invoices/{rentInvoice}/send-email',
            [RentInvoiceController::class, 'sendEmail']
        )->name('rent-invoices.send-email');


        /*
        |--------------------------------------------------------------------------
        | M-Pesa STK Push
        |--------------------------------------------------------------------------
        */

        Route::post(
            'rent-invoices/{rentInvoice}/payments/mpesa',
            [PaymentController::class, 'initiateMpesa']
        )->name('rent-invoices.payments.mpesa');


        /*
        |--------------------------------------------------------------------------
        | Manual / Internal Payments
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'payments',
            PaymentController::class
        );

    });


    /*
    |--------------------------------------------------------------------------
    | Maintenance
    |--------------------------------------------------------------------------
    |
    | Administrator
    | Property Manager
    | Maintenance Staff
    |
    |--------------------------------------------------------------------------
    */

    Route::middleware(
        'role:administrator,property_manager,maintenance_staff'
    )->group(function () {

        Route::resource(
            'maintenance-categories',
            MaintenanceCategoryController::class
        );

        Route::resource(
            'maintenance-requests',
            MaintenanceRequestController::class
        );

        Route::resource(
            'maintenance-expenses',
            MaintenanceExpenseController::class
        );

    });


    /*
    |--------------------------------------------------------------------------
    | Reports
    |--------------------------------------------------------------------------
    |
    | Administrator
    | Property Manager
    | Accountant
    |
    |--------------------------------------------------------------------------
    */

    Route::middleware(
        'role:administrator,property_manager,accountant'
    )->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Reports Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/reports',
            [ReportController::class, 'index']
        )->name('reports.index');


        /*
        |--------------------------------------------------------------------------
        | Excel Report Export
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/reports/export',
            [ReportController::class, 'export']
        )->name('reports.export');

    });


    /*
    |--------------------------------------------------------------------------
    | Settings
    |--------------------------------------------------------------------------
    |
    | All authenticated users.
    |
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/settings',
        [SettingsController::class, 'index']
    )->name('settings.index');

    Route::patch(
        '/settings',
        [SettingsController::class, 'update']
    )->name('settings.update');


    /*
    |--------------------------------------------------------------------------
    | Receipt Verification
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/receipts/verify/{token}',
        [ReceiptVerificationController::class, 'show']
    )->name('receipts.verify');


    /*
    |--------------------------------------------------------------------------
    | Payment Receipts
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/payments/{payment}/receipt',
        [PaymentReceiptController::class, 'show']
    )->name('payments.receipt');

    Route::get(
        '/payments/{payment}/receipt/download',
        [PaymentReceiptController::class, 'download']
    )->name('payments.receipt.download');


    /*
    |--------------------------------------------------------------------------
    | Support
    |--------------------------------------------------------------------------
    |
    | All authenticated users.
    |
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/support',
        [SupportRequestController::class, 'index']
    )->name('support.index');

    Route::post(
        '/support',
        [SupportRequestController::class, 'store']
    )->name('support.store');

    Route::post(
        '/support/ai',
        [SupportRequestController::class, 'askAi']
    )
        ->middleware('throttle:20,1')
        ->name('support.ai');


    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    |
    | All authenticated users.
    |
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/profile',
        [ProfileController::class, 'edit']
    )->name('profile.edit');

    Route::patch(
        '/profile',
        [ProfileController::class, 'update']
    )->name('profile.update');

    Route::delete(
        '/profile',
        [ProfileController::class, 'destroy']
    )->name('profile.destroy');

});


/*
|--------------------------------------------------------------------------
| Fallback / 404
|--------------------------------------------------------------------------
*/

Route::fallback(function () {
    return response()
        ->view('errors.404', [], 404);
});


/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';