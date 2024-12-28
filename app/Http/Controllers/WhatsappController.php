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

        if($result['status']) $result['message'] = "Mensagem enviada com sucesso";
        return $this->response($result);
    }

    public function sendAudio(Request $request){
        $result = $this->whatsappService->audio($request);

        if($result['status']) $result['message'] = "Audio enviado com sucesso";
        return $this->response($result);
    }

    public function sendMedia(Request $request){
        $result = $this->whatsappService->midia($request);

        if($result['status']) $result['message'] = "Midia enviada com sucesso";
        return $this->response($result);
    }

    public function readMessage(Request $request){
        $result = $this->whatsappService->read($request);

        if($result['status']) $result['message'] = "Audio enviado com sucesso";
        return $this->response($result);
    }

    public function updateStatus(Request $request, $id){
        $result = $this->whatsappService->updateStatus($request, $id);

        if($result['status']) $result['message'] = "Status do contato atualizado com sucesso";
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