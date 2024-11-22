<?php

namespace App\Services\Travel;

use Exception;
use App\Models\Travel;
use App\Models\TravelAttachment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TravelService
{

    public function search($request)
    {
        try {
            $perPage = $request->input('take', 10);
            $date_from = $request->date_from ?? null;
            $date_to = $request->date_to ?? null;


            $travels = Travel::orderBy('id', 'desc')
                ->with('user');

            if($request->filled('search_term')){
                $search_term = $request->search_term;
                $travels->where(function($query) use($search_term){
                    $query->where('description', 'LIKE', "%$search_term%")
                        ->orWhere('type', 'LIKE', "%$search_term%")
                        ->orWhere('transporte', 'LIKE', "%$search_term%");
                });
            }

            if($request->filled('user_id')){
                $travels->where('user_id', $request->user_id);
            }

            if($date_from && $date_to){
                if($date_from == $date_to){
                    $travels->whereDate('created_at', $date_from);
                }else{
                    $travels->whereBetween('created_at', [$date_from, $date_to]);
                }
            }else if($date_from){
                $travels->whereDate('created_at', '>' , $date_from);
            }else if($date_to){
                $travels->whereDate('created_at', '<' , $date_from);
            }

            $travels = $travels->paginate($perPage);

            return $travels;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function getById($id)
    {
        try {

            $travel = Travel::where('id', $id)
                ->with('user')
                ->first();
            
            if(!isset($travel)) throw new Exception('Viagem não encontrada');

            return $travel;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function cards()
    {
        try {
            $pendingTravels = Travel::where('purchase_status', 'Pending')
                ->whereMonth('created_at', Carbon::now())
                ->count();
            $resolvedTravels = Travel::where('purchase_status', 'Resolved')
                ->whereMonth('created_at', Carbon::now())
                ->count();
            $totalValueTravels = Travel::where('purchase_status', 'Resolved')
                ->whereMonth('created_at', Carbon::now())
                ->sum('total_value');
            
            return [
               'status' => true,
                'data' => [
                    'pendingTravels' => $pendingTravels,
                    'resolvedTravels' => $resolvedTravels,
                    'totalValueTravels' => $totalValueTravels,                    
                ],
            ];
            
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function create($request)
    {
        try {
            // 'Pending', 'Resolved', 'RequestFinance', 'RequestManager' Tarcio
            $rules = [
                'description' => ['required', 'string', 'max:255'],
                'type' => ['required', 'string', 'max:255'],
                'transport' => ['required', 'string', 'max:255'],                
                'total_value' => ['required', 'numeric'],                                
                'observations' => ['nullable', 'string'],
                'purchase_date' => ['required', 'date'],
                'attachments' => ['nullable', 'array']
            ];

            $requestData = $request->all();

            $requestData['user_id'] = Auth::user()->id;

            $validator = Validator::make($requestData, $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            if(Carbon::parser($request->purchase_date)->format('Y-m-d') == Carbon::now()->format('Y-m-d')){
                $requestData['purchase_status'] = 'RequestManager';
            }

            DB::beginTransaction();

            $travel = Travel::create($requestData);

            if(isset($requestData['attachments'])){
                foreach($requestData['attachments'] as $attachment){
                    $name = $attachment->getClientOriginalName();
                    $path = $attachment->store('attachments');

                    TravelAttachment::create([            
                        'filename' => $name,
                        'path' => $path,
                        'travel_id' => $travel->id,
                    ]);
                }
            }
            
            DB::commit();
            
            return ['status' => true, 'data' => $travel];
        } catch (Exception $error) {
            DB::rollBack();
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function update($request, $id)
    {
        try {
            // 'Pending', 'Resolved', 'RequestFinance', 'RequestManager' Tarcio
            $travelToUpdate = Travel::find($id);

            if(isset($travelToUpdate)) throw new Exception('Viagem não encontrada');

            $rules = [
                'description' => ['required', 'string', 'max:255'],
                'type' => ['required', 'string', 'max:255'],
                'transport' => ['required', 'string', 'max:255'],
                'total_value' => ['required', 'numeric'],
                'observations' => ['nullable', 'string'],
                'purchase_date' => ['required', 'date']
            ];

            $requestData = $request->all();

            $requestData['user_id'] = Auth::user()->id;

            $validator = Validator::make($requestData, $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            if(Carbon::parser($request->purchase_date)->format('Y-m-d') == Carbon::now()->format('Y-m-d')){
                $requestData['purchase_status'] = 'RequestManager';
            }

            DB::beginTransaction();

            $travelToUpdate = $travelToUpdate->update($requestData);

            if(isset($requestData['attachments'])){
                foreach($requestData['attachments'] as $attachment){
                    $name = $attachment->getClientOriginalName();
                    $path = $attachment->store('attachments');
                    
                    TravelAttachment::create([            
                        'filename' => $name,
                        'path' => $path,
                        'travel_id' => $travelToUpdate->id,
                    ]);
                }
            }
            
            DB::commit();

            return ['status' => true, 'data' => $travelToUpdate];
        } catch (Exception $error) {
            DB::rollBack();
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function delete($id){
        try{
            $travel = Travel::find($id);

            if(!$travel) throw new Exception('Viagem não encontrada');

            $travelName = $travel->name;
            $travel->delete();

            return ['status' => true, 'data' => $travelName];
        }catch(Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
}
