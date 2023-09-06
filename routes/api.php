<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;

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

Route::prefix('auth')->group(function (Router $router) {
    $router->post('code', [AuthController::class, 'code']);
    $router->post('confirm', [AuthController::class, 'confirm']);
    $router->post('register', [AuthController::class, 'register']);
    $router->post('login', [AuthController::class, 'login']);
});
