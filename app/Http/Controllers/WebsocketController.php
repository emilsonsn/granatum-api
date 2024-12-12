<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\EvolutionEvent;
use App\Services\Websocket\WebsocketService;

class WebsocketController extends Controller
{

    public $websocketService;

    public function __construct(WebsocketService $websocketService) {
        $this->websocketService = $websocketService;
    }

    public function handle(Request $request){
        $requestData = $request->all();

        $this->websocketService->handle($requestData['data']);
    
        if($requestData['data']['event'] == "chats.update"){
            $push = [
                'event' => $requestData['data']['event'],
                'remoteJid' => $requestData['data']['data'][0]['remoteJid'],
                'instance' => $requestData['data']['instance']
            ];
            broadcast(new EvolutionEvent($push));
        }
    
        return response()->json(['status' => 'success']);
    }
}
