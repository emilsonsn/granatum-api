<?php

namespace App\Services\BudgetGenerated;

use Exception;
use App\Models\BudgetGenerated;
use App\Models\BudgetVariable;
use Illuminate\Support\Facades\Validator;

class BudgetGeneratedService
{

    public function search($request)
    {
        try {
            $perPage = $request->input('take', 10);
            $status = $request->status ?? null;

            $budgetGenerateds = BudgetGenerated::with(['budget', 'lead'])
                ->orderBy('id', 'desc');

            if($request->filled('search_term')){
                $search_term = $request->search_term;
                $budgetGenerateds->where(function($query) use($search_term){
                    $query->where('description', 'LIKE', "%$search_term%");
                });
            }

            if(isset($status)){
                $budgetGenerateds->where('status', $status);
            }

            $budgetGenerateds = $budgetGenerateds->paginate($perPage);

            return $budgetGenerateds;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function getById($id)
    {
        try {
            $budgetGenerated = BudgetGenerated::find($id);
            
            if(!isset($budgetGenerated)) throw new Exception('Orçamento não encontrado');

            return $budgetGenerated;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function create($request)
    {
        try {
            $rules = [
                'description' => ['required', 'string', 'max:255'],
                'budget_id' => ['required', 'integer'],
                'lead_id' => ['required', 'integer'],
                'status' => ['nullable', 'in:Generated,Delivered,Approved,Desapproved'],
                'variables' => ['nullable', 'array'],
                'variables.*' => ['array']
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $data = $validator->validated();

            $budgetGenerated = BudgetGenerated::create($data);

            $variables = $request->variables ?? [];

            foreach($variables as $variable){
                $budget['variables'][] = BudgetVariable::updateOrCreate([
                    'key' => $variable['key']
                ], $variable);                
            }
         
            return ['status' => true, 'data' => $budgetGenerated];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function update($request, $id)
    {
        try {
            $budgetGeneratedToUpdate = BudgetGenerated::find($id);

            if(!isset($budgetGeneratedToUpdate)) throw new Exception('Orçamento não encontrado');
            
            $rules = [
                'description' => ['required', 'string', 'max:255'],
                'budget_id' => ['required', 'integer'],
                'lead_id' => ['required', 'integer'],
                'status' => ['nullable', 'in:Generated,Delivered,Approved,Desapproved'],
                'variables' => ['nullable', 'array'],
                'variables.*' => ['array']
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $data = $validator->validated();

            $budgetGeneratedToUpdate = $budgetGeneratedToUpdate->update($data);

            $variables = $request->variables ?? [];

            foreach($variables as $variable){
                $budget['variables'][] = BudgetVariable::updateOrCreate([
                    'key' => $variable['key']
                ], $variable);                
            }

            return ['status' => true, 'data' => $budgetGeneratedToUpdate];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
    
    public function delete($id){
        try{
            $budgetGenerated = BudgetGenerated::find($id);

            if(!$budgetGenerated) throw new Exception('Orçamento não encontrado');

            $budgetGeneratedName = $budgetGenerated->name;
            $budgetGenerated->budgetDetails()->delete();
            $budgetGenerated->delete();

            return ['status' => true, 'data' => $budgetGeneratedName];
        }catch(Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
    

    public function deleteVariable($id){
        try{
            $budgetVariable = BudgetVariable::find($id);

            if(!$budgetVariable) throw new Exception('Variável não encontrada');

            $variableKey = $budgetVariable->key;
            $budgetVariable->budgetDetails()->delete();
            $budgetVariable->delete();

            return ['status' => true, 'data' => $variableKey];
        }catch(Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
}
