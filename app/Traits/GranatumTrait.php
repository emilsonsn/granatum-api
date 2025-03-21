<?php

namespace App\Traits;

use App\Models\Order;
use App\Models\Travel;
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

    public function getAccountBank()
    {
        $url = $this->buildUrl('contas');

        $response = Http::get($url);

        $result = $response->json();

        if(!isset($result[0])) throw new Exception('Contas bancárias não encontradas');

        return $result;
    }

    public function categories()
    {
        $url = $this->buildUrl('categorias');

        $response = Http::get($url);

        $result = $response->json();

        if(!isset($result[0])) throw new Exception('Categorias não encontradas');

        return $result;
    }

    public function tags()
    {
        $url = $this->buildUrl('tags');

        $response = Http::get($url);

        $result = $response->json();

        if(!isset($result[0])) throw new Exception('Tags não encontradas');

        return $result;
    }   
    
    public function suplier()
    {
        $url = $this->buildUrl('fornecedores');

        $response = Http::get($url);

        $result = $response->json();

        if(!isset($result[0])) throw new Exception('Fornecedores não encontrados');

        return $result;
    }       
    
    public function costCenters()
    {
        $url = $this->buildUrl('centros_custo_lucro');

        $response = Http::get($url);

        $result = $response->json();

        if(!isset($result[0])) throw new Exception('Categorias não encontradas');

        return $result;
    }

    public function createRelease(
        $categoryId,
        $accountBankId,
        $description,
        $value,
        $purchaseDate,
        $tagId,
        $suplierId,
        $costCenterId
    )
    {
        $url = $this->buildUrl('lancamentos');

        if($value > 0) $value = $value * -1;

        $payload = [
            'categoria_id' => $categoryId,
            'conta_id' => $accountBankId,
            'centro_custo_lucro_id' => $costCenterId,
            'tags' => [
                ['id' => $tagId]
            ],
            'descricao' => $description,
            'valor' => $value,
            'pessoa_id' => $suplierId,
            'data_vencimento' => Carbon::now()->addYear()->format('Y-m-d'),
            'data_pagamento' => $purchaseDate,
            'data_competencia' => $purchaseDate,
        ];

        $response = Http::post($url, $payload);

        return $response->json();
    }

    protected function sendAttachs($id, $releaseId, $model = 'order')
    {
        if($model == 'order'){
            $order = Order::find($id);
            $files = $order->files;
        }else{
            $travels = Travel::find($id);
            $files = $travels->files;
        }
    
        foreach($files as $file){
            $relativePath = str_replace(asset('storage') . '/', '', $file->path);
    
            $filePath = storage_path('app/public/' . $relativePath); 
    
            if (!file_exists($filePath)) {
                throw new Exception("Arquivo não encontrado: " . $file->name);
            }
    
            $response = Http::attach(
                    'file', file_get_contents($filePath), $file->name // Nome e conteúdo do arquivo
                )->post($this->buildUrl('anexos'), [
                    'lancamento_id' => $releaseId,
                    'filename' => $file->name
                ]);

            $response = $response->json();

            if(isset($response['errors']) && !isset($response['id'])) {
                throw new Exception ("Erro ao enviar o anexo: " . $file->name);
            }
        }

        return ['status' => true];
    }

    private function buildUrl($endpoint){
        return $this->baseUrl . "/$endpoint" .  '?access_token=' . $this->apiKey;
    }
}