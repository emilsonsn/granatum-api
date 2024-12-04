<?php

namespace App\Http\Controllers;

use App\Services\Whatsapp\WhatsappService;
use Illuminate\Http\Request;

class WhatsappController extends Controller
{
    private $whatsappService;

    public function __construct(WhatsappService $whatsappService) {
        $this->whatsappService = $whatsappService;
    }

    public function searchChat(Request $request, string $instance){
        $result = $this->whatsappService->searchChat($request, $instance);

        return $result;
    }

    public function searchMessage(Request $request, string $remoteJid){
        $result = $this->whatsappService->searchMessage($request, $remoteJid);

        return $result;
    }

    public function sendMessage(Request $request){
        $result = $this->whatsappService->createMessage($request);

        if($result['status']) $result['message'] = "Mensagem criada com sucesso";
        return $this->response($result);
    }

    private function response($result){
        return response()->json([
            'status' => $result['status'],
            'message' => $result['message'] ?? null,
            'data' => $result['data'] ?? null,
            'error' => $result['error'] ?? null
        ], $result['statusCode'] ?? 200);
    }
}