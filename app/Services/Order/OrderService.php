<?php

namespace App\Services\Order;

use App\Enums\CompanyPositionEnum;
use App\Enums\OrderTypeEnum;
use App\Enums\PurchaseStatusEnum;
use App\Enums\SolicitationStatusEnum;
use App\Models\Item;
use Exception;
use App\Models\Order;
use App\Models\OrderFile;
use App\Models\Release;
use App\Models\Solicitation;
use App\Traits\GranatumTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
class OrderService
{

    use GranatumTrait;
    public function search($request)
    {
        try {
            $perPage = $request->input('take', 10);
            $search_term = $request->search_term;

            $order = Order::orderBy('id', 'desc')
                ->with(['construction', 'supplier', 'files', 'items', 'releases', 'solicitation', 'user']);

            if(isset($search_term)){
                $order->where('description', 'LIKE', "%{$search_term}%");
            }

            if(isset($request->status)){
                $status = explode(',', $request->status);
                $order->whereIn('purchase_status', $status);
            }

            if(isset($request->start_date) && isset($request->end_date)){                
                $order->whereBetween('date', [$request->start_date, $request->end_date]);
            }else if(isset($request->start_date)){
                $order->whereDate('date', '>' ,$request->start_date);
            }else if(isset($request->end_date)){
                $order->whereDate('date', '<' ,$request->end_date);
            }

            if(isset($request->is_home)){
                $companyPosition = Auth::user()->companyPosition;
                $order->where('purchase_status', '!=', PurchaseStatusEnum::Resolved->value);

                switch ($companyPosition->position){
                    case CompanyPositionEnum::Admin->value: break;
                    case CompanyPositionEnum::Financial->value:
                        $order->where('purchase_status', PurchaseStatusEnum::RequestFinance->value);
                        break;
                    case CompanyPositionEnum::Supplies->value:
                        $order->where('purchase_status', PurchaseStatusEnum::RequestManager->value);
                        break;
                    case CompanyPositionEnum::Requester->value:
                        $order->where('purchase_status', PurchaseStatusEnum::Pending->value);
                        break;
                    default: break;
                }
            }

            $order = $order->paginate($perPage);

            return $order;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function getById($id)
    {
        try {

            $order = Order::where('id', $id)
                ->with(['construction', 'supplier', 'files', 'items', 'releases', 'solicitation', 'user'])
                ->first();
            
                if(!isset($order)) throw new Exception('Pedido não encontrado');

            return $order;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function cards()
    {
        try {
            $orderPending = order::where('status', PurchaseStatusEnum::Pending->value)
                ->count();
            
            $orderResolved = order::where('status', PurchaseStatusEnum::Resolved->value)
                ->count();
                
            $orderRequestFinance = order::where('status', PurchaseStatusEnum::RequestFinance->value)
                ->count();
            
            return [
                'status' => true,
                'data' => [

                    'orderPending' => $orderPending,
                    'orderResolved' => $orderResolved,
                    'orderRequestFinance' => $orderRequestFinance,
                ]
            ];

        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }    

    public function create($request)
    {
        try {
            $request['bank_id'] = $request['bank_id'] === 'null' ? null : $request['bank_id'];
            $request['category_id'] = $request['category_id'] === 'null' ? null : $request['category_id'];
            $request['purchase_status'] = $request['purchase_status'] === 'null' ? null : $request['purchase_status'];
            $request['user_id'] = $request['user_id'] === 'null' ? Auth::user()->id : $request['user_id'];
            $request['purchase_date'] = $request['purchase_date'] === 'null' ? null : $request['purchase_date'];

            $rules = [
                'order_type' => 'required|string|max:255',
                'date' => 'required|date',
                'construction_id' => 'required|integer',
                'user_id' => 'required|integer',
                'supplier_id' => 'nullable|integer',
                'quantity_items' => 'required|integer',
                'description' => 'required|string',
                'total_value' => 'required|numeric',
                'payment_method' => 'required|string|max:255',
                'purchase_status' => 'nullable|string|max:255',
                'purchase_date' => 'nullable|date',
                'bank_id' => 'nullable|integer',
                'category_id' => 'nullable|integer',
                'tag_id' => 'nullable|integer',
                'external_suplier_id'=> 'nullable|integer',
                'cost_center_id' => 'nullable|integer',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $data = $validator->validated();

            if(
                $data['purchase_status'] == PurchaseStatusEnum::Resolved->value
            ){
                $data['purchase_date'] = $data['purchase_date'] ?? Carbon::now()->format('Y-m-d');
            }

            if((float)$data['total_value'] <= 300){
                $data['purchase_status'] = PurchaseStatusEnum::RequestManager->value;                
            }

            if($data['payment_method'] == 'Cash' || $data['order_type'] == OrderTypeEnum::Reimbursement->value){
                $data['purchase_status'] = PurchaseStatusEnum::RequestFinance->value;
            }

            if(!isset($data['purchase_status']) || $data['purchase_status'] == 'null'){
                $data['purchase_status'] = PurchaseStatusEnum::Pending->value;
            }

            $order = Order::create($data);

            if($data['purchase_status'] == PurchaseStatusEnum::RequestFinance->value){
                $solicitation_type = $data['order_type'] == OrderTypeEnum::Reimbursement->value ? 'Reimbursement' : 'Payment';
                $solicitation = Solicitation::create([
                    'order_id' => $order->id,
                    'solicitation_type' => $solicitation_type,
                    'total_value' => $order->total_value,
                    'supplier_id' => $order->supplier_id,
                    'user_id' => $order->user_id,
                    'construction_id' => $order->construction_id,
                    'status' =>  SolicitationStatusEnum::Pending->value,
                    'payment_date' => null,
                ]);

                $order['solicitation'] = $solicitation;
            }

            if(isset($request->items)){
                $items = $request->items;

                foreach($items as $item){
                    $item = json_decode($item);
                    Item::updateOrCreate(
                        [
                            'id' => $item->id ?? null
                        ],
                        [
                            'order_id' => $order->id ?? null,
                            'name' => $item->name,
                            'quantity' => $item->quantity,
                            'unit_value' => $item->unit_value,
                        ]
                    );
                }
            }

            if(isset($request->order_files)){
                $orderFiles = $request->order_files;

                foreach($orderFiles as $file){
                    $path = $file->store('order_files', 'public');
                    $fullPath = asset('storage/' . $path);

                    OrderFile::create(
                        [
                            'name' => $file->getClientOriginalName(),
                            'path' => $fullPath,
                            'order_id' => $order->id,
                        ]
                    );
                }
            }

            return ['status' => true, 'data' => $order];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function getBank(){
        try{
            $result = $this->getAccountBank();
            return ['status' => true, 'data' => $result];
        }catch(Exception $error){
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function getCategories(){
        try{
            $result = $this->categories();
    
            // Primeira redução para combinar categorias filhas com suas respectivas categorias pais
            $result = array_reduce($result, function($carry, $category) {
                if (isset($category['categorias_filhas']) && is_array($category['categorias_filhas'])) {
                    foreach ($category['categorias_filhas'] as $filha) {
                        // Atualiza a descrição da categoria filha incluindo a categoria pai
                        $filha['descricao'] = $category['descricao'] . ' / ' . $filha['descricao'];
                        $carry[] = $filha;
                    }
                } else {
                    $carry[] = $category;
                }
                return $carry;
            }, []);
    
            return ['status' => true, 'data' => $result];
        } catch(Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }    

    public function getTags(){
        try{
            $result = $this->tags();
    
            return ['status' => true, 'data' => $result];
        } catch(Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }  
    
    public function getSuplier(){
        try{
            $result = $this->suplier();
    
            return ['status' => true, 'data' => $result];
        } catch(Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }      

    public function getCostCenter(){
        try{
            $result = $this->costCenters();
    
            $result = array_reduce($result, function($carry, $costCenter) {
                if (isset($costCenter['centros_custo_lucro_filhos']) && is_array($costCenter['centros_custo_lucro_filhos'])) {
                    foreach ($costCenter['centros_custo_lucro_filhos'] as $filha) {
                        // Atualiza a descrição da categoria filha incluindo a categoria pai
                        $filha['descricao'] = $costCenter['descricao'] . ' / ' . $filha['descricao'];
                        $carry[] = $filha;
                    }
                } else {
                    $carry[] = $costCenter;
                }
                return $carry;
            }, []);
    
            return ['status' => true, 'data' => $result];
        } catch(Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }  

    public function update($request, $user_id)
    {
        try {
            $request['bank_id'] = $request['bank_id'] === 'null' ? null : $request['bank_id'];
            $request['category_id'] = $request['category_id'] === 'null' ? null : $request['category_id'];
            $request['purchase_status'] = $request['purchase_status'] === 'null' ? null : $request['purchase_status'];
            $request['user_id'] = $request['user_id'] === 'null' ? Auth::user()->id : $request['user_id'];
            $request['purchase_date'] = $request['purchase_date'] === 'null' ? null : Carbon::parse($request['purchase_date'])->format('Y-m-d');

            $rules = [
                'order_type' => 'required|string|max:255',
                'date' => 'required|date',
                'construction_id' => 'required|integer',
                'user_id' => 'required|integer',
                'supplier_id' => 'nullable|integer',
                'quantity_items' => 'required|integer',
                'description' => 'required|string',
                'total_value' => 'required|numeric',
                'payment_method' => 'required|string|max:255',
                'purchase_status' => 'required|string|max:255',
                'purchase_date' => 'nullable|date',
                'bank_id' => 'nullable|integer',
                'category_id' => 'nullable|integer',
                'tag_id' => 'nullable|integer',
                'external_suplier_id'=> 'nullable|integer',
                'cost_center_id' => 'nullable|integer',
            ];

            $data = $request->all();

            $validator = Validator::make($data, $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $orderToUpdate = Order::find($user_id);

            if(!isset($orderToUpdate)) throw new Exception('Pedido não encontrado');
            
            $data = $validator->validated();
            
            if(
                $orderToUpdate->purchase_status != PurchaseStatusEnum::Resolved->value
                and $data['purchase_status'] == PurchaseStatusEnum::Resolved->value
            ){
                $data['purchase_date'] = $data['purchase_date'] ?? Carbon::now()->format('Y-m-d');
            }

            if((float)$data['total_value'] <= 300){
                $data['purchase_status'] = PurchaseStatusEnum::RequestManager->value;                
            }

            if($data['payment_method'] == 'Cash' || $data['order_type'] == OrderTypeEnum::Reimbursement->value){
                $data['purchase_status'] = PurchaseStatusEnum::RequestFinance->value;
            }

            if(!isset($data['purchase_status']) || $data['purchase_status'] == 'null'){
                $data['purchase_status'] = PurchaseStatusEnum::Pending->value;
            }

            $orderToUpdate->update($data);

            // if($data['purchase_status'] == PurchaseStatusEnum::RequestFinance->value){
            //     $solicitation_type = $data['order_type'] == OrderTypeEnum::Reimbursement->value ? 'Reimbursement' : 'Payment';
            //     $solicitation = Solicitation::create([
            //         'order_id' => $orderToUpdate->id,
            //         'solicitation_type' => $solicitation_type,
            //         'total_value' => $orderToUpdate->total_value,
            //         'supplier_id' => $orderToUpdate->supplier_id,
            //         'user_id' => $orderToUpdate->user_id,
            //         'construction_id' => $orderToUpdate->construction_id,
            //         'status' =>  SolicitationStatusEnum::Pending->value,
            //         'payment_date' => null,
            //     ]);

            //     $orderToUpdate['solicitation'] = $solicitation;
            // }

            if(isset($request['items'])){
                foreach($request['items'] as $item){
                    $item = json_decode($item);
                    Item::updateOrCreate(
                        [
                            'id' => $item->id ?? null
                        ],
                        [
                            'order_id' => $orderToUpdate->id,
                            'name' => $item->name,
                            'quantity' => $item->quantity,
                            'unit_value' => $item->unit_value,
                        ]
                    );
                }
            }

            if(isset($request->order_files)){
                foreach($request->order_files as $file){
                    $path = $file->store('order_files', 'public');
                    $fullPath = asset('storage/' . $path);

                    OrderFile::create(
                        [
                            'name' => $file->getClientOriginalName(),
                            'path' => $fullPath,
                            'order_id' => $orderToUpdate->id,
                        ]
                    );
                }
            }

            return ['status' => true, 'data' => $orderToUpdate];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function delete($id){
        try{
            $order = Order::find($id);

            if(!$order) throw new Exception('Pedido não encontrado');

            $orderDescription = $order->description;
            $order->delete();

            return ['status' => true, 'data' => $orderDescription];
        }catch(Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function upRelease($orderId) {
        try{
            
            $order = Order::find($orderId);

            if(!isset($order)) throw new Exception('Pedido não encontrado');

            if(count($order->releases)) throw new Exception('Lançamento já foi efetuado');
            
            $description = $order->description;
            $value = $order->total_value;
            $purchaseDate = $order->purchase_date;
            $accountBankId = $order->bank_id;
            $categoryId =  $order->category_id;
            $tagId =  $order->tag_id;
            $suplierId =  $order->external_suplier_id;
            $costCenterId = $order->cost_center_id;

            $response = $this->createRelease($categoryId, $accountBankId, $description, $value, $purchaseDate, $tagId, $suplierId);
    
            if(isset($response['errors']) && !isset($response['id'])) throw new Exception ("Erro ao criar lançamento no granatum");

            Release::create([
                'release_id' => $response['id'],
                'category_id' => $categoryId,
                'centro_custo_lucro_id' => $costCenterId,
                'account_bank_id' => $accountBankId,
                'description' => $description,
                'value' => $value,
                'user_id' => auth()->user()->id,
                'order_id' => $orderId,
                'api_response' => json_encode($response) ?? null
            ]);

            $order->update(['has_granatum' => true]);

            $attachResponse = $this->sendAttachs($order->id, $response['id']);

            if(isset($attachResponse['errors'])) throw new Exception ("Não foi possível enviar os anexos");
    
            return ['status' => true, 'message' => 'Lançamento criado com sucesso'];

        }catch(Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function delete_order_file($id){
        try{
            $orderFile = OrderFile::find($id);

            if(!isset($orderFile)) throw new Exception ("Arquivo não encontrado");

            Storage::delete($orderFile->path);

            $orderFileName= $orderFile->name;
            $orderFile->delete();

            return ['status' => true, 'data' => $orderFileName];
        }catch(Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function delete_order_item($id){
        try{
            $item = Item::find($id);

            if(!isset($item)) throw new Exception ("Item não encontrado");

            $orderItemName= $item->name;
            $item->delete();

            return ['status' => true, 'data' => $orderItemName];
        }catch(Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
}