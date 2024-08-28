<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GranatumTrait
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('GRANATUM_API_KEY');
        $this->baseUrl = env('GRANATUM_API_BASE_URL');
    }

    public function createTransaction(array $data)
    {
        $baseUrl = $this->baseUrl;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->post("$baseUrl/transactions", $data);

        return $response->json();
    }
}
