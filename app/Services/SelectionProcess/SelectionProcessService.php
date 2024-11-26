<?php

namespace App\Services\SelectionProcess;

use App\Models\Candidate;
use App\Models\CandidateStatus;
use Exception;
use App\Models\SelectionProcess;
use App\Models\Status;
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
                ->with('vacancy', 'statuses');

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
                ->with('vacancy', 'statuses')
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

            $activeSelectionProcesss = SelectionProcess::where('is_active', true)->count();
            $inactiveSelectionProcesss = SelectionProcess::where('is_active', false)->count();

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

            $statusList = [
                [ 'title' => 'Candidato' , 'color' => '#FF5733'], // Laranja
                [ 'title' => 'Documentos' , 'color' => '#3498DB'], // Azul
                [ 'title' => 'Entrevista' , 'color' => '#9B59B6'], // Verde
                [ 'title' => 'Pré-Contrato' , 'color' => '#F1C40F'], // Amarelo
                [ 'title' => 'Contrato' , 'color' =>'#2ECC71'], // Roxo
            ];
            
            $selectionProcessStatus = [];
            foreach($statusList as $status){
                $selectionProcessStatus[] = Status::create([
                    'title' => $status['title'],
                    'color' => $status['color'],
                   'selection_process_id' => $selectionProcess->id,
                ]);
            }

            $candidates = Candidate::where('profession_id', $selectionProcess->vacancy->profession_id)
                ->where('is_active', true)
                ->get();

            foreach($candidates as $candidate){
                CandidateStatus::create([
                    'candidate_id' => $candidate->id,
                    'status_id' => $selectionProcessStatus[0]->id
                ]);                
            }

            $selectionProcess['status'] = $selectionProcessStatus;
            
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

    public function updateStatus($request)
    {
        try {
            $candidate_id = $request->candidate_id;
            $status_id = $request->status_id;

            $statusToUpdate = CandidateStatus::where('candidate_id', $candidate_id)
                ->first();

            if(!isset($statusToUpdate)) throw new Exception('Etapa do candidato não encontrada');

            $statusToUpdate->update([
                "status_id" => $status_id
            ]);

            return ['status' => true, 'data' => $statusToUpdate];
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
