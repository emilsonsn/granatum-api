<?php

namespace App\Services\Label;

use Exception;
use App\Models\Label;
use Illuminate\Support\Facades\Validator;

class LabelService
{

    public function search($request)
    {
        try {
            $perPage = $request->input('take', 10);

            $labels = Label::orderBy('id', 'desc');

            if($request->filled('text')){                
                $labels->where('text', 'LIKE', "%$request->search_term;%");
            }
            
            $labels = $labels->paginate($perPage);

            return $labels;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function getById($id)
    {
        try {

            $labels = Label::find($id);
            
            if(!isset($labels)) throw new Exception('Etiqueta não encontrada');

            return $labels;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function create($request)
    {
        try {
            $rules = [
                'text' => ['required', 'string', 'max:255'],
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $label = Label::create($validator->validated());            

            return ['status' => true, 'data' => $label];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function update($request, $id)
    {
        try {
            $labelToUpdate = Label::find($id);

            if(isset($labelToUpdate)) throw new Exception('Etiqueta não encontrada');

            $rules = [
                'text' => ['required', 'string', 'max:255'],
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $labelToUpdate = $labelToUpdate->update($validator->validated());

            return ['status' => true, 'data' => $labelToUpdate];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
    public function delete($id){
        try{
            $label = Label::find($id);

            if(!$label) throw new Exception('Etiqueta não encontrada');

            $labelsText = $label->text;
            $label->delete();

            return ['status' => true, 'data' => $labelsText];
        }catch(Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
}
