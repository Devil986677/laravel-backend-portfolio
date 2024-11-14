<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DashboardController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\EsewaPaymentController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::post('/verify-token', [AuthController::class, 'verifyToken']);


    Route::apiResource('contact', ContactController::class);
Route::apiResource('skills', SkillController::class);
Route::apiResource('projects', ProjectsController::class);
Route::get('/count', [DashboardController::class, 'index']);


Route::post('/esewa-payment-initiate', [EsewaPaymentController::class, 'initiatePayment']);
Route::post('/esewa-payment-verify', [EsewaPaymentController::class, 'verifyPayment']);

Route::get('/esewa-payment-success', [EsewaPaymentController::class, 'paymentSuccess']);
Route::get('/esewa-payment-failure', [EsewaPaymentController::class, 'paymentFailure']);