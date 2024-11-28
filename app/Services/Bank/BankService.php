<?php

namespace App\Services\Bank;

use Exception;
use App\Models\Bank;
use Illuminate\Support\Facades\Validator;

class BankService
{

    public function search($request)
    {
        try {
            $perPage = $request->input('take', 10);

            $banks = Bank::orderBy('id', 'desc');

            if($request->filled('search_term')){
                $search_term = $request->search_term;
                $banks->where(function($query) use($search_term){
                    $query->where('name', 'LIKE', "%$search_term%");                        
                });
            }

            if($request->filled('is_active')){
                $is_active = $request->input('is_active');
                $banks->where('is_active', $is_active);
            }

            $banks = $banks->paginate($perPage);

            return $banks;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function getById($id)
    {
        try {

            $bank = Bank::find($id);
            
            if(!isset($bank)) throw new Exception('Banco não encontrado');

            return $bank;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function create($request)
    {
        try {
            $request['is_active'] = $request['is_active'] == 'true' ? true : false;

            $rules = [
                'name' => ['required', 'string', 'max:256'],
                'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
                'is_active' => ['nullable', 'boolean']
            ];    
    
            $validator = Validator::make($request->all(), $rules);
    
            if ($validator->fails()) throw new Exception($validator->errors());
    
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('banks', 'public');
            }

            $data = $validator->validated();
    
            $bank = Bank::create([
                'name' => $data['name'],
                'image' => $imagePath
            ]);
    
            return ['status' => true, 'data' => $bank];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function update($request, $id)
    {
        try {
            $request['is_active'] = $request['is_active'] == 'true' ? true : false;

            $bankToUpdate = Bank::find($id);

            if(isset($bankToUpdate)) throw new Exception('Banco não encontrado');

            $rules = [
                'name' => ['required', 'string', 'max:256'],
                'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
                'is_active' => ['nullable', 'boolean']
            ];  

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('banks', 'public');
            }

            $data = $validator->validated();

            $bankToUpdate = $bankToUpdate->update([
                'name' => $data['name'],
                'image' => $imagePath,
            ]);

            return ['status' => true, 'data' => $bankToUpdate];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
    public function delete($id){
        try{
            $bank = Bank::find($id);

            if(!$bank) throw new Exception('Banco não encontrado');

            $bankName = $bank->name;
            $bank->delete();

            return ['status' => true, 'data' => $bankName];
        }catch(Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
}
