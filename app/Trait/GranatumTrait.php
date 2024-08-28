<?php

namespace App\Trait;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;

Trait GranatumTrait
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('GRANATUM_API_KEY');
        $this->baseUrl = env('GRANATUM_API_BASE_URL');
    }

    public function getCategories()
    {
        $url = $this->buildUrl('categorias');

        $response = Http::get($url);

        $categories =  $response->json();

        if(!count($categories)) throw new Exception('Categoria API não encontrada');

        foreach($categories as $category){
            if($category['descricao'] == 'API'){
                $categoryId = $category['id'];
            }
        }

        if(!isset($categoryId)) throw new Exception('Categoria API não encontrada');

        return $categoryId;
        
    }

    public function getAccountBank()
    {
        $url = $this->buildUrl('contas');

        $response = Http::get($url);

        $result = $response->json();

        if(!isset($result[0])) throw new Exception('Contas bancárias não encontradas');

        return $result[0]['id'];
    }

    public function createRelease($categoryId, $accountBankId, $description, $value, $purchaseDate)
    {
        $url = $this->buildUrl('lancamentos');

        $payload = [
            'categoria_id' => $categoryId,
            'conta_id' => $accountBankId,
            'descricao' => $description,
            'valor' => $value,
            'data_vencimento' => Carbon::now()->addYear()->format('Y-m-d'),
            'data_pagamento' => $purchaseDate
        ];

        $response = Http::post($url, $payload);

        return $response->json();
    }

    private function buildUrl($endpoint){
        return $this->baseUrl . "/$endpoint" .  '?access_token=' . $this->apiKey;
    }
}
