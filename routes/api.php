<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\BudgetDetailController;
use App\Http\Controllers\BudgetGeneratedController;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ConstructionController;
use App\Http\Controllers\CrmCampaignController;
use App\Http\Controllers\CrmDashboardController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FunnelController;
use App\Http\Controllers\FunnelStepController;
use App\Http\Controllers\HrCampaignController;
use App\Http\Controllers\LabelController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\ProfessionController;
use App\Http\Controllers\SelectionProcessController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SolicitationController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TravelController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VacancyController;
use App\Http\Controllers\WebsocketController;
use App\Http\Controllers\WhatsappController;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Http\Request;

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

Route::post('/evolution-data', [WebsocketController::class, 'handle']);

Route::prefix('candidate')->group(function(){
    Route::post('create', [CandidateController::class, 'create']);
});

Route::prefix('profession')->group(function(){   
    Route::post('create', [ProfessionController::class, 'create']);
});

Route::prefix('selection-process')->group(function(){
    Route::get('search', [SelectionProcessController::class, 'search']);
    Route::get('{id}', [SelectionProcessController::class, 'getById']);
});

Route::prefix('lead')->group(function(){
    Route::post('create', [LeadController::class, 'create']);
});

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
        Route::delete('{id}', [UserController::class, 'delete']);
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
        Route::get('get-bank', [OrderController::class, 'getBank']);
        Route::get('get-categories', [OrderController::class, 'getCategories']);
        Route::get('get-tags', [OrderController::class, 'getTags']);
        Route::get('get-suplier', [OrderController::class, 'getSuplier']);        
        Route::get('cost-centers', [OrderController::class, 'getCostCenter']);        
        Route::get('{id}', [OrderController::class, 'getById']);        
        Route::post('create', [OrderController::class, 'create']);
        Route::post('granatum/{orderId}', [OrderController::class, 'upRelease']);
        Route::patch('{id}', [OrderController::class, 'update']);
        Route::delete('{id}', [OrderController::class, 'delete']);
        Route::delete('file/{id}', [OrderController::class, 'delete_order_file']);
        Route::delete('item/{id}', [OrderController::class, 'delete_order_item']);
    });

    Route::prefix('hr-campaign')->group(function(){
        Route::get('search', [HrCampaignController::class, 'search']);
        Route::get('{id}', [HrCampaignController::class, 'getById']);        
        Route::post('create', [HrCampaignController::class, 'create']);
        Route::patch('{id}', [HrCampaignController::class, 'update']);
        Route::delete('{id}', [HrCampaignController::class, 'delete']);
    });

    Route::prefix('crm-campaign')->group(function(){
        Route::get('search', [CrmCampaignController::class, 'search']);
        Route::get('{id}', [CrmCampaignController::class, 'getById']);        
        Route::post('create', [CrmCampaignController::class, 'create']);
        Route::patch('{id}', [CrmCampaignController::class, 'update']);
        Route::delete('{id}', [CrmCampaignController::class, 'delete']);
    });

    Route::prefix('crm-dashboard')->group(function(){
        Route::get('cards/{period}', [CrmDashboardController::class, 'cards']);
        Route::get('budget-graphic/{status}', [CrmDashboardController::class, 'budgetGraphic']);
    });    

    Route::prefix('budget')->group(function(){
        Route::get('search', [BudgetController::class, 'search']);
        Route::get('{id}', [BudgetController::class, 'getById']);        
        Route::post('create', [BudgetController::class, 'create']);
        Route::patch('{id}', [BudgetController::class, 'update']);
        Route::delete('{id}', [BudgetController::class, 'delete']);
    });  
    
    Route::prefix('budget-generated')->group(function(){
        Route::get('search', [BudgetGeneratedController::class, 'search']);
        Route::get('{id}', [BudgetGeneratedController::class, 'getById']);        
        Route::post('create', [BudgetGeneratedController::class, 'create']);
        Route::patch('{id}', [BudgetGeneratedController::class, 'update']);
        Route::delete('variable/{id}', [BudgetGeneratedController::class, 'deleteVariable']);
        Route::delete('{id}', [BudgetGeneratedController::class, 'delete']);
    });    

    Route::prefix('budget-detail')->group(function(){
        Route::get('{id}', [BudgetDetailController::class, 'getById']);        
        Route::post('create', [BudgetDetailController::class, 'create']);
        Route::patch('{id}', [BudgetDetailController::class, 'update']);
    });

    Route::prefix('travel')->group(function(){
        Route::get('search', [TravelController::class, 'search']);
        Route::get('get-bank', [OrderController::class, 'getBank']);
        Route::get('cards', [TravelController::class, 'cards']);
        Route::get('get-categories', [OrderController::class, 'getCategories']);
        Route::get('get-suplier', [OrderController::class, 'getSuplier']);
        Route::get('get-tags', [OrderController::class, 'getTags']);
        Route::get('cost-centers', [OrderController::class, 'getCostCenter']);        
        Route::get('{id}', [TravelController::class, 'getById']);        
        Route::post('create', [TravelController::class, 'create']);
        Route::post('granatum/{travel_id}', [TravelController::class, 'upRelease']);
        Route::patch('{id}', [TravelController::class, 'update']);
        Route::patch('solicitation/{id}', [TravelController::class, 'updateSolicitation']);
        Route::delete('{id}', [TravelController::class, 'delete']);
        Route::delete('file/{id}', [TravelController::class, 'deleteFile']);
    });

    Route::prefix('candidate')->group(function(){
        Route::get('search', [CandidateController::class, 'search']);
        Route::get('cards', [CandidateController::class, 'cards']);
        Route::post('create', [CandidateController::class, 'create']);
        Route::patch('{id}', [CandidateController::class, 'update']);
        Route::delete('{id}', [CandidateController::class, 'delete']);
    });

    Route::prefix('profession')->group(function(){
        Route::get('search', [ProfessionController::class, 'search']);
        Route::get('cards', [ProfessionController::class, 'cards']);        
        Route::get('{id}', [ProfessionController::class, 'getById']);        
        Route::post('create', [ProfessionController::class, 'create']);
        Route::patch('{id}', [ProfessionController::class, 'update']);
        Route::delete('{id}', [ProfessionController::class, 'delete']);
    });

    Route::prefix('vacancy')->group(function(){
        Route::get('search', [VacancyController::class, 'search']);
        Route::get('cards', [VacancyController::class, 'cards']);        
        Route::get('{id}', [VacancyController::class, 'getById']);        
        Route::post('create', [VacancyController::class, 'create']);
        Route::patch('{id}', [VacancyController::class, 'update']);
        Route::delete('{id}', [VacancyController::class, 'delete']);
    });

    Route::prefix('selection-process')->group(function(){
        Route::get('search', [SelectionProcessController::class, 'search']);
        Route::get('cards', [SelectionProcessController::class, 'cards']);
        Route::get('{id}', [SelectionProcessController::class, 'getById']);        
        Route::post('create', [SelectionProcessController::class, 'create']);
        Route::patch('update-status', [SelectionProcessController::class, 'updateStatus']);
        Route::patch('{id}', [SelectionProcessController::class, 'update']);
        Route::delete('{id}', [SelectionProcessController::class, 'delete']);
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

    Route::prefix('lead')->group(function(){
        Route::get('search', [LeadController::class, 'search']);
        Route::get('{id}', [LeadController::class, 'getById']);
        Route::patch('lead-step', [LeadController::class, 'leadStep']);
        Route::patch('{id}', [LeadController::class, 'update']);
        Route::delete('{id}', [LeadController::class, 'delete']);
    });

    Route::prefix('funnel')->group(function(){
        Route::get('search', [FunnelController::class, 'search']);
        Route::get('{id}', [FunnelController::class, 'getById']);
        Route::post('create', [FunnelController::class, 'create']);
        Route::patch('{id}', [FunnelController::class, 'update']);
        Route::delete('{id}', [FunnelController::class, 'delete']);
    });

    Route::prefix('funnel-step')->group(function(){
        Route::get('search', [FunnelStepController::class, 'search']);
        Route::get('{id}', [FunnelStepController::class, 'getById']);
        Route::post('create', [FunnelStepController::class, 'create']);
        Route::patch('{id}', [FunnelStepController::class, 'update']);
        Route::delete('{id}', [FunnelStepController::class, 'delete']);
    });

    Route::prefix('bank')->group(function(){
        Route::get('search', [BankController::class, 'search']);
        Route::get('{id}', [BankController::class, 'getById']);
        Route::post('create', [BankController::class, 'create']);
        Route::patch('{id}', [BankController::class, 'update']);
        Route::delete('{id}', [BankController::class, 'delete']);
    });

    Route::prefix('partner')->group(function(){
        Route::get('search', [PartnerController::class, 'search']);
        Route::get('{id}', [PartnerController::class, 'getById']);
        Route::post('create', [PartnerController::class, 'create']);
        Route::patch('{id}', [PartnerController::class, 'update']);
        Route::delete('{id}', [PartnerController::class, 'delete']);
    });

    Route::prefix('label')->group(function(){
        Route::get('search', [LabelController::class, 'search']);
        Route::get('{id}', [LabelController::class, 'getById']);
        Route::post('create', [LabelController::class, 'create']);
        Route::patch('{id}', [LabelController::class, 'update']);
        Route::delete('{id}', [LabelController::class, 'delete']);
    });
    
    Route::prefix('whatsapp')->group(function(){
        Route::get('chats/{instance}', [WhatsappController::class, 'searchChat']);
        Route::get('messages/{remoteJid}', [WhatsappController::class, 'searchMessage']);
        Route::post('read-message', [WhatsappController::class, 'readMessage']);
        Route::post('send-message', [WhatsappController::class, 'sendMessage']);
        Route::post('send-midia', [WhatsappController::class, 'sendMedia']);
        Route::post('send-audio', [WhatsappController::class, 'sendAudio']);
        Route::patch('update-status/{id}', [WhatsappController::class, 'updateStatus']);
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