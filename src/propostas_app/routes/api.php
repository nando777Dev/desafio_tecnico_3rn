<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PropostasController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aqui você pode registrar as rotas da sua API.
| Essas rotas são automaticamente prefixadas com "/api".
|
*/

Route::prefix('propostas')->group(function () {
    Route::post('/create', [PropostasController::class, 'store']);
    Route::get('/', [PropostasController::class, 'index']);
    Route::get('/{id}', [PropostasController::class, 'show']);
    Route::patch('/{id}/update', [PropostasController::class, 'update']);
    Route::patch('/{id}/status', [PropostasController::class, 'updateStatus']);
    Route::delete('/{id}', [PropostasController::class, 'destroy']);

});
