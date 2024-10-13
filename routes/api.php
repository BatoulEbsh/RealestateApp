<?php

use App\Http\Controllers\Constants\GovernorateController;
use App\Http\Controllers\Constants\PropertyStateController;
use App\Http\Controllers\Constants\PropertyTypeController;
use App\Http\Controllers\Constants\RegionController;
use App\Http\Controllers\Constants\ReservationStateController;
use App\Http\Controllers\Constants\ReservationTypeController;
use App\Http\Controllers\FavouriteController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\User\ReportController;
use App\Http\Controllers\User\UserControllers;
use App\Http\Controllers\User\WalletController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PropertyController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'prefix' => 'auth',
], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::group([
        'middleware' => ['auth_user:api'],
    ], function () {
        Route::get('logout', [AuthController::class, 'logout']);
        Route::get('me', [UserControllers::class, 'me']);
        Route::post('updateUser', [UserControllers::class, 'updateUser']);
        Route::post('changePassword', [UserControllers::class, 'changePassword']);
        Route::post('/', [ReportController::class, 'store']);
        Route::post('/', [PropertyController::class, 'store']);

    });
});
Route::group([
    'prefix' => 'reports',
    'middleware' => ['auth_user:api']
], function () {
    Route::post('/', [ReportController::class, 'store']);
    Route::get('/properties/{id}', [ReportController::class, 'index'])->middleware(['admin']);
    Route::get('/{id}', [ReportController::class, 'show']);
    Route::put('/{id}', [ReportController::class, 'update']);
    Route::post('/{id}',[ReportController::class,'destroy'])->middleware(['admin']);
});
Route::group([
    'prefix' => 'properties',
    'middleware' => ['auth_user:api',]
], function () {
    Route::post('/', [PropertyController::class, 'store']);
    Route::get('/', [PropertyController::class, 'index']);
    Route::get('/search', [PropertyController::class, 'search']);
    Route::get('/{id}', [PropertyController::class, 'show']);
    Route::delete('/{id}', [PropertyController::class, 'destroy'])->middleware(['admin']);
    Route::put('/{id}', [PropertyController::class, 'update']);
    Route::get('/show/all', [PropertyController::class, 'showAll']);
});
Route::group([
    'prefix' => 'wallet',
    'middleware' => ['auth_user:api'],
], function () {
    Route::get('/', [WalletController::class, 'index']);
});
Route::group([
    'prefix' => 'admin/financial',
    'middleware' => ['auth_user:api', 'admin'],
], function () {
    Route::put('/withdraw', [WalletController::class, 'withdrawAmount']);
    Route::put('/deposit', [WalletController::class, 'deposit']);
});
Route::group([
    'prefix' => 'reservations',
    'middleware' => ['auth_user:api']
], function () {
    Route::post('/', [ReservationController::class, 'store']);
    Route::post('/{id}',[ReservationController::class,'accept']);
});
Route::group([
    'prefix' => 'favourite',
    'middleware' => ['auth_user:api']
], function () {
    Route::get('/', [FavouriteController::class, 'index']);
    Route::put('/{id}', [FavouriteController::class, 'add']);
    Route::delete('/{id}',[FavouriteController::class,'delete']);
});
Route::get('governorates', [GovernorateController::class, 'index']);
Route::get('propertyStates', [PropertyStateController::class, 'index']);
Route::get('governorates/{id}/regions', [RegionController::class, 'index']);
Route::get('reservationStates', [ReservationStateController::class, 'index']);
Route::get('reservationTypes', [ReservationTypeController::class, 'index']);
Route::get('propertyTypes', [PropertyTypeController::class, 'index']);
