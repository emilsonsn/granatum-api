<?php

namespace App\Services\Order;

use App\Enums\CompanyPositionEnum;
use App\Enums\PurchaseStatusEnum;
use App\Models\Item;
use Exception;
use App\Models\Order;
use App\Models\OrderFile;
use App\Models\Release;
use App\Trait\GranatumTrait;
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

            $rules = [
                'order_type' => 'required|string|max:255',
                'date' => 'required|date',
                'construction_id' => 'required|integer',
                'user_id' => 'required|integer',
                'supplier_id' => 'required|integer',
                'quantity_items' => 'required|integer',
                'description' => 'required|string',
                'total_value' => 'required|numeric',
                'payment_method' => 'required|string|max:255',
                'purchase_status' => 'nullable|string|max:255',
                'bank_id' => 'nullable|integer',
                'category_id' => 'nullable|integer',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $data = $validator->validated();

            if((float)$data['total_value'] <= 300){
                $data['purchase_status'] = PurchaseStatusEnum::RequestManager->value;                
            }

            if($data['payment_method'] == 'Cash'){
                $data['purchase_status'] = PurchaseStatusEnum::RequestFinance->value;
            }

            if(!isset($data['purchase_status']) || $data['purchase_status'] == 'null'){
                $data['purchase_status'] = PurchaseStatusEnum::Pending->value;
            }

            $order = Order::create($data);

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
    
            $result = array_reduce($result, function($carry, $category) {
                if (isset($category['categorias_filhas']) && is_array($category['categorias_filhas'])) {
                    $carry = array_merge($carry, $category['categorias_filhas']);
                } else {
                    $carry[] = $category;
                }
                return $carry;
            }, []);
    
            $result = array_reduce($result, function($carry, $category) {
                $exists = false;
                foreach ($carry as $existingCategory) {
                    if ($existingCategory['descricao'] === $category['descricao']) {
                        $exists = true;
                        break;
                    }
                }
                if (!$exists) {
                    $carry[] = $category;
                }
                return $carry;
            }, []);
    
            return ['status' => true, 'data' => $result];
        }catch(Exception $error){
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function update($request, $user_id)
    {
        try {
            $request['bank_id'] = $request['bank_id'] === 'null' ? null : $request['bank_id'];
            $request['category_id'] = $request['category_id'] === 'null' ? null : $request['category_id'];

            $rules = [
                'order_type' => 'required|string|max:255',
                'date' => 'required|date',
                'construction_id' => 'required|integer',
                'user_id' => 'required|integer',
                'supplier_id' => 'required|integer',
                'quantity_items' => 'required|integer',
                'description' => 'required|string',
                'total_value' => 'required|numeric',
                'payment_method' => 'required|string|max:255',
                'purchase_status' => 'required|string|max:255',
                'bank_id' => 'nullable|integer',
                'category_id' => 'nullable|integer',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $orderToUpdate = Order::find($user_id);

            if(!isset($orderToUpdate)) throw new Exception('Pedido não encontrado');
            
            $data = $validator->validated();
            
            if(
                $orderToUpdate->purchase_status != PurchaseStatusEnum::Resolved->value
                and $data['purchase_status'] == PurchaseStatusEnum::Resolved->value
            ){
                $data['purchase_date'] = Carbon::now()->format('Y-m-d');
            }            
            
            $orderToUpdate->update($data);

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
    
            $response = $this->createRelease($categoryId, $accountBankId, $description, $value, $purchaseDate);
    
            if(isset($response['errors']) && !isset($response['id'])) throw new Exception ("Erro ao criar lançamento no granatum");

            Release::create([
                'release_id' => $response['id'],
                'category_id' => $categoryId,
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
