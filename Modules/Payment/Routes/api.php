<?php

use App\Helpers\GeneralHelper;
use Illuminate\Support\Facades\Route;
use Modules\Payment\Http\Controllers\AdminPaymentController;

Route::group(['middleware' => GeneralHelper::getDefaultLoggedUserMiddlewares()], function () {
    Route::prefix('payments')->group(function () {
        Route::get('', [AdminPaymentController::class, 'index']);
        Route::post('', [AdminPaymentController::class, 'process']);
        Route::get('{id}', [AdminPaymentController::class, 'show']);
    });
});
