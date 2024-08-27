<?php

namespace App\Services\User;

use App\Models\PasswordRecovery;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordRecoveryMail;
use App\Mail\WelcomeMail;
use App\Models\CompanyPosition;
use App\Models\Sector;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserService
{

    public function all()
    {
        try {
            $users = User::get();

            return ['status' => true, 'data' => $users];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => $error->getCode()];
        }
    }

    public function search($request)
    {
        try {
            $perPage = $request->input('take', 10);
            $search_term = $request->search_term;

            $users = User::where('is_admin', 0)->with('companyPosition', 'sector');

            if(isset($search_term)){
                $users->where('name', 'LIKE', "%{$search_term}%")
                    ->orWhere('email', 'LIKE', "%{$search_term}%");
            }

            $users = $users->paginate($perPage);

            return $users;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => $error->getCode()];
        }
    }

    public function getUser()
    {
        try {
            $user = auth()->user();

            return ['status' => true, 'data' => $user];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => $error->getCode()];
        }
    }

    public function create($request)
    {
        try {
            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'nullable|string|min:8',
                'phone' => 'nullable|string',
                'whatsapp' => 'nullable|string',
                'cpf_cnpj' => 'nullable|string',
                'birth_date' => 'nullable|date',
                'company_position_id' => 'nullable|integer',
                'sector_id' => 'nullable|integer',
                'is_active' => 'nullable|boolean|default:true',
            ];

            $password = str_shuffle(Str::upper(Str::random(1)) . rand(0, 9) . Str::random(1, '?!@#$%^&*') . Str::random(5));

            $requestData = $request->all();
            $requestData['password'] = Hash::make($password);

            $validator = Validator::make($requestData, $rules);

            if ($validator->fails()) {
                return ['status' => false, 'error' => $validator->errors()];
            }

            $user = User::create($validator->validated());

            Mail::to($user->email)->send(new WelcomeMail($user->name, $user->email, $password));

            return ['status' => true, 'data' => $user];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => $error->getCode()];
        }
    }


    public function update($request, $user_id)
    {
        try {
            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8',
                'phone' => 'nullable|string',
                'whatsapp' => 'nullable|string',
                'cpf_cnpj' => 'nullable|string',
                'birth_date' => 'nullable|date',
                'company_position_id' => 'nullable|integer',
                'sector_id' => 'nullable|integer',
                'is_active' => 'nullable|boolean|default:true',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $userToUpdate = User::find($user_id);

            if(!isset($userToUpdate)) throw new Exception('Usuário não encontrado');

            $userToUpdate->update($validator->validated());

            return ['status' => true, 'data' => $userToUpdate];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => $error->getCode()];
        }
    }

    public function userBlock($user_id)
    {
        try {
            $user = User::find($user_id);

            if (!$user) throw new Exception('Usuário não encontrado');

            $user->is_active = !$user->is_active;
            $user->save();

            return ['status' => true, 'data' => $user];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => $error->getCode()];
        }
    }

    public function requestRecoverPassword($request)
    {
        try {
            $email = $request->email;
            $user = User::where('email', $email)->first();

            if (!isset($user)) throw new Exception('Usuário não encontrado.');

            $code = bin2hex(random_bytes(10));

            $recovery = PasswordRecovery::create([
                'code' => $code,
                'user_id' => $user->id
            ]);

            if (!$recovery) {
                throw new Exception('Erro ao tentar recuperar senha');
            }

            Mail::to($email)->send(new PasswordRecoveryMail($code));
            return ['status' => true, 'data' => $user];

        } catch (Exception $error) {
            Log::error('Erro na recuperação de senha: ' . $error->getMessage());
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => $error->getCode()];
        }
    }


    public function updatePassword($request){
        try{
            $code = $request->code;
            $password = $request->password;

            $recovery = PasswordRecovery::orderBy('id', 'desc')->where('code', $code)->first();

            if(!$recovery) throw new Exception('Código enviado não é válido.');

            $user = User::find($recovery->user_id);
            $user->password = Hash::make($password);
            $user->save();
            $recovery->delete();

            return ['status' => true, 'data' => $user];
        }catch(Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => $error->getCode()];
        }
    }

    public function positionSearch($request)
    {
        try {
            $perPage = $request->input('take', 10);

            $companyPositions = CompanyPosition::orderBy('id', 'desc');

            $companyPositions = $companyPositions->paginate($perPage);

            return $companyPositions;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => $error->getCode()];
        }
    }

    public function sectorSearch($request)
    {
        try {
            $perPage = $request->input('take', 10);

            $sectors = Sector::orderBy('id', 'desc');

            $sectors = $sectors->paginate($perPage);

            return $sectors;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => $error->getCode()];
        }
    }

    public function sectorCreate($request)
    {
        try {
            $rules = [
                'sector' => 'required|string|max:255',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return ['status' => false, 'error' => $validator->errors()];
            }

            $sector = Sector::create($validator->validated());

            return ['status' => true, 'data' => $sector];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => $error->getCode()];
        }
    }

    public function sectorDelete($id){
        try{
            $sector = Sector::find($id);

            if(!$sector) throw new Exception('Setor não encontrado');

            $name = $sector->sector;
            $sector->delete();

            return ['status' => true, 'data' => $sector];
        }catch(Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => $error->getCode()];
        }
    }
}
