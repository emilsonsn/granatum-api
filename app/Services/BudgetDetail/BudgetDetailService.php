<?php

namespace App\Services\BudgetDetailDetail;

use Exception;
use App\Models\BudgetDetail;
use Illuminate\Support\Facades\Validator;

class BudgetDetailDetailService
{

    public function getById($id)
    {
        try {
            $budgetDetail = BudgetDetail::with('budget')->find($id);
            
            if(!isset($budgetDetail)) throw new Exception('Orçamento não encontrado');

            return $budgetDetail;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function create($request)
    {
        try {
            $rules = [
                'budget_id' => ['nullable', 'string'],
                'presentation_text_1' => ['nullable', 'string'],
                'presentation_text_2' => ['nullable', 'string'],
                'presentation_text_3' => ['nullable', 'string'],
                'development_text_1' => ['nullable', 'string'],
                'development_text_2' => ['nullable', 'string'],
                'development_text_3' => ['nullable', 'string'],
                'development_text_4' => ['nullable', 'string'],
                'payment_methods' => ['nullable', 'string'],
                'conclusion_text_1' => ['nullable', 'string'],
                'conclusion_text_2' => ['nullable', 'string'],
                // Images
                'presentation_image_1' => ['nullable', 'image', 'max:8192'],
                'presentation_image_2' => ['nullable', 'image', 'max:8192'],
                'presentation_image_3' => ['nullable', 'image', 'max:8192'],
                'development_image_1' => ['nullable', 'image', 'max:8192'],
                'development_image_2' => ['nullable', 'image', 'max:8192'],
                'development_image_3' => ['nullable', 'image', 'max:8192'],
                'development_image_4' => ['nullable', 'image', 'max:8192'],
                'conclusion_image_1' => ['nullable', 'image', 'max:8192'],
                'conclusion_image_2' => ['nullable', 'image', 'max:8192'],
                'cover' => ['nullable', 'image', 'max:8192'],
                'final_cover' => ['nullable', 'image', 'max:8192'],
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $validatedData = $validator->validated();

            $imageFields = [
                'presentation_image_1',
                'presentation_image_2',
                'presentation_image_3',
                'development_image_1',
                'development_image_2',
                'development_image_3',
                'development_image_4',
                'conclusion_image_1',
                'conclusion_image_2',
                'cover',
                'final_cover',
            ];
    
            foreach ($imageFields as $field) {
                if ($request->hasFile($field)) {
                    $path = $request->file($field)->store('public/budgets/images');
                    $validatedData[$field] = str_replace('public/', 'storage/', $path);
                }
            }
    
            $budgetDetail = BudgetDetail::create($validatedData);
    
            return ['status' => true, 'data' => $budgetDetail];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function update($request, $id)
    {
        try {
            $budgetDetailToUpdate = BudgetDetail::find($id);

            if(!isset($budgetDetailToUpdate)) throw new Exception('Orçamento não encontrado');
            
            $rules = [
                'budget_id' => ['nullable', 'string'],
                'presentation_text_1' => ['nullable', 'string'],
                'presentation_text_2' => ['nullable', 'string'],
                'presentation_text_3' => ['nullable', 'string'],
                'development_text_1' => ['nullable', 'string'],
                'development_text_2' => ['nullable', 'string'],
                'development_text_3' => ['nullable', 'string'],
                'development_text_4' => ['nullable', 'string'],
                'payment_methods' => ['nullable', 'string'],
                'conclusion_text_1' => ['nullable', 'string'],
                'conclusion_text_2' => ['nullable', 'string'],
                // Images
                'presentation_image_1' => ['nullable', 'image', 'max:8192'],
                'presentation_image_2' => ['nullable', 'image', 'max:8192'],
                'presentation_image_3' => ['nullable', 'image', 'max:8192'],
                'development_image_1' => ['nullable', 'image', 'max:8192'],
                'development_image_2' => ['nullable', 'image', 'max:8192'],
                'development_image_3' => ['nullable', 'image', 'max:8192'],
                'development_image_4' => ['nullable', 'image', 'max:8192'],
                'conclusion_image_1' => ['nullable', 'image', 'max:8192'],
                'conclusion_image_2' => ['nullable', 'image', 'max:8192'],
                'cover' => ['nullable', 'image', 'max:8192'],
                'final_cover' => ['nullable', 'image', 'max:8192'],
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $validatedData = $validator->validated();

            $imageFields = [
                'presentation_image_1',
                'presentation_image_2',
                'presentation_image_3',
                'development_image_1',
                'development_image_2',
                'development_image_3',
                'development_image_4',
                'conclusion_image_1',
                'conclusion_image_2',
                'cover',
                'final_cover',
            ];
    
            foreach ($imageFields as $field) {
                if ($request->hasFile($field)) {
                    $path = $request->file($field)->store('public/budgets/images');
                    $validatedData[$field] = str_replace('public/', 'storage/', $path);
                }
            }

            $budgetDetailToUpdate = $budgetDetailToUpdate->update($validatedData);

            return ['status' => true, 'data' => $budgetDetailToUpdate];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
}
