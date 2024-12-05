<?php

namespace App\Services\Websocket;

use Exception;
use App\Models\ChatMessage;
use App\Models\WhatsappChat;

class WebsocketService
{

    public function handle($request)
    {
        try{
            switch($request['event']){
                case 'messages.upsert':
                    $this->importCharOrMessage($request);
                    break;
                case "contacts.update":
                    $this->updateChat($request);
                    break;
            }
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function updateChat($request){
        try{
            $remoteJid = $request['data']['remoteJid'];
    
            $chat = WhatsappChat::updateOrcreate(
                [
                    'remoteJid' => $remoteJid,
                ],
                [
                    'name' => $request['data']['pushName'],
                    'instance' => $request['instance'] ?? null,
                    'instanceId' => $request['data']['instanceId'],
                    'profilePicUrl' => $request['data']['profilePicUrl'],
                    'apiKey' => $request['apikey'] ,
                ]
            );
            
            return $chat;        
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function importCharOrMessage($request){
        try{
            $remoteJid = $request['data']['key']['remoteJid'];
            $chat = WhatsappChat::where('remoteJid', $remoteJid)->first();
    
            if(!$chat){
                $chat = WhatsappChat::updateOrcreate(
                    [
                        'remoteJid' => $remoteJid,
                    ],
                    [
                        'name' => $request['data']['pushName'],
                        'instance' => $request['instance'] ?? null,
                        'instanceId' => $request['data']['instanceId'],
                        'apiKey' => $request['apikey'],
                    ]
                );
            }
            
            $message = ChatMessage::create([
                'remoteJid' => $chat->remoteJid,
                'externalId' => $request['data']['key']['id'],
                'instanceId' => $request['data']['instanceId'],
                'fromMe' => $request['data']['key']['fromMe'],
                'message' => $request['data']['message']['conversation'],
                'messageReplied' => $request['data']['contextInfo']['stanzaId'] ?? null,
                'whatsapp_chat_id' => $chat->id,
            ]);
    
            return $message;
        }catch(Exception $error){
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
}