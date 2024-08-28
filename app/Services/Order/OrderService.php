<?php

namespace App\Services\Order;

use App\Enums\PurchaseStatus;
use App\Enums\PurchaseStatusEnum;
use App\Models\Item;
use Exception;
use App\Models\Order;
use App\Models\OrderFile;
use App\Models\Solicitation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class OrderService
{

    public function search($request)
    {
        try {
            $perPage = $request->input('take', 10);
            $search_term = $request->search_term;

            $order = Order::orderBy('id', 'desc')->with('files');

            if(isset($search_term)){
                $order->where('description', 'LIKE', "%{$search_term}%");
            }

            $order = $order->paginate($perPage);

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
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return ['status' => false, 'error' => $validator->errors()];
            }

            $order = Order::create($validator->validated());

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
                    $path = $file->store('order_files');

                    OrderFile::create(
                        [
                            'name' => $file->getClientOriginalName(),
                            'path' => $path,
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


    public function update($request, $user_id)
    {
        try {
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
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());


            $orderToUpdate = Order::find($user_id);

            if(!isset($orderToUpdate)) throw new Exception('Pedido não encontrado');
            
            $data = $validator->validated();
            
            if(
                $orderToUpdate->purchase_status != PurchaseStatusEnum::Resolved->value
                and $data['purchase_status'] != PurchaseStatusEnum::Resolved->value
            ){
                $data['purchase_date'] = Carbon::now()->format('Y-m-d');
            }            
            
            $orderToUpdate->update();

            if(isset($request['items'])){
                foreach($request['items'] as $item){
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
                    $path = $file->store('order_files');

                    OrderFile::create(
                        [
                            'name' => $file->getClientOriginalName(),
                            'path' => $path,
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

}
