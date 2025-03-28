<?php

namespace App\Traits;

use App\Models\Order;
use App\Models\Travel;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;
use Log;

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
        $orderDate,
        $purchaseDate,
        $dueDate,
        $tagId,
        $suplierId,
        $costCenterId
    ): mixed
    {
        $url = $this->buildUrl('lancamentos');

        if($value > 0) $value = $value * -1;

        $payload = [
            'categoria_id' => $categoryId,
            'conta_id' => $accountBankId,
            'descricao' => $description,
            'valor' => $value,
            'data_competencia' => $orderDate ?? $purchaseDate ?? Carbon::now()->addYear()->format('Y-m-d'),
            'data_vencimento' => $purchaseDate ?? $dueDate ?? Carbon::now()->addYear()->format('Y-m-d'),
            'data_pagamento' => $purchaseDate ?? null,
        ];

        if($tagId) $payload['tags'] = [ ['id' => $tagId] ];
        if($suplierId) $payload['pessoa_id'] = $suplierId;
        if($costCenterId) $payload['centro_custo_lucro_id'] = $costCenterId;

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
                Log::error('File does not exist');
                throw new Exception("Arquivo não encontrado: " . $file->name);
            }
    
            $response = Http::attach(
                    'file', file_get_contents($filePath), $file->name // Nome e conteúdo do arquivo
                )->post($this->buildUrl('anexos'), [
                    'lancamento_id' => $releaseId,
                    'filename' => $file->name
                ]);

            $response = $response->json();
            Log::info('Response ', $response ?? []);

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