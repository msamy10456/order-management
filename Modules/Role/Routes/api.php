<?php

use App\Helpers\GeneralHelper;
use Illuminate\Support\Facades\Route;
use Modules\Role\Helpers\PermissionHelper;
use Modules\Role\Http\Controllers\AdminRoleController;

/*
 *--------------------------------------------------------------------------
 * API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 *
*/

Route::group(['prefix' => 'admin/roles', 'middleware' => GeneralHelper::adminMiddlewares()], function () {
    Route::get('', [AdminRoleController::class, 'index'])
        ->middleware(PermissionHelper::getPermissionNameMiddleware('all', 'role'));

    Route::get('{id}', [AdminRoleController::class, 'show'])
        ->middleware(PermissionHelper::getPermissionNameMiddleware('show', 'role'));

    Route::post('', [AdminRoleController::class, 'store'])
        ->middleware(PermissionHelper::getPermissionNameMiddleware('store', 'role'));

    Route::put('{id}', [AdminRoleController::class, 'update'])
        ->middleware(PermissionHelper::getPermissionNameMiddleware('update', 'role'));

    Route::delete('{id}', [AdminRoleController::class, 'destroy'])
        ->middleware(PermissionHelper::getPermissionNameMiddleware('delete', 'role'));
});
