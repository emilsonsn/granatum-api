<?php

namespace App\Services\Vacancy;

use Exception;
use App\Models\Vacancy;
use App\Traits\GranatumTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class VacancyService
{
    use GranatumTrait;

    public function search($request)
    {
        try {
            $perPage = $request->input('take', 10);
            $date_from = $request->date_from ?? null;
            $date_to = $request->date_to ?? null;
            $profession_id = $request->profession_id ?? null;

            $vacancies = Vacancy::orderBy('id', 'desc')
                ->with('profession');

            if($request->filled('search_term')){
                $search_term = $request->search_term;
                $vacancies->where(function($query) use($search_term){
                    $query->where('description', 'LIKE', "%$search_term%")
                        ->orWhere('title', 'LIKE', "%$search_term%");
                });
            }

            if($date_from && $date_to){
                if($date_from == $date_to){
                    $vacancies->whereDate('created_at', $date_from);
                }else{
                    $vacancies->whereBetween('created_at', [$date_from, $date_to]);
                }
            }else if($date_from){
                $vacancies->whereDate('created_at', '>' , $date_from);
            }else if($date_to){
                $vacancies->whereDate('created_at', '<' , $date_from);
            }

            if($profession_id){
                $vacancies->where('profession_id', $profession_id);
            }

            $vacancies = $vacancies->paginate($perPage);

            return $vacancies;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function getById($id)
    {
        try {

            $vacancy = Vacancy::where('id', $id)
                ->first();
            
            if(!isset($vacancy)) throw new Exception('Vagas não encontrada');

            return $vacancy;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function cards()
    {
        try {
            $totalVacancysMonth = Vacancy::whereMonth('created_at', Carbon::now())
                ->count();

            $activeVacancys = Vacancy::with('selectionProcesses', function($query){
                $query->where('is_active', true);
            })->count();
                
            
            $inactiveVacancys = Vacancy::whereDoesntHave('selectionProcesses', function($query) {
                $query->where('is_active', true);
            })->count();
            

            return [
               'status' => true,
                'data' => [
                    'totalVacancysMonth' => $totalVacancysMonth,
                    'activeVacancys' => $activeVacancys,
                    'inactiveVacancys' => $inactiveVacancys,                                      
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
                'title' => ['required', 'string', 'max:255'],
                'description' => ['required', 'string'], //@Tarcio -> Longtext (descrição da vaga, texto gigante)
                'profession_id' => ['required', 'integer'],
            ];

            $requestData = $request->all();

            $validator = Validator::make($requestData, $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $vacancy = Vacancy::create($requestData);
            
            return ['status' => true, 'data' => $vacancy];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function update($request, $id)
    {
        try {
            $vacancyToUpdate = Vacancy::find($id);

            if(!isset($vacancyToUpdate)) throw new Exception('Vagas não encontrada');

            $rules = [
                'title' => ['required', 'string', 'max:255'],
                'description' => ['required', 'string'], //@Tarcio -> Longtext (descrição da vaga, texto gigante)
                'profession_id' => ['required', 'integer'],
            ];

            $requestData = $request->all();

            $validator = Validator::make($requestData, $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $vacancyToUpdate->update($requestData);

            return ['status' => true, 'data' => $vacancyToUpdate];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

    public function delete($id)
    {
        try {
            $vacancy = Vacancy::find($id);

            if (!$vacancy) throw new Exception('Vagas não encontrada');

            $vacancyTitle = $vacancy->title;

            $vacancy->delete();

            return ['status' => true, 'data' => $vacancyTitle];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage(), 'statusCode' => 400];
        }
    }

}
