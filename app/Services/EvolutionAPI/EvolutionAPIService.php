<?php

namespace App\Services\EvolutionAPI;

use Exception;
use App\Traits\EvolutionTrait;

class EvolutionAPIService
{
    use EvolutionTrait;

    public function sendMessage($request, $instance)
    {
        try {

            $request->validate([
                'number' => "required|string",
                'message' => "required|string"
            ]);

            if($request->fail()){
                throw new Exception($request->validateErros(), 400);
            }

            $number = $request->number;
            $message = $request->message;

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
