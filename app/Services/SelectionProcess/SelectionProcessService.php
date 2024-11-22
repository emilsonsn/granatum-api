<?php

namespace App\Services\SelectionProcess;

use Exception;
use App\Models\SelectionProcess;
use App\Traits\GranatumTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SelectionProcessService
{
    use GranatumTrait;

    public function search($request)
    {
        try {
            $perPage = $request->input('take', 10);
            $is_active = $request->is_active ?? null;

            $selectionProcesses = SelectionProcess::orderBy('id', 'desc')
                ->with('vacancy');

            if($request->filled('search_term')){
                $search_term = $request->search_term;
                $selectionProcesses->where(function($query) use($search_term){
                    $query->where('title', 'LIKE', "%$search_term%");
                });
            }

            if($is_active){
                $selectionProcesses->where('is_active', $is_active);
            }

            $selectionProcesses = $selectionProcesses->paginate($perPage);

            return $selectionProcesses;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function getById($id)
    {
        try {

            $selectionProcess = SelectionProcess::where('id', $id)
                ->with('vacancy')
                ->first();
            
            if(!isset($selectionProcess)) throw new Exception('Processo seletivo não encontrado');

            return $selectionProcess;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function cards()
    {
        try {
            $totalSelectionProcesssMonth = SelectionProcess::whereMonth('created_at', Carbon::now())
                ->count();

            $activeSelectionProcesss = SelectionProcess::with('selectionProcesses', function($query){
                $query->where('is_active', true);
            })->count();
                
            $inactiveSelectionProcesss = SelectionProcess::doenstHave('selectionProcesses', function($query){
                $query->where('is_active', true);
            })->count();

            return [
               'status' => true,
                'data' => [
                    'totalSelectionProcesssMonth' => $totalSelectionProcesssMonth,
                    'activeSelectionProcesss' => $activeSelectionProcesss,
                    'inactiveSelectionProcesss' => $inactiveSelectionProcesss,                                      
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
                'total_candidates' => ['required', 'integer'],
                'available_vacancies' => ['nullable', 'integer'], 
                'user_id' => ['required', 'integer'],               
                'vacancy_id' => ['required', 'integer'],
                'is_active' => ['nullable', 'boolean'],                
            ];

            $requestData = $request->all();

            $requestData['user_id'] = Auth::user()->id;

            $validator = Validator::make($requestData, $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $selectionProcess = SelectionProcess::create($requestData);
            
            return ['status' => true, 'data' => $selectionProcess];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function update($request, $id)
    {
        try {
            $selectionProcessToUpdate = SelectionProcess::find($id);

            if(!isset($selectionProcessToUpdate)) throw new Exception('Processo seletivo não encontrado');

            $rules = [
                'title' => ['required', 'string', 'max:255'],
                'total_candidates' => ['required', 'integer'],
                'available_vacancies' => ['nullable', 'integer'],                
                'user_id' => ['required', 'integer'],
                'vacancy_id' => ['required', 'integer'],
                'is_active' => ['nullable', 'boolean'],
            ];

            $requestData = $request->all();

            $requestData['user_id'] = Auth::user()->id;

            $validator = Validator::make($requestData, $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $selectionProcessToUpdate->update($requestData);

            return ['status' => true, 'data' => $selectionProcessToUpdate];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function delete($id)
    {
        try {
            $selectionProcess = SelectionProcess::find($id);

            if (!$selectionProcess) throw new Exception('Processo seletivo não encontrado');

            $selectionProcessTitle = $selectionProcess->title;

            $selectionProcess->delete();

            return ['status' => true, 'data' => $selectionProcessTitle];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
}