<?php

namespace App\Services\Candidate;

use Exception;

use App\Models\Candidate;
use App\Models\CandidateAttachment;
use App\Models\CandidateStatus;
use App\Models\Status;
use Illuminate\Support\Facades\Validator;

class CandidateService
{

    public function search($request)
    {
        try {
            $perPage = $request->input('take', 10);
            $search_term = $request->search_term;
            $is_active = $request->is_active;
            $profession_id = $request->profession_id;

            $candidates = Candidate::orderBy('id', 'desc')
                ->with('profession', 'files');

            if(isset($search_term)){
                $candidates->where('name', 'LIKE', "%{$search_term}%")
                    ->orWhere('surname', 'LIKE', "%{$search_term}%")
                    ->orWhere('email', 'LIKE', "%{$search_term}%")
                    ->orWhere('cpf', 'LIKE', "%{$search_term}%")
                    ->orWhere('phone', 'LIKE', "%{$search_term}%");
            }

            if(isset($is_active)){
                $candidates->where('is_active', $is_active);
            }

            if(isset($profession_id)){
                $candidates->where('profession_id', $profession_id);
            }

            $candidates = $candidates->paginate($perPage);

            return $candidates;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function cards()
    {
        try {
            $totalCandidateActive = Candidate::where('is_active', true)
                ->count();

            $totalCandidateInactive = Candidate::where('is_active', false)
                ->count();

            $total = Candidate::count();

            return [
               'status' => true,
                'data' => [
                    'totalCandidates' => $total,
                    'totalCandidatesActive' => $totalCandidateActive,
                    'totalCandidatesInactive' => $totalCandidateInactive,
                ],
            ];
            
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function create($request)
    {
        try {
            $request['attachments'] = $request['attachments'] == 'null' ? null : $request['attachments'];
            $request['is_active'] = $request['is_active'] !== 'false' ? true : false;
            $request['processes'] = $request['processes'] == 'null' ? null : $request['processes'];

            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'surname' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'max:255'],
                'cpf' => ['required', 'string', 'max:255'],
                'phone' => ['required', 'string', 'max:20'],
                'cep' => ['required','string', 'max:255'],
                'state' => ['required','string', 'max:255'],
                'city' => ['required','string', 'max:255'],
                'neighborhood' => ['required','string', 'max:255'],
                'street' => ['required','string', 'max:255'],
                'number' => ['required','string', 'max:255'],
                'marital_status' => ['required','string', 'max:255'],
                'is_active' => ['nullable', 'boolean'],
                'profession_id' => ['required', 'integer'],
                'attachments' => ['nullable', 'array'],
                'processes' => ['nullable', 'string']
            ];            

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                throw new Exception($validator->errors(), 400);
            }

            if(Candidate::where('cpf', $request->cpf)->count()){
                throw new Exception('Candidato com esse cpf já cadastrado', 400);
            }

            $data = $validator->validated();

            $candidate = Candidate::create($data);

            if (isset($request->attachments)) {
                foreach ($request->attachments as $attachment) {
                    $filename = $attachment->getClientOriginalName();
                    $path = $attachment->store('attachments');
            
                    $processes = $request->processes ? explode(',', $request->processes) : [null];
            
                    foreach ($processes as $process) {
                        CandidateAttachment::create([
                            'name' => $filename,
                            'path' => $path,
                            'candidate_id' => $candidate->id,
                            'selection_process_id' => $process ?? null
                        ]);
                    }
                }
            }            

            if (isset($request->processes)) {
                $processes = explode(',' ,$request->processes);
                foreach ($processes as $process) {
                    $statusId = Status::where('selection_process_id', $process)
                        ->pluck('id')
                        ->first();
            
                    if ($statusId) {
                        CandidateStatus::create([
                            'candidate_id' => $candidate->id,
                            'status_id' => $statusId
                        ]);
                    }
                }
            }

            return ['status' => true, 'data' => $candidate];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function update($request, $user_id)
    {
        try {
            $request['attachments'] = $request['attachments'] == 'null' ? null : $request['attachments'];
            $request['is_active'] = $request['is_active'] == 'null' ? null : $request['is_active'];
            $request['processes'] = $request['processes'] == 'null' ? null : $request['processes'];

            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'surname' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'max:255'],
                'cpf' => ['required', 'string', 'max:255'],
                'phone' => ['required', 'string', 'max:20'],
                'cep' => ['required','string', 'max:255'],
                'state' => ['required','string', 'max:255'],
                'city' => ['required','string', 'max:255'],
                'neighborhood' => ['required','string', 'max:255'],
                'street' => ['required','string', 'max:255'],
                'number' => ['required','string', 'max:255'],
                'marital_status' => ['required','string', 'max:255'],
                'is_active' => ['nullable', 'boolean'],
                'profession_id' => ['required', 'integer'],
                'attachments' => ['nullable', 'array'],
                'processes' => ['nullable', 'string']
            ];            

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $candidateToUpdate = Candidate::find($user_id);

            if(!isset($candidateToUpdate)) throw new Exception('Candidato não encontrado');

            $candidateToUpdate->update($validator->validated());

            if (isset($request->attachments)) {
                foreach ($request->attachments as $attachment) {
                    $filename = $attachment->getClientOriginalName();
                    $path = $attachment->store('attachments');
            
                    $processes = $request->processes ? explode(',', $request->processes) : [null];
            
                    foreach ($processes as $process) {
                        CandidateAttachment::create([
                            'name' => $filename,
                            'path' => $path,
                            'candidate_id' => $candidateToUpdate->id,
                            'selection_process_id' => $process ?? null
                        ]);
                    }
                }
            }

            if (isset($request->processes)) {
                $processes = explode(',' ,$request->processes);
                foreach ($processes as $process) {
                    $statusId = Status::where('selection_process_id', $process)
                        ->pluck('id')
                        ->first();
            
                    if ($statusId) {
                        $candidateStatus = [
                            'candidate_id' => $candidateToUpdate->id,
                            'status_id' => $statusId
                        ];
                        CandidateStatus::updateOrcreate($candidateStatus, $candidateStatus);
                    }
                }
            }

            return ['status' => true, 'data' => $candidateToUpdate];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function delete($id){
        try{
            $candidate = Candidate::find($id);

            if(!$candidate) throw new Exception('Candidato não encontrado');

            $candidateName = $candidate->name;
            $candidate->delete();

            return ['status' => true, 'data' => $candidateName];
        }catch(Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
}