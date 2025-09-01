<?php


use App\Http\Controllers\Api\TaskController as ApiTaskController;
use App\Http\Controllers\Api\CategoryController as ApiCategoryController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

Route::middleware('auth:sanctum')->group(function () {
    // Tasks API
    Route::apiResource('tasks', ApiTaskController::class);
    
    // Categories API
    Route::apiResource('categories', ApiCategoryController::class);

    //Api Tokens
    Route::middleware('auth:sanctum')->post('/tokens', [AuthenticatedSessionController::class, 'createToken']);
});
