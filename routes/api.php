<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ProjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post('register', [AuthController::class, 'register']);

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    //projects routes
    Route::get('projects', [ProjectController::class,'index']);
    Route::post('projects', [ProjectController::class,'store']);
    Route::get('projects/{project}', [ProjectController::class,'show']);
    Route::put('projects/{project}', [ProjectController::class,'update']);
    Route::delete('projects/{project}', [ProjectController::class,'destroy']);
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
