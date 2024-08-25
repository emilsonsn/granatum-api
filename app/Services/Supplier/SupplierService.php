<?php

namespace App\Services\Supplier;

use Exception;
use App\Models\Supplier;
use Illuminate\Support\Facades\Validator;

class SupplierService
{

    public function search($request)
    {
        try {
            $perPage = $request->input('take', 10);
            $search_term = $request->search_term;

            $suppliers = Supplier::orderBy('id', 'desc');

            if(isset($search_term)){
                $suppliers->where('fantasy_name', 'LIKE', "%{$search_term}%")
                    ->orWhere('cnpj', 'LIKE', "%{$search_term}%")
                    ->orWhere('email', 'LIKE', "%{$search_term}%")
                    ->orWhere('phone', 'LIKE', "%{$search_term}%")
                    ->orWhere('whatsapp', 'LIKE', "%{$search_term}%");
            }

            $suppliers = $suppliers->paginate($perPage);

            return ['status' => true, 'data' => $suppliers];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage()];
        }
    }

    public function create($request)
    {
        try {
            $rules = [
                'fantasy_name' => 'required|string|max:255',
                'cnpj' => 'required|string|max:255',
                'phone' => 'required|string|max:255',
                'whatsapp' => 'required|string|max:255',
                'email' => 'required|string|max:255',
                'type_supplier_id' => 'required|integer',
                'address' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'state' => 'required|string|max:255',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return ['status' => false, 'errors' => $validator->errors()];
            }

            $supplier = Supplier::create($validator->validated());

            return ['status' => true, 'data' => $supplier];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage()];
        }
    }


    public function update($request, $user_id)
    {
        try {
            $rules = [
                'fantasy_name' => 'required|string|max:255',
                'cnpj' => 'required|string|max:255',
                'phone' => 'required|string|max:255',
                'whatsapp' => 'required|string|max:255',
                'email' => 'required|string|max:255',
                'type_supplier_id' => 'required|integer',
                'address' => 'required|string|max:255',
                'city' => 'required|string|max:255',
                'state' => 'required|string|max:255',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $supplierToUpdate = Supplier::find($user_id);

            if(!isset($supplierToUpdate)) throw new Exception('Fornecedor não encontrado');

            $supplierToUpdate->update($validator->validated());

            return ['status' => true, 'data' => $supplierToUpdate];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage()];
        }
    }

    public function delete($id){
        try{
            $supplier = Supplier::find($id);

            if(!$supplier) throw new Exception('Fornecedor não encontrado');

            $supplierFantasy_name = $supplier->fantasy_name;
            $supplier->delete();

            return ['status' => true, 'data' => $supplierFantasy_name];
        }catch(Exception $error) {
            return ['status' => false, 'error' => $error->getMessage()];
        }
    }
}
