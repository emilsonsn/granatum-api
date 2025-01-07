<?php

namespace App\Services\Lead;

use App\Models\FunnelStep;
use Exception;
use App\Models\Lead;
use App\Models\LeadStep;
use Illuminate\Support\Facades\Validator;

class LeadService
{

    public function search($request)
    {
        try {
            $perPage = $request->input('take', 10);

            $leads = Lead::orderBy('id', 'desc')
                ->with(['responsible', 'funnel']);

            if($request->filled('search_term')){
                $search_term = $request->search_term;
                $leads->where(function($query) use($search_term){
                    $query->where('name', 'LIKE', "%$search_term%")
                        ->orWhere('email', 'LIKE', "%$search_term%")
                        ->orWhere('phone', 'LIKE', "%$search_term%")
                        ->orWhere('origin', 'LIKE', "%$search_term%");
                });
            }

            if($request->filled('responsible_id')){
                $leads->where('responsible_id', $request->responsible_id);
            }

            $leads = $leads->paginate($perPage);

            return $leads;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function getById($id)
    {
        try {

            $lead = Lead::where('id', $id)
                ->with(['responsible'])
                ->first();
            
            if(!isset($lead)) throw new Exception('Lead não encontrado');

            return $lead;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function create($request)
    {
        try {
            $rules = [
                'name' => ['required', 'string', 'max:256'],
                'email' => ['required', 'string', 'max:256'],
                'phone' => ['required', 'string', 'max:256'],
                'origin' => ['nullable', 'string', 'max:256'],
                'observations' => ['nullable', 'string',],
                'responsible_id' => ['required', 'integer'], 
                'funnel_id' => ['nullable', 'integer']
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());            

            $lead = Lead::create($validator->validated());

            if(isset($request->funnel_id)){
                $funnelStep = FunnelStep::where('funnel_id', $request->funnel_id)
                    ->first();

                if(!isset($funnelStep)) return;

                $lead['leadStep'] = LeadStep::create([
                    'step_id' => $funnelStep->id,
                    'lead_id' => $lead->id,
                    'postition' => 1
                ]);
            }

            return ['status' => true, 'data' => $lead];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function update($request, $id)
    {
        try {
            $leadToUpdate = Lead::find($id);

            if(isset($leadToUpdate)) throw new Exception('Lead não encontrado');

            $rules = [
                'name' => ['required', 'string', 'max:256'],
                'email' => ['required', 'string', 'max:256'],
                'phone' => ['required', 'string', 'max:256'],
                'origin' => ['nullable', 'string', 'max:256'],
                'observations' => ['nullable', 'string',],
                'responsible_id' => ['required', 'integer'], 
                'funnel_id' => ['nullable', 'integer']
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $leadToUpdate = $leadToUpdate->update($validator->validated());

            if(isset($request->funnel_id)){
                $funnelStep = FunnelStep::where('funnel_id', $request->funnel_id)
                    ->first();

                if(!isset($funnelStep)) return;

                $lead['leadStep'] = LeadStep::create([
                    'step_id' => $funnelStep->id,
                    'lead_id' => $leadToUpdate->id,
                    'postition' => 1
                ]);
            }

            return ['status' => true, 'data' => $leadToUpdate];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function leadStep($request)
    {
        try {
            $rules = [
                'position' => ['nullable', 'integer'],
                'lead_id' => ['required', 'integer'],
                'step_id' => ['required', 'integer'],
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $data = $validator->validated();

            if (isset($data['position'])) {
                LeadStep::where('step_id', $data['step_id'])
                    ->where('position', '>=', $data['position'])
                    ->increment('position');
            } else {            
                $data['position'] = 1;
            }

            LeadStep::updateOrCreate([
                'step_id' => $data['step_id'],
                'lead_id' => $data['lead_id'],
            ],[
                'position' => $data['position'],
            ]);

            return ['status' => true, 'message' => 'LeadStep atualizado com sucesso'];

        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function delete($id){
        try{
            $lead = Lead::find($id);

            if(!$lead) throw new Exception('Lead não encontrado');

            $leadName = $lead->name;
            $lead->delete();

            return ['status' => true, 'data' => $leadName];
        }catch(Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
}
