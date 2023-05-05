<?php

use App\Http\Controllers\NasabahController;
use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix('nasabah')->group(function () {
    Route::get('get', [NasabahController::class, 'getNasabah']);
    Route::get('get-point', [NasabahController::class, 'getNasabahPoint']);
    Route::get('get-report', [NasabahController::class, 'getNasabahReport']);
    Route::post('make', [NasabahController::class, 'makeNasabah']);
    Route::delete('delete/{id}', [NasabahController::class, 'deleteNasabah']);
});

Route::prefix('transaction')->group(function () {
    Route::get('get', [TransactionController::class, 'getTransaction']);
    Route::post('make', [TransactionController::class, 'makeTransaction']);
    Route::delete('delete/{id}', [TransactionController::class, 'deleteTransaction']);
});
