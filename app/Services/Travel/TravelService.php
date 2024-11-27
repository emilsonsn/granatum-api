<?php

namespace App\Services\Travel;

use App\Models\Release;
use Exception;
use App\Models\Travel;
use App\Models\TravelAttachment;
use App\Traits\GranatumTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TravelService
{
    use GranatumTrait;

    public function search($request)
    {
        try {
            $perPage = $request->input('take', 10);
            $date_from = $request->date_from ?? null;
            $date_to = $request->date_to ?? null;
            $purchase_status = $request->purchase_status ?? null;

            $travels = Travel::orderBy('id', 'desc')
                ->with('user', 'files');

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

            if($purchase_status){
                $travels->where('purchase_status', $purchase_status);
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
            $totalValueTravels = Travel::whereMonth('created_at', Carbon::now())
                ->count();
            // ----------- Dashboard ---------------
            $totalValueMonthTravelsSum = Travel::whereMonth('created_at', Carbon::now())
                ->sum('total_value');
            $pendingMonthTravelsSum = Travel::where('purchase_status', 'Pending')
                ->whereMonth('created_at', Carbon::now())
                ->sum('total_value');
            $resolvedMonthTravelsSum = Travel::where('purchase_status', 'Resolved')
                ->whereMonth('created_at', Carbon::now())
                ->sum('total_value');

            return [
               'status' => true,
                'data' => [
                    'totalValueTravels' => $totalValueTravels,
                    'pendingTravels' => $pendingTravels,
                    'resolvedTravels' => $resolvedTravels,
                    
                    'totalValueMonthTravelsSum' => $totalValueMonthTravelsSum,
                    'pendingMonthTravelsSum' => $pendingMonthTravelsSum,
                    'resolvedMonthTravelsSum' => $resolvedMonthTravelsSum,
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

            if(Carbon::parse($request->purchase_date)->format('Y-m-d') == Carbon::now()->format('Y-m-d')){
                $requestData['purchase_status'] = 'RequestFinance';
            }else{
                $requestData['purchase_status'] = 'RequestManager';
            }

            DB::beginTransaction();

            $travel = Travel::create($requestData);

            if(isset($requestData['attachments'])){
                foreach($requestData['attachments'] as $attachment){
                    $name = $attachment->getClientOriginalName();
                    $path = $attachment->store('attachments');

                    TravelAttachment::create([            
                        'name' => $name,
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
            $travelToUpdate = Travel::find($id);

            if(!isset($travelToUpdate)) throw new Exception('Viagem não encontrada');

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

            if(Carbon::parse($request->purchase_date)->format('Y-m-d') == Carbon::now()->format('Y-m-d')){
                $requestData['purchase_status'] = 'RequestManager';
            }

            DB::beginTransaction();

            $travelToUpdate->update($requestData);
            
            $attachments = [];
            if(isset($requestData['attachments'])){
                foreach($requestData['attachments'] as $attachment){
                    $name = $attachment->getClientOriginalName();
                    $path = $attachment->store('attachments');
                    
                    $attachments[] = TravelAttachment::create([
                        'name' => $name,
                        'path' => $path,
                        'travel_id' => $travelToUpdate->id,
                    ]);
                }

                $travelToUpdate['attachments'] = $attachments;
            }
            
            DB::commit();

            return ['status' => true, 'data' => $travelToUpdate];
        } catch (Exception $error) {
            DB::rollBack();
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function updateSolicitation($request, $id)
    {
        try {
            $travelToUpdate = Travel::find($id);

            if(!isset($travelToUpdate)) throw new Exception('Viagem não encontrada');

            $rules = [
                'solicitation_type' => ['required', 'string', 'in:Payment,Reimbursement'],
            ];

            $requestData = $request->all();
            $requestData['purchase_status'] =  'RequestFinance';

            $validator = Validator::make($requestData, $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $travelToUpdate->update($requestData);

            return ['status' => true, 'data' => $travelToUpdate];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function deleteFile($id){
        try{
            $travelAttachment = TravelAttachment::find($id);

            if(!isset($travelAttachment)) throw new Exception ("Arquivo não encontrado");

            Storage::delete($travelAttachment->path);

            $travelAttachmentName= $travelAttachment->name;
            $travelAttachment->delete();

            return ['status' => true, 'data' => $travelAttachmentName];
        }catch(Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function upRelease($id) {
        try{
            $travel = Travel::find($id);

            if(!isset($travel)) throw new Exception('Viagem não encontrada');

            if(count($travel->releases)) throw new Exception('Lançamento já foi efetuado');
            
            $description = $travel->description;
            $value = $travel->total_value;
            $purchaseDate = $travel->purchase_date;
            $accountBankId = $travel->bank_id;
            $categoryId =  $travel->category_id;
    
            $response = $this->createRelease($categoryId, $accountBankId, $description, $value, $purchaseDate);
    
            if(isset($response['errors']) && !isset($response['id'])) throw new Exception ("Erro ao criar lançamento no granatum");

            Release::create([
                'release_id' => $response['id'],
                'category_id' => $categoryId,
                'account_bank_id' => $accountBankId,
                'description' => $description,
                'value' => $value,
                'user_id' => auth()->user()->id,
                'order_id' => null,
                'travel_id' => $id,
                'api_response' => json_encode($response) ?? null
            ]);

            $travel->update([
                'has_granatum' => true,
                'purchase_status' => 'Resolved'
            ]);

            $attachResponse = $this->sendAttachs($travel->id, $response['id'], 'travel');

            if(isset($attachResponse['errors'])) throw new Exception ("Não foi possível enviar os anexos");
    
            return ['status' => true, 'message' => 'Lançamento criado com sucesso'];

        }catch(Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function delete($id)
    {
        try {
            $travel = Travel::find($id);

            if (!$travel) throw new Exception('Viagem não encontrada');

            $travelDescription = $travel->description;

            foreach ($travel->files as $file) {
                Storage::delete($file->path);
                $file->delete();
            }

            $travel->delete();

            return ['status' => true, 'data' => $travelDescription];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

}
