<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\VerifyCsrfToken;

use aghaeian\ziraat\Http\Controllers\ziraatController;

Route::group(['middleware' => ['web', 'theme', 'locale', 'currency']], function () {    
    Route::get('ziraat-payment-checkout', [
        ziraatController::class, 'checkoutWithziraat'])->name('ziraat.payment.checkout');    
    Route::post('ziraat-payment-callback/{token}', [
        ziraatController::class, 'paymentCallback'
    ])->withoutMiddleware(VerifyCsrfToken::class)->name('ziraat.payment.callback'); 
});
