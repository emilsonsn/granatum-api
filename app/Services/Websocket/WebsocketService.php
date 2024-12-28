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

    public function importCharOrMessage($request) {
        try {
            $remoteJid = $request['data']['key']['remoteJid'];
            $chat = WhatsappChat::where('remoteJid', $remoteJid)->first();
    
            if (!$chat) {
                $chat = WhatsappChat::updateOrCreate(
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
    
            $type = 'Text';
            $relativePath = null;
    
            if (isset($request['data']['message']['audioMessage'])) {
                $messageType = 'audioMessage';
                $type = 'audio';
                $mimeType = $request['data']['message']['audioMessage']['mimetype'];
                $base64Data = $request['data']['message']['base64'];
            } elseif (isset($request['data']['message']['videoMessage'])) {
                $type = 'video';
                $messageType = 'videoMessage';
                $mimeType = $request['data']['message']['videoMessage']['mimetype'];
                $base64Data = $request['data']['message']['base64'];
            } elseif (isset($request['data']['message']['imageMessage'])) {
                $type = 'image';
                $messageType = 'imageMessage';
                $mimeType = $request['data']['message']['imageMessage']['mimetype'];
                $base64Data = $request['data']['message']['base64'];
            } elseif (isset($request['data']['message']['documentWithCaptionMessage'])) {                            
                $type = 'file';
                $messageType = 'fileMessage';
                $mimeType = $request['data']['message']['documentWithCaptionMessage']['message']['documentMessage']['mimetype'];
                $base64Data = $request['data']['message']['base64'];
            } elseif(isset($request['data']['message']['documentMessage'])) {
                $type = 'file';
                $messageType = 'fileMessage';
                $mimeType = $request['data']['message']['documentMessage']['mimetype'];
                $base64Data = $request['data']['message']['base64'];
            }else {
                $messageType = null;
                $mimeType = null;
                $base64Data = null;
            }
    
            if ($mimeType && $base64Data) {
                $extension = $this->getFileExtensionFromMimeType($mimeType);
    
                if (!$extension) {
                    throw new Exception("Tipo de arquivo não suportado: $mimeType");
                }
    
                $fileName = uniqid($messageType . '_', true) . '.' . $extension;
    
                $type = ucfirst($type);
                $relativePath = strtolower($type) . 's/' . $fileName;
    
                $outputFilePath = storage_path('app/public/' . $relativePath);
    
                $this->saveBase64AudioToFile($base64Data, $outputFilePath);
            }
    
            $message = ChatMessage::create([
                'remoteJid' => $chat->remoteJid,
                'externalId' => $request['data']['key']['id'],
                'instanceId' => $request['data']['instanceId'],
                'fromMe' => $request['data']['key']['fromMe'],
                'message' => $request['data']['message']['conversation'] ?? '',
                'messageReplied' => $request['data']['contextInfo']['stanzaId'] ?? null,
                'whatsapp_chat_id' => $chat->id,
                'type' => $type,
                'path' => $relativePath,
            ]);
    
             return $message;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    private function getFileExtensionFromMimeType($mimeType) {
        $mimeToExtension = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/bmp' => 'bmp',
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'application/vnd.ms-powerpoint' => 'ppt',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
            'application/zip' => 'zip',
            'application/x-rar-compressed' => 'rar',
            'audio/mpeg' => 'mp3',
            'audio/ogg' => 'ogg',
            'audio/aac' => 'aac',
            'audio/wav' => 'wav',
            'video/mp4' => 'mp4',
            'video/webm' => 'webm',
            'video/3gpp' => '3gp',
            'text/plain' => 'txt',
            'text/html' => 'html',
            'application/json' => 'json',
            'audio/ogg; codecs=opus' => 'ogg',
            'audio/ogg;' => 'ogg',
        ];
    
        return $mimeToExtension[$mimeType] ?? null;
    }
    
    private function saveBase64AudioToFile($base64Audio, $outputFilePath) {
        $audioData = preg_replace('/^data:[a-zA-Z0-9\/]+;base64,/', '', $base64Audio);
    
        $decodedData = base64_decode($audioData);
    
        if ($decodedData === false) {
            throw new Exception('Falha ao decodificar o Base64.');
        }
    
        $directory = dirname($outputFilePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    
        if (file_put_contents($outputFilePath, $decodedData) === false) {
            throw new Exception("Falha ao salvar o arquivo em $outputFilePath.");
        }
    
        return $outputFilePath;
    }
    

}