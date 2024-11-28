<?php

namespace App\Services\Profession;

use Exception;
use App\Models\Profession;
use App\Traits\GranatumTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class ProfessionService
{
    use GranatumTrait;

    public function search($request)
    {
        try {
            $perPage = $request->input('take', 10);

            $professions = Profession::orderBy('id', 'desc');

            if($request->filled('search_term')){
                $search_term = $request->search_term;
                $professions->where(function($query) use($search_term){
                    $query->where('description', 'LIKE', "%$search_term%")
                        ->orWhere('title', 'LIKE', "%$search_term%");
                });
            }

            $professions = $professions->paginate($perPage);

            return $professions;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function getById($id)
    {
        try {

            $profession = Profession::where('id', $id)
                ->first();
            
            if(!isset($profession)) throw new Exception('Profissão não encontrada');

            return $profession;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function cards()
    {
        try {
            $totalProfessionsMonth = Profession::whereMonth('created_at', Carbon::now())
                ->count();

            $total = Profession::count();

            return [
               'status' => true,
                'data' => [
                    'totalProfessionsMonth' => $totalProfessionsMonth,
                    'totalProfessions' => $total,
                ],
            ];
            
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function create($request)
    {
        try {
            $rules = [
                'title' => ['required', 'string', 'max:255'],
                'description' => ['required', 'string'],
            ];

            $requestData = $request->all();

            $validator = Validator::make($requestData, $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $profession = Profession::create($requestData);
            
            return ['status' => true, 'data' => $profession];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function update($request, $id)
    {
        try {
            $professionToUpdate = Profession::find($id);

            if(!isset($professionToUpdate)) throw new Exception('Profissão não encontrada');

            $rules = [
                'title' => ['required', 'string', 'max:255'],
                'description' => ['required', 'string'], 
            ];

            $requestData = $request->all();

            $validator = Validator::make($requestData, $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $professionToUpdate->update($requestData);

            return ['status' => true, 'data' => $professionToUpdate];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function delete($id)
    {
        try {
            $profession = Profession::find($id);

            if (!$profession) throw new Exception('Profissão não encontrada');

            $professionTitle = $profession->title;

            $profession->delete();

            return ['status' => true, 'data' => $professionTitle];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
}
