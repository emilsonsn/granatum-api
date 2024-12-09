<?php

namespace App\Services\Budget;

use Exception;
use App\Models\Budget;
use App\Models\BudgetDetail;
use Illuminate\Support\Facades\Validator;

class BudgetService
{

    public function search($request)
    {
        try {
            $perPage = $request->input('take', 10);

            $budgets = Budget::orderBy('id', 'desc');

            if($request->filled('search_term')){
                $search_term = $request->search_term;
                $budgets->where(function($query) use($search_term){
                    $query->where('title', 'LIKE', "%$search_term%")
                        ->orWhere('description', 'LIKE', "%$search_term%");
                });
            }

            $budgets = $budgets->paginate($perPage);

            return $budgets;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function getById($id)
    {
        try {
            $budget = Budget::find($id);
            
            if(!isset($budget)) throw new Exception('Orçamento não encontrado');

            return $budget;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function create($request)
    {
        try {
            $rules = [
                'title' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string', 'max:255'],
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $budget = Budget::create($validator->validated());

            $budget['details'] = BudgetDetail::create([
                'budget_id' => $budget->id,                
            ]);            

            return ['status' => true, 'data' => $budget];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function update($request, $id)
    {
        try {
            $budgetToUpdate = Budget::find($id);

            if(!isset($budgetToUpdate)) throw new Exception('Orçamento não encontrado');
            
            $rules = [
                'title' => ['required', 'string', 'max:255'],
                'description' => ['nullable', 'string', 'max:255'],
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $budgetToUpdate = $budgetToUpdate->update($validator->validated());

            return ['status' => true, 'data' => $budgetToUpdate];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
    public function delete($id){
        try{
            $budget = Budget::find($id);

            if(!$budget) throw new Exception('Orçamento não encontrado');

            $budgetName = $budget->name;
            $budget->budgetDetails()->delete();
            $budget->delete();

            return ['status' => true, 'data' => $budgetName];
        }catch(Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
}
