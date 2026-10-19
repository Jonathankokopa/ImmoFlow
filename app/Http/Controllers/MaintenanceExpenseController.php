<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceExpense;
use App\Models\MaintenanceRequest;
use App\Support\Currency;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MaintenanceExpenseController extends Controller
{
    public function index()
    {
        $expenses = MaintenanceExpense::with([
            'maintenanceRequest.unit.property',
        ])
            ->latest()
            ->get();

        return view(
            'maintenance_expenses.index',
            compact('expenses')
        );
    }

    public function create()
    {
        $requests = MaintenanceRequest::with([
            'unit.property',
        ])
            ->whereNotIn('status', ['cancelled'])
            ->latest()
            ->get();

        return view(
            'maintenance_expenses.create',
            compact('requests')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'maintenance_request_id' => [
                'required',
                'exists:maintenance_requests,id',
            ],

            'expense_type' => [
                'required',
                'in:labour,materials,parts,other',
            ],

            'description' => [
                'required',
                'string',
                'max:255',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0',
            ],

            'expense_date' => [
                'required',
                'date',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Get Maintenance Request
        |--------------------------------------------------------------------------
        */

        $maintenanceRequest = MaintenanceRequest::with([
            'unit',
        ])->findOrFail(
            $validated['maintenance_request_id']
        );

        /*
        |--------------------------------------------------------------------------
        | Validate Unit
        |--------------------------------------------------------------------------
        */

        if (!$maintenanceRequest->unit) {
            throw ValidationException::withMessages([
                'maintenance_request_id' =>
                    'The maintenance request is not associated with a unit.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Get Currency From Unit
        |--------------------------------------------------------------------------
        |
        | Maintenance expenses inherit the currency of the unit.
        |
        */

        $currency = strtoupper(
            (string) $maintenanceRequest->unit->currency
        );

        if (!in_array($currency, Currency::SUPPORTED, true)) {
            throw ValidationException::withMessages([
                'maintenance_request_id' =>
                    'The unit has an unsupported currency.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Create Expense
        |--------------------------------------------------------------------------
        */

        $validated['currency'] = $currency;

        MaintenanceExpense::create($validated);

        return redirect()
            ->route('maintenance-expenses.index')
            ->with(
                'success',
                'Maintenance expense recorded successfully.'
            );
    }

    public function show(MaintenanceExpense $maintenanceExpense)
    {
        $maintenanceExpense->load([
            'maintenanceRequest.unit.property',
        ]);

        return view(
            'maintenance_expenses.show',
            compact('maintenanceExpense')
        );
    }

    public function edit(MaintenanceExpense $maintenanceExpense)
    {
        $requests = MaintenanceRequest::with([
            'unit.property',
        ])
            ->where(function ($query) use ($maintenanceExpense) {
                $query
                    ->whereNotIn('status', ['cancelled'])
                    ->orWhere(
                        'id',
                        $maintenanceExpense->maintenance_request_id
                    );
            })
            ->latest()
            ->get();

        return view(
            'maintenance_expenses.edit',
            compact(
                'maintenanceExpense',
                'requests'
            )
        );
    }

    public function update(
        Request $request,
        MaintenanceExpense $maintenanceExpense
    ) {
        $validated = $request->validate([
            'maintenance_request_id' => [
                'required',
                'exists:maintenance_requests,id',
            ],

            'expense_type' => [
                'required',
                'in:labour,materials,parts,other',
            ],

            'description' => [
                'required',
                'string',
                'max:255',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0',
            ],

            'expense_date' => [
                'required',
                'date',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Get Maintenance Request
        |--------------------------------------------------------------------------
        */

        $maintenanceRequest = MaintenanceRequest::with([
            'unit',
        ])->findOrFail(
            $validated['maintenance_request_id']
        );

        /*
        |--------------------------------------------------------------------------
        | Validate Unit
        |--------------------------------------------------------------------------
        */

        if (!$maintenanceRequest->unit) {
            throw ValidationException::withMessages([
                'maintenance_request_id' =>
                    'The maintenance request is not associated with a unit.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Determine Currency
        |--------------------------------------------------------------------------
        |
        | Currency always comes from the current unit.
        |
        | We do NOT allow the request to submit a currency.
        |
        */

        $currency = strtoupper(
            (string) $maintenanceRequest->unit->currency
        );

        if (!in_array($currency, Currency::SUPPORTED, true)) {
            throw ValidationException::withMessages([
                'maintenance_request_id' =>
                    'The unit has an unsupported currency.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Preserve Historical Currency
        |--------------------------------------------------------------------------
        |
        | If this expense already has a recorded currency and the expense
        | is being moved to another unit with a different currency, changing
        | the currency would make the historical amount ambiguous.
        |
        | Therefore, once an expense exists, its currency is locked.
        |
        */

        $existingCurrency = strtoupper(
            (string) $maintenanceExpense->currency
        );

        if (
            $existingCurrency !== ''
            && in_array(
                $existingCurrency,
                Currency::SUPPORTED,
                true
            )
            && $existingCurrency !== $currency
        ) {
            throw ValidationException::withMessages([
                'maintenance_request_id' =>
                    'This expense cannot be moved to a unit with a different currency.',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Save Expense
        |--------------------------------------------------------------------------
        */

        $validated['currency'] =
            $existingCurrency !== ''
                ? $existingCurrency
                : $currency;

        $maintenanceExpense->update($validated);

        return redirect()
            ->route('maintenance-expenses.index')
            ->with(
                'success',
                'Maintenance expense updated successfully.'
            );
    }

    public function destroy(MaintenanceExpense $maintenanceExpense)
    {
        $maintenanceExpense->delete();

        return redirect()
            ->route('maintenance-expenses.index')
            ->with(
                'success',
                'Maintenance expense deleted successfully.'
            );
    }
}