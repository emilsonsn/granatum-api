<?php

namespace App\Services\Partner;

use Exception;
use App\Models\Partner;
use Illuminate\Support\Facades\Validator;

class PartnerService
{

    public function search($request)
    {
        try {
            $perPage = $request->input('take', 10);

            $partners = Partner::orderBy('id', 'desc');

            if($request->filled('search_term')){
                $search_term = $request->search_term;
                $partners->where(function($query) use($search_term){
                    $query->where('name', 'LIKE', "%$search_term%")
                        ->orWhere('email', 'LIKE', "%$search_term%")
                        ->orWhere('phone', 'LIKE', "%$search_term%")
                        ->orWhere('cnpj_cpf', 'LIKE', "%$search_term%");
                });
            }

            if($request->filled('is_active')){
                $partners->where('is_active', $request->is_active);
            }

            $partners = $partners->paginate($perPage);

            return $partners;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function getById($id)
    {
        try {

            $partner = Partner::where('id', $id)->first();
            
            if(!isset($partner)) throw new Exception('Parceiro não encontrado');

            return $partner;
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
                'phone' => ['required', 'string', 'max:20'],
                'email' => ['required', 'string', 'max:256'],
                'cnpj_cpf' => ['required', 'string', 'max:18'],
                'activity' => ['required', 'string', 'max:256'],
                'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
                'is_active' => ['boolean'],
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $data = $validator->validated();
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('partners', 'public');
            }

            $partner = Partner::create($data);            

            return ['status' => true, 'data' => $partner];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function update($request, $id)
    {
        try {
            $request['is_active'] = $request['is_active'] == 'true' ? true : false;

            $partnerToUpdate = Partner::find($id);

            if(!$partnerToUpdate) throw new Exception('Parceiro não encontrado');

            $rules = [
                'name' => ['required', 'string', 'max:256'],
                'phone' => ['required', 'string', 'max:20'],
                'email' => ['required', 'string', 'max:256'],
                'cnpj_cpf' => ['required', 'string', 'max:18'],
                'activity' => ['required', 'string', 'max:256'],
                'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
                'is_active' => ['boolean'],
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $data = $validator->validated();
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('partners', 'public');
            }

            $partnerToUpdate->update($data);

            return ['status' => true, 'data' => $partnerToUpdate];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function delete($id){
        try{
            $partner = Partner::find($id);

            if(!$partner) throw new Exception('Parceiro não encontrado');

            $partnerName = $partner->name;
            $partner->delete();

            return ['status' => true, 'data' => $partnerName];
        }catch(Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }
}