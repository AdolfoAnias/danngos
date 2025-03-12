<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Modules\Tasks\Controllers\TaskController;
use App\Modules\Users\Controllers\RegisterController;
use App\Modules\Users\Controllers\LoginController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/auth/register', [RegisterController::class, 'registerUser']);
Route::post('/auth/login', [LoginController::class, 'loginUser'])->name('login');

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('task', [TaskController::class, 'index'])->name('task.index');
    Route::get('task/{id}', [TaskController::class, 'show']);
    Route::delete('task/{id}', [TaskController::class, 'destroy']);
    Route::post('task', [TaskController::class, 'store']);
    Route::put('task', [TaskController::class, 'update']);
});

    
