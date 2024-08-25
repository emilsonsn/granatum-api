<?php

namespace App\Services\Construction;

use Exception;

use App\Models\Construction;

use Illuminate\Support\Facades\Validator;

class ConstructionService
{

    public function search($request)
    {
        try {
            $perPage = $request->input('take', 10);
            $search_term = $request->search_term;

            $constructions = Construction::orderBy('id', 'desc');

            if(isset($search_term)){
                $constructions->where('name', 'LIKE', "%{$search_term}%")
                    ->orWhere('description', 'LIKE', "%{$search_term}%");
            }

            $constructions = $constructions->paginate($perPage);

            return ['status' => true, 'data' => $constructions];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage()];
        }
    }

    public function create($request)
    {
        try {
            $rules = [
                'name'  => 'required|string|max:255',
                'local'  => 'required|string|max:255',
                'contractor_id'  => 'required|interger',
                'client_id'  => 'required|interger',
                'cno'  => 'required|string|interger:255',
                'description'  => 'required|string',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return ['status' => false, 'error' => $validator->errors()];
            }

            $construction = Construction::create($validator->validated());

            return ['status' => true, 'data' => $construction];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage()];
        }
    }

    public function update($request, $user_id)
    {
        try {
            $rules = [
                'name'  => 'required|string|max:255',
                'local'  => 'required|string|max:255',
                'contractor_id'  => 'required|interger',
                'client_id'  => 'required|interger',
                'cno'  => 'required|string|interger:255',
                'description'  => 'required|string',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $constructionToUpdate = Construction::find($user_id);

            if(!isset($constructionToUpdate)) throw new Exception('Obra não encontrada');

            $constructionToUpdate->update($validator->validated());

            return ['status' => true, 'data' => $constructionToUpdate];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage()];
        }
    }

    public function delete($id){
        try{
            $construction = Construction::find($id);

            if(!$construction) throw new Exception('Obra não encontrada');

            $constructionName = $construction->name;
            $construction->delete();

            return ['status' => true, 'data' => $constructionName];
        }catch(Exception $error) {
            return ['status' => false, 'error' => $error->getMessage()];
        }
    }

}
