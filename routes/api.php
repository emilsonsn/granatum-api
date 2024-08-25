<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TenderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WebhookController;
use App\Http\Middleware\AdminMiddleware;

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

Route::post('login', [AuthController::class, 'login']);

Route::get('validateToken', [AuthController::class, 'validateToken']);
Route::post('recoverPassword', [UserController::class, 'passwordRecovery']);
Route::post('updatePassword', [UserController::class, 'updatePassword']);


Route::get('validateToken', [AuthController::class, 'validateToken']);

Route::middleware('jwt')->group(function(){
    
    Route::post('logout', [AuthController::class, 'logout']);

    Route::get('user/getUser', [UserController::class, 'getUser']);

    Route::prefix('user')->group(function(){
        Route::get('search', [UserController::class, 'search']);
        Route::post('create', [UserController::class, 'create']);
        Route::patch('{id}', [UserController::class, 'update']);
        Route::post('block/{id}', [UserController::class, 'userBlock']);
    });

    Route::prefix('supplier')->group(function(){
        Route::get('search', [SupplierController::class, 'search']);
        Route::post('create', [SupplierController::class, 'create']);
        Route::patch('{id}', [SupplierController::class, 'update']);
        Route::delete('{id}', [SupplierController::class, 'delete']);
    });

    Route::prefix('task')->group(function(){
        Route::get('search', [TaskController::class, 'search']);
        Route::post('create', [TaskController::class, 'create']);
        Route::patch('{id}', [TaskController::class, 'update']);
        Route::delete('{id}', [TaskController::class, 'delete']);

        // Sub-tasks
        Route::patch('subtask/status/{id}', [TaskController::class, 'change_status_sub_tasks']);
        Route::delete('subtask/{id}', [TaskController::class, 'delete_sub_tasks']);

        // Status
        Route::post('status/create', [TaskController::class, 'create_status']);
        Route::delete('status/{id}', [TaskController::class, 'delete_status']);

        // Arquivos de tarefas
        Route::delete('file/{id}', [TaskController::class, 'delete_task_file']);
    });



});
