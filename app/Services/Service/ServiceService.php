<?php

namespace App\Services\Service;

use Exception;
use App\Models\Service;
use Illuminate\Support\Facades\Validator;

class ServiceService
{

    public function search($request)
    {
        try {
            $perPage = $request->input('take', 10);
            $search_term = $request->search_term;

            $services = Service::orderBy('id', 'desc');

            if(isset($search_term)){
                $services->where('name', 'LIKE', "%{$search_term}%")
                    ->orWhere('type', 'LIKE', "%{$search_term}%");
            }

            $services = $services->paginate($perPage);

            return ['status' => true, 'data' => $services];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage()];
        }
    }

    public function create($request)
    {
        try {
            $rules = [
                'name' => 'required|string|max:255',
                'type' => 'required|string|max:255',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return ['status' => false, 'error' => $validator->errors()];
            }

            $service = Service::create($validator->validated());

            return $service;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage()];
        }
    }


    public function update($request, $user_id)
    {
        try {
            $rules = [
                'name' => 'required|string|max:255',
                'type' => 'required|string|max:255',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $serviceToUpdate = Service::find($user_id);

            if(!isset($serviceToUpdate)) throw new Exception('Serviço não encontrado');

            $serviceToUpdate->update($validator->validated());

            return ['status' => true, 'data' => $serviceToUpdate];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage()];
        }
    }

    public function delete($user_id)
    {
        try {

            $service = Service::find($user_id);

            if(!isset($service)) throw new Exception('Serviço não encontrado');

            $serviceName = $service->name;
            $service->delete();

            return ['status' => true, 'data' => $serviceName];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage()];
        }
    }
}
