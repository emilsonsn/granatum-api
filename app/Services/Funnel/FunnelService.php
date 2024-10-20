<?php

namespace App\Services\Funnel;

use Exception;
use App\Models\Funnel;
use App\Trait\GranatumTrait;
use Illuminate\Support\Facades\Validator;

class FunnelService
{

    public function search($request)
    {
        try {
            $perPage = $request->input('take', 10);

            $funnels = Funnel::orderBy('id', 'desc');

            if($request->filled('search_term')){
                $search_term = $request->search_term;
                $funnels->where(function($query) use($search_term){
                    $query->where('name', 'LIKE', "%$search_term%")
                        ->orWhere('description', 'LIKE', "%$search_term%");
                });
            }

            $funnels = $funnels->paginate($perPage);

            return $funnels;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function getById($id)
    {
        try {

            $funnel = Funnel::find($id);
            
            if(!isset($funnel)) throw new Exception('Funil não encontrado');

            return $funnel;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function create($request)
    {
        try {
            $rules = [
                'name' => ['required', 'string', 'max:256'],
                'description' => ['required', 'string'],                
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $funnel = Funnel::create($validator->validated());            

            return ['status' => true, 'data' => $funnel];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function update($request, $id)
    {
        try {
            $funnelToUpdate = Funnel::find($id);

            if(isset($funnelToUpdate)) throw new Exception('Funil não encontrado');

            $rules = [
                'name' => ['required', 'string', 'max:256'],
                'description' => ['required', 'string'],                
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $funnelToUpdate = $funnelToUpdate->update($validator->validated());

            return ['status' => true, 'data' => $funnelToUpdate];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
    public function delete($id){
        try{
            $funnel = Funnel::find($id);

            if(!$funnel) throw new Exception('Funil não encontrado');

            $funnelName = $funnel->name;
            $funnel->delete();

            return ['status' => true, 'data' => $funnelName];
        }catch(Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
}
