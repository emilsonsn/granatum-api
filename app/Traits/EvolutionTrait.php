<?php

namespace App\Traits;

use GuzzleHttp\Client;

trait EvolutionTrait
{
    protected $baseUrl;
    protected $apiKey;
    protected $accountName;
    protected $client;

    public function prepareDataEvolution($instance)
    {
        $this->baseUrl = "https://evo.localizadordeeditais.com.br";
        $this->apiKey = env('EVO_API_KEY');
        $this->accountName = $instance;
        $this->client = new Client();
    }

    public function sendMessage($number, $message)
    {
        $url = $this->baseUrl . "/message/sendText/{$this->accountName}";
        $data = [
            'headers' => [
                'apiKey' => $this->apiKey,
            ],
            'json' => [
                'number' => $number,
                'text' => $message,
            ]
        ];

        $response = $this->client->request('POST', $url, $data);
        return $response->getBody()->getContents();
    }

    public function updateMessage($instance, $number, $text, $remoteJid, $fromMe, $id)
    {
        $url = $this->baseUrl . "/chat/updateMessage/{$instance}";
        $data = [
            'headers' => [
                'apiKey' => $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'number' => $number,
                'text' => $text,
                'key' => [
                    'remoteJid' => $remoteJid,
                    'fromMe' => $fromMe,
                    'id' => $id,
                ],
            ]
        ];

        $response = $this->client->request('PUT', $url, $data);
        return $response->getBody()->getContents();
    }

    public function fetchInstances()
    {
        $url = $this->baseUrl . "/instance/fetchInstances";
        $data = [
            'headers' => [
                'apiKey' => $this->apiKey,
            ]
        ];

        $response = $this->client->request('GET', $url, $data);
        return $response->getBody()->getContents();
    }

    public function findMessages($instance, $criteria)
    {
        $url = $this->baseUrl . "/chat/findMessages/{$instance}";
        $data = [
            'headers' => [
                'apiKey' => $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'where' => $criteria,
            ]
        ];

        $response = $this->client->request('POST', $url, $data);
        return $response->getBody()->getContents();
    }

    public function setPresence($instance, $presence)
    {
        $url = $this->baseUrl . "/instance/setPresence/{$instance}";
        $data = [
            'headers' => [
                'apiKey' => $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'presence' => $presence,
            ]
        ];

        $response = $this->client->request('POST', $url, $data);
        return $response->getBody()->getContents();
    }

    public function sendMedia($instance, $number, $mediaType, $media, $caption = null, $mimeType = null, $fileName = null)
    {
        $url = $this->baseUrl . "/message/sendMedia/{$instance}";
        $data = [
            'headers' => [
                'apiKey' => $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'number' => $number,
                'mediatype' => $mediaType,
                'media' => $media,
                'caption' => $caption,
                'mimetype' => $mimeType,
                'fileName' => $fileName,
            ]
        ];

        $response = $this->client->request('POST', $url, $data);
        return $response->getBody()->getContents();
    }

    public function sendAudio($instance, $number, $audio, $delay = null, $encoding = null, $quoted = null, $mentionsEveryOne = null, $mentioned = null)
    {
        $url = $this->baseUrl . "/message/sendWhatsAppAudio/{$instance}";
        $data = [
            'headers' => [
                'apiKey' => $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'number' => $number,
                'audio' => $audio,
                'delay' => $delay,
                'encoding' => $encoding,
                'quoted' => $quoted,
                'mentionsEveryOne' => $mentionsEveryOne,
                'mentioned' => $mentioned,
            ]
        ];

        $response = $this->client->request('POST', $url, $data);
        return $response->getBody()->getContents();
    }

    public function sendSticker($instance, $number, $sticker, $delay = null, $quoted = null, $mentionsEveryOne = null, $mentioned = null)
    {
        $url = $this->baseUrl . "/message/sendSticker/{$instance}";
        $data = [
            'headers' => [
                'apiKey' => $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'number' => $number,
                'sticker' => $sticker,
                'delay' => $delay,
                'quoted' => $quoted,
                'mentionsEveryOne' => $mentionsEveryOne,
                'mentioned' => $mentioned,
            ]
        ];

        $response = $this->client->request('POST', $url, $data);
        return $response->getBody()->getContents();
    }
}
