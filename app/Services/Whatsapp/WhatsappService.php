<?php

namespace App\Services\Whatsapp;

use App\Enums\MessageType;
use App\Events\EvolutionEvent;
use App\Models\ChatMessage;
use Exception;
use App\Models\WhatsappChat;
use App\Traits\EvolutionTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class WhatsappService
{
    use EvolutionTrait;

    public function searchChat($request, $instance)
    {
        try {
            $perPage = $request->input('take', 20);            
            $name = $request->input('name', null);
            $remoteJid = $request->input('remoteJid', null);
            
            $chats = WhatsappChat::where('instance', $instance)
                ->orderBy('id', 'desc');

            if($name){
                $chats->where('name','LIKE', "%$instance%");
            }

            if($remoteJid){
                $chats->where('remoteJid', $remoteJid);
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
                'instance' => "required|string",
                'sign' => "nullable|boolean",
            ];

            $validator = Validator::make($request->all(), $rules);

            if($validator->fails()){
                throw new Exception($validator->errors()->first());
            }

            $number = $request->number;
            $message = $request->message;
            $instance = $request->instance;

            if($request->filled('sign') && $request->sign){
                $fullName = Auth::user()->name;
                $nameList = explode(',' ,$fullName);
                $name = $nameList[0] . ($nameList[1] ?? '');
                $sign = "*$name*:\n";
                $message = $sign . $message;
            }

            $this->prepareDataEvolution($instance);
            $result = $this->sendMessage($number, $message);

            if(!isset($result['key'])){
                $error = $result['response']['message'][0] ?? 'Erro não identificado';                
                throw new Exception($error, 400);
            }

            $whatsappChat = WhatsappChat::where('remoteJid', $result['key']['remoteJid'])
                ->first();

            if(isset($whatsappChat)){
                $result['internalMessage'] = ChatMessage::create([
                    'remoteJid' => $whatsappChat->remoteJid,
                    'externalId' => $result['key']['id'],
                    'instanceId' => $whatsappChat->instanceId,
                    'fromMe' => true,
                    'message' => $result['message']['extendedTextMessage']['text'],
                    'messageReplied' => null,
                    'whatsapp_chat_id' => $whatsappChat->id,
                ]);
            }

            $push = [
                'event' => 'chats.update',
                'remoteJid' => $whatsappChat->remoteJid ?? null,
                'instance' => $whatsappChat->instanceId ?? null
            ];

            broadcast(new EvolutionEvent($push));

            return ['status' => true, 'data' => $result];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }    

    public function read($request)
    {
        try {
            $rules = [
                'number' => "required|string",
                'instance' => "required|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if($validator->fails()){
                throw new Exception($validator->errors()->first());
            }

            $number = $request->number;
            $instance = $request->instance;

            $messages = ChatMessage::where('remoteJid', $number)
                ->where('read', false)
                ->select('remoteJid', 'fromMe', 'externalId');                

            $messagesData = array_map(function ($message) {
                $message['id'] = $message['externalId'];
                unset($message['externalId']);
                return $message;
            }, $messages->get()->toArray());
        
            $this->prepareDataEvolution($instance);
            $result = $this->readMessages($instance, $messagesData);

            if(!isset($result['read']) || $result['read'] !== 'success'){
                $error = $result['response']['message'][0] ?? 'Erro não identificado';                
                throw new Exception($error, 400);
            }
            
            $messages->update([ 'read' => true ]);

            return ['status' => true, 'data' => $result];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function audio($request)
    {
        try {
            $rules = [
                'number' => "required|string",
                'instance' => "required|string",
                'audio' => "required|file|mimes:mp3,ogg|max:10240"
            ];

            $validator = Validator::make($request->all(), $rules);

            if($validator->fails()){
                throw new Exception($validator->errors()->first());
            }

            $audio = $request->audio;
            $number = $request->number;
            $instance = $request->instance;

            $audioPath = $audio->store('audios', 'public');

            $fullAudioPath = asset('storage/' . $audioPath);

            // Mock de envio de audio
            $fullAudioPath = 'https://tuningmania.com.br/autosom/mp3/16kHz.mp3';

            $this->prepareDataEvolution($instance);
            $result = $this->sendAudio($instance, $number, $fullAudioPath);

            if(!isset($result['key'])){
                $error = $result['response']['message'][0] ?? 'Erro não identificado';                
                throw new Exception($error, 400);
            }

            $whatsappChat = WhatsappChat::where('remoteJid', $result['key']['remoteJid'])
                ->first();

            if(isset($whatsappChat)){
                $result['internalMessage'] = ChatMessage::create([
                    'remoteJid' => $whatsappChat->remoteJid,
                    'externalId' => $result['key']['id'],
                    'instanceId' => $whatsappChat->instanceId,                    
                    'fromMe' => true,
                    'messageReplied' => null,
                    'type' => MessageType::Audio->value,
                    'path' => $audioPath,
                    'whatsapp_chat_id' => $whatsappChat->id,
                ]);
            }


            $push = [
                'event' => 'chats.update',
                'remoteJid' => $whatsappChat->remoteJid ?? null,
                'instance' => $whatsappChat->instanceId ?? null,
            ];

            broadcast(new EvolutionEvent($push));

            return ['status' => true, 'data' => $result];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function midia($request)
    {
        try {
            $rules = [
                'number' => "required|string",
                'instance' => "required|string",
                'medias' => "required|array",
                'medias.*' => 'file|max:10240',
                'message' => "nullable|string",
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $number = $request->number;
            $instance = $request->instance;
            $message = $request->message ?? '';

            if (!$request->hasFile('medias')) {
                throw new Exception('Mídias não encontradas');
            }

            $fullMidiaPaths = [];
            $mimeTypes = [];
            $mediaTypes = [];
            $categories = [];

            foreach ($request->medias as $media) {
                $mediaPath = $media->store('media', 'public');
                $fullMidiaPath = asset('storage/' . $mediaPath);
                $fullMidiaPaths[] = $fullMidiaPath;

                $filePath = storage_path('app/public/' . $mediaPath);
                $mimeType = mime_content_type($filePath);
                $mimeTypes[] = $mimeType;

                if (str_starts_with($mimeType, 'image/')) {
                    $mediaTypes[] = 'image';
                    $categories[] = 'Image';
                } elseif (str_starts_with($mimeType, 'video/')) {
                    $mediaTypes[] = 'video';
                    $categories[] = 'Video';
                } else {
                    $mediaTypes[] = 'document';
                    $categories[] = 'File';
                }
            }

            $this->prepareDataEvolution($instance);

            foreach ($fullMidiaPaths as $index => $fullMidiaPath) {
                $mimeType = $mimeTypes[$index];
                $mediaType = $mediaTypes[$index]; // Pega o mediaType dinâmico (image, video, document)
                $category = $categories[$index]; // Pega a categoria para salvar no banco

                // Mock do caminho para teste (remova esta linha quando estiver em produção)
                $fullMidiaPath = 'https://eppg.fgv.br/sites/default/files/teste.pdf';

                $result = $this->sendMedia($instance, $number, $mediaType, $fullMidiaPath, $message, $mimeType);

                if (!isset($result['key'])) {
                    $error = $result['response']['message'][0] ?? 'Erro não identificado';
                    throw new Exception($error, 400);
                }

                $whatsappChat = WhatsappChat::where('remoteJid', $result['key']['remoteJid'])->first();

                if (isset($whatsappChat)) {
                    $result['internalMessage'] = ChatMessage::create([
                        'remoteJid' => $whatsappChat->remoteJid,
                        'externalId' => $result['key']['id'],
                        'instanceId' => $whatsappChat->instanceId,
                        'fromMe' => true,
                        'messageReplied' => null,
                        'type' => $category, // Salva apenas Image, Video ou File no banco
                        'path' => $fullMidiaPath,
                        'whatsapp_chat_id' => $whatsappChat->id,
                    ]);
                }
            }

            $push = [
                'event' => 'chats.update',
                'remoteJid' => $whatsappChat->remoteJid ?? null,
                'instance' => $whatsappChat->instanceId ?? null,
            ];

            broadcast(new EvolutionEvent($push));

            return ['status' => true, 'data' => $result];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

}

