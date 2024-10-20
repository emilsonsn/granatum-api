<?php

namespace App\Services\FunnelStep;

use Exception;
use App\Models\FunnelStep;
use Illuminate\Support\Facades\Validator;

class FunnelStepService
{

    public function search($request)
    {
        try {
            $perPage = $request->input('take', 10);

            $funnelStepSteps = FunnelStep::orderBy('id', 'desc')
                ->with(['leads']);

            if($request->filled('search_term')){
                $search_term = $request->search_term;
                $funnelStepSteps->where(function($query) use($search_term){
                    $query->where('name', 'LIKE', "%$search_term%")
                        ->orWhere('description', 'LIKE', "%$search_term%");
                });
            }

            if($request->filled('funnel_id')){
                $funnelStepSteps->where('funnel_id', $request->funnel_id);
            }

            $funnelStepSteps = $funnelStepSteps->paginate($perPage);

            return $funnelStepSteps;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function getById($id)
    {
        try {

            $funnelStep = FunnelStep::with(['leads'])
                ->find($id);
            
            if(!isset($funnelStep)) throw new Exception('Etapa do funil não encontrada');

            return $funnelStep;
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
                'funnel_id' => ['required', 'integer'],
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $funnelStep = FunnelStep::create($validator->validated());            

            return ['status' => true, 'data' => $funnelStep];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function update($request, $id)
    {
        try {
            $funnelStepToUpdate = FunnelStep::find($id);

            if(isset($funnelStepToUpdate)) throw new Exception('Etapa do funil não encontrada');

            $rules = [
                'name' => ['required', 'string', 'max:256'],
                'description' => ['required', 'string'],                
                'funnel_id' => ['required', 'integer'],
            ];
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $funnelStepToUpdate = $funnelStepToUpdate->update($validator->validated());

            return ['status' => true, 'data' => $funnelStepToUpdate];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
    
    public function delete($id){
        try{
            $funnelStep = FunnelStep::find($id);

            if(!$funnelStep) throw new Exception('Etapa do funil não encontrada');

            $funnelStepName = $funnelStep->name;
            $funnelStep->delete();

            return ['status' => true, 'data' => $funnelStepName];
        }catch(Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
}
