<?php

namespace App\Services\Whatsapp;

use App\Models\ChatMessage;
use Exception;
use App\Models\WhatsappChat;
use App\Traits\EvolutionTrait;
use Illuminate\Support\Facades\Validator;

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

            $messages = $messages->paginate($perPage);

            return $messages;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function updateStatus($request, $id)
    {
        try {
            $rules = [
                'status' => ['required', 'string', 'in:Waiting,Responding,Finished']
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }
            
            $chat = WhatsappChat::find($id);                             

            if(!isset($chat)){
                throw new Exception('Chat não encontrado');
            }

            $chat->update($validator->validated());

            return $chat;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function createMessage($request)
    {
        try {
            $rules = [
                'number' => "required|string",
                'message' => "required|string",
                'instance' => "required|string"
            ];

            $validator = Validator::make($request->all(), $rules);

            if($validator->fails()){
                throw new Exception($validator->errors()->first());
            }

            $number = $request->number;
            $message = $request->message;
            $instance = $request->instance;

            $this->prepareDataEvolution($instance);
            $result = $this->sendMessage($number, $message);

            if(!isset($result['key'])){
                $error = $result['response']['message'][0] ?? 'Erro não identificado';                
                throw new Exception($error, 400);
            }

            return ['status' => true, 'data' => $result];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

}
