<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NewsController;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;


Route::prefix('auth')->group(function (Router $router) {
    $router->post('code', [AuthController::class, 'code']);
    $router->post('confirm', [AuthController::class, 'confirm']);
    $router->post('register', [AuthController::class, 'register']);
    $router->post('login', [AuthController::class, 'login']);
});

Route::middleware('auth:sanctum')->group(function (Router $router) {

    Route::prefix('news')->group(function (Router $router) {
        $router->get('list', [NewsController::class, 'list']);
        $router->get('detail', [NewsController::class, 'detail']);
        $router->post('create', [NewsController::class, 'create'])->middleware('checkRole');
        $router->post('update', [NewsController::class, 'update'])->middleware('checkRole');
        $router->get('delete', [NewsController::class, 'delete'])->middleware('checkRole');
    });

    $router->post('admin/add-user', [AdminController::class, 'addUser'])->middleware('admin');
});
