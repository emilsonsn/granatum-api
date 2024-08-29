<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ConstructionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SolicitationController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
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

    Route::middleware(AdminMiddleware::class)->group(function() {
        // Middleware do admin
    });

    Route::post('logout', [AuthController::class, 'logout']);

    Route::prefix('user')->group(function(){
        Route::get('all', [UserController::class, 'all']);
        Route::get('search', [UserController::class, 'search']);
        Route::get('cards', [UserController::class, 'cards']);
        Route::get('me', [UserController::class, 'getUser']);
        Route::post('create', [UserController::class, 'create']);
        Route::patch('{id}', [UserController::class, 'update']);
        Route::post('block/{id}', [UserController::class, 'userBlock']);

        Route::get('position/search', [UserController::class, 'positionSearch']);

        Route::get('sector/search', [UserController::class, 'sectorSearch']);
        Route::post('sector/create', [UserController::class, 'sectorCreate']);
        Route::delete('sector/{id}', [UserController::class, 'sectorDelete']);
    });

    Route::prefix('supplier')->group(function(){
        Route::get('search', [SupplierController::class, 'search']);
        Route::post('create', [SupplierController::class, 'create']);
        Route::patch('{id}', [SupplierController::class, 'update']);
        Route::delete('{id}', [SupplierController::class, 'delete']);

        Route::get('type/search', [SupplierController::class, 'typeSearch']);
        Route::post('type/create', [SupplierController::class, 'typeCreate']);
        Route::delete('type/{id}', [SupplierController::class, 'typeDelete']);
    });

    Route::prefix('service')->group(function(){
        Route::get('search', [ServiceController::class, 'search']);
        Route::post('create', [ServiceController::class, 'create']);
        Route::patch('{id}', [ServiceController::class, 'update']);
        Route::delete('{id}', [ServiceController::class, 'delete']);

        Route::get('type/search', [ServiceController::class, 'typeSearch']);
        Route::post('type/create', [ServiceController::class, 'typeCreate']);
        Route::delete('type/{id}', [ServiceController::class, 'typeDelete']);
    });

    Route::prefix('construction')->group(function(){
        Route::get('search', [ConstructionController::class, 'search']);
        Route::post('create', [ConstructionController::class, 'create']);
        Route::patch('{id}', [ConstructionController::class, 'update']);
        Route::delete('{id}', [ConstructionController::class, 'delete']);
    });

    Route::prefix('client')->group(function(){
        Route::get('search', [ClientController::class, 'search']);
        Route::post('create', [ClientController::class, 'create']);
        Route::patch('{id}', [ClientController::class, 'update']);
        Route::delete('{id}', [ClientController::class, 'delete']);
    });

    Route::prefix('order')->group(function(){
        Route::get('search', [OrderController::class, 'search']);
        Route::post('create', [OrderController::class, 'create']);
        Route::post('granatum/{orderId}', [OrderController::class, 'upRelease']);
        Route::patch('{id}', [OrderController::class, 'update']);
        Route::delete('{id}', [OrderController::class, 'delete']);
        Route::delete('file/{id}', [OrderController::class, 'delete_order_file']);
        Route::delete('item/{id}', [OrderController::class, 'delete_order_item']);
    });

    Route::prefix('dashboard')->group(function(){
        Route::get('cards', [DashboardController::class, 'cards']);
        Route::post('purchaseGraphic', [DashboardController::class, 'purchaseGraphic']);
        Route::post('orderGraphic', [DashboardController::class, 'orderGraphic']);
    });

    Route::prefix('solicitation')->group(function(){
        Route::get('search', [SolicitationController::class, 'search']);
        Route::get('cards', [SolicitationController::class, 'cards']);
        Route::post('create', [SolicitationController::class, 'create']);
        Route::patch('{id}', [SolicitationController::class, 'update']);
        Route::delete('{id}', [SolicitationController::class, 'delete']);
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
        Route::get('status', [TaskController::class, 'getStatus']);
        Route::post('status/create', [TaskController::class, 'create_status']);
        Route::delete('status/{id}', [TaskController::class, 'delete_status']);

        // Arquivos de tarefas
        Route::delete('file/{id}', [TaskController::class, 'delete_task_file']);
    });
});
