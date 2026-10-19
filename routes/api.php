<?php

use App\Http\Controllers\MpesaController;
use Illuminate\Support\Facades\Route;

Route::post(
    '/payments/mpesa/callback',
    [MpesaController::class, 'callback']
);

Route::post(
    '/payments/mpesa/stk-push',
    [MpesaController::class, 'stkPush']
);