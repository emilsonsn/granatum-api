<?php

namespace App\Services\Whatsapp;

use App\Models\ChatMessage;
use Exception;
use App\Models\WhatsappChat;
use App\Traits\EvolutionTrait;

class WhatsappService
{

    use EvolutionTrait;

    public function searchChat($request, $instance)
    {
        try {
            $perPage = $request->input('take', 20);            
            $name = $request->input('name', null);
            
            $chats = WhatsappChat::where('instance', $instance)
                ->orderBy('id', 'desc');                

            if($name){
                $chats->where('name','LIKE', "%$instance%");
            }

            $chats = $chats->with('lastMessage')
                ->paginate($perPage);

            return $chats;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function searchMessage($request, $remoteJid)
    {
        try {
            $perPage = $request->input('take', 20);             
            $message = $request->input('message', null);
            $status = $request->input('status', null);
            
            $messages = ChatMessage::where('remoteJid', $remoteJid)
                ->orderBy('id', 'desc');            

            if($message){
                $messages->where('message', 'LIKE', "%$message%");
            }

            if($status){
                $messages->where('status', $status);
            }

            $status = $messages->paginate($perPage);

            return $messages;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function createMessage($request)
    {
        try {

            $request->validate([
                'number' => "required|string",
                'message' => "required|string",
                'instance' => "required|string"
            ]);

            if($request->fail()){
                throw new Exception($request->validateErros(), 400);
            }

            $number = $request->number;
            $message = $request->message;
            $instance = $request->instance;

            $this->prepareDataEvolution($instance);
            $result = $this->sendMessage($number, $message);

            if(isset($result['status']) || $result['status'] != 200){
                $error = $result['response']['message'][0] ?? 'Erro não identificado';                
                throw new Exception($error, 400);
            }

            return ['status' => true, 'data' => $result];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

}
