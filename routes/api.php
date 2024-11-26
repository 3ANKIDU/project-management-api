<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::post('register', [AuthController::class, 'register']);

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    //projects routes
    Route::get('projects', [ProjectController::class,'index'])->middleware(['permission:view project']);
    Route::post('projects', [ProjectController::class,'store'])->middleware(['permission:create project']);
    Route::get('projects/{project}', [ProjectController::class,'show'])->middleware(['permission:view project']);
    Route::put('projects/{project}', [ProjectController::class,'update'])->middleware(['permission:update project']);;
    Route::delete('projects/{project}', [ProjectController::class,'destroy'])->middleware(['permission:delete project']);

    //tasks routes
    Route::get('projects/{project}/tasks', [TaskController::class,'index'])->middleware(['permission:view task']);
    Route::post('projects/{project}/tasks', [TaskController::class,'store'])->middleware(['permission:create task']);
    Route::get('tasks/{task}', [TaskController::class,'show'])->middleware(['permission:view task']);
    Route::put('tasks/{task}', [TaskController::class,'update'])->middleware(['permission:update task']);
    Route::delete('tasks/{task}', [TaskController::class,'destroy'])->middleware(['permission:delete task']);
    Route::get('{user}/created-tasks', [TaskController::class,'tasksCreatedBy'])->middleware(['permission:view task']);
    Route::get('{user}/tasks', [TaskController::class,'tasksAssignedTo'])->middleware(['permission:view task']);

});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
