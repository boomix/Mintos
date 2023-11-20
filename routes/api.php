<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\TransfersController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/accounts', [TransfersController::class, 'accounts']);
Route::get('/transactions', [TransfersController::class, 'transactions']);
Route::post('/transfer', [TransfersController::class, 'transfer']);
