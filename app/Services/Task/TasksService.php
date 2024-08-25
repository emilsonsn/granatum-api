<?php

namespace App\Services\Task;

use App\Models\SubTask;
use Exception;

use App\Models\Supplier;
use App\Models\Task;
use App\Models\TaskFile;
use App\Models\TaskStatus;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TasksService
{

    public function search($request)
    {
        try {
            $perPage = $request->input('take', 10);
            $search_term = $request->search_term;
            $task_status_id = $request->task_status_id;

            $tasks = Task::orderBy('id', 'desc');

            if(isset($search_term)){
                $tasks->where('name', 'LIKE', "%{$search_term}%")
                    ->orWhere('description', 'LIKE', "%{$search_term}%");
            }

            if(isset($task_status_id)){
                $tasks->where('task_status_id', $task_status_id);
            }

            $tasks = $tasks->paginate($perPage);

            return $tasks;
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage()];
        }
    }

    public function create($request)
    {
        try {
            $rules = [
                'name' => 'required|string|max:255',
                'user_id' => 'required|integer',
                'concluded_at' => 'nullable|date',
                'description'  => 'required|string',
                'task_status_id' => 'nullable|integer'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return ['status' => false, 'error' => $validator->errors()];
            }

            $data = $validator->validated();

            if(!isset($data['task_status_id'])){
                $status = TaskStatus::orderBy('id', 'asc')->first();

                if(!isset($status)) throw new Exception('Não tem nenhm status de tarefas cadastrado');

                $data['task_status_id'] = $status->id;
            }

            $task = Task::create($data);

            if(isset($request->sub_tasks)){
                foreach($request->sub_tasks as $sub_task){
                    SubTask::updateOrCreate(
                        [
                            'id' => $sub_task['id']] ,
                        [
                            'description' => $sub_task['description'],
                            'status' => $sub_task['status'] ?? false,
                            'task_id' => $task->id,
                        ]
                    );
                }
            }

            if(isset($request->tasks_files)){
                foreach($request->tasks_files as $file){
                    $path = $file->store('tasks_files');

                    TaskFile::create(
                        [
                            'name' => $file->getClientOriginalName(),
                            'path' => $path,
                            'task_id' => $task->id,
                        ]
                    );
                }
            }

            return ['status' => true, 'data' => $task];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage()];
        }
    }

    public function update($request, $user_id)
    {
        try {
            $rules = [
                'name' => 'required|string|max:255',
                'user_id' => 'required|integer',
                'concluded_at' => 'required|date',
                'description'  => 'required|string',
                'task_status_id' => 'required|integer'
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) throw new Exception($validator->errors());

            $tasksToUpdate = Task::find($user_id);

            if(!isset($tasksToUpdate)) throw new Exception('Tarefa não encontrada');

            $tasksToUpdate->update($validator->validated());

            if(isset($request->sub_tasks)){
                foreach($request->sub_tasks as $sub_task){
                    SubTask::updateOrCreate(
                        [
                            'id' => $sub_task['id']] ,
                        [
                            'description' => $sub_task['description'],
                            'status' => $sub_task['status']
                        ]
                    );
                }
            }

            if(isset($request->tasks_files)){
                foreach($request->tasks_files as $file){
                    $path = $file->store('tasks_files');

                    TaskFile::create(
                        [
                            'name' => $file->getClientOriginalName(),
                            'path' => $path,
                            'task_id' => $tasksToUpdate->id,
                        ]
                    );
                }
            }

            return ['status' => true, 'data' => $tasksToUpdate];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage()];
        }
    }

    public function delete($id){
        try{
            $task = Task::find($id);

            if(!$task) throw new Exception('Tarefa não encontrado');

            $taskName = $task->name;
            $task->delete();

            return ['status' => true, 'data' => $taskName];
        }catch(Exception $error) {
            return ['status' => false, 'error' => $error->getMessage()];
        }
    }

    public function change_status_sub_tasks($id){
        try {

            $subTask = SubTask::find($id);

            if(!isset($subTask)) throw new Exception ("SubTask não encontrada");

            $subTask->update([
                'status' => !$subTask->status
            ]);

            return ['status' => true, 'data' => $subTask];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage()];
        }
    }

    public function delete_sub_tasks($id){
        try{
            $subTask = SubTask::find($id);

            if(!isset($subTask)) throw new Exception ("SubTask não encontrada");

            $taskStatusDescription= $subTask->description;
            $subTask->delete();

            return ['status' => true, 'data' => $taskStatusDescription];
        }catch(Exception $error) {
            return ['status' => false, 'error' => $error->getMessage()];
        }
    }

    public function delete_task_file($id){
        try{
            $taskFile = TaskFile::find($id);

            if(!isset($taskFile)) throw new Exception ("Arquivo não encontrado");

            Storage::delete($taskFile->path);

            $taskFileName= $taskFile->name;
            $taskFile->delete();

            return ['status' => true, 'data' => $taskFileName];
        }catch(Exception $error) {
            return ['status' => false, 'error' => $error->getMessage()];
        }
    }

    public function create_status($request){
        try {
            $rules = [
                'name' => 'required|string|max:255',
                'color' => 'required|string|max:255',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return ['status' => false, 'error' => $validator->errors()];
            }

            $taskStatus = TaskStatus::create($validator->validated());

            return ['status' => true, 'data' => $taskStatus];
        } catch (Exception $error) {
            return ['status' => false, 'error' => $error->getMessage()];
        }
    }

    public function delete_status($id){
        try{
            $taskStatus = TaskStatus::find($id);

            if(!$taskStatus) throw new Exception('Status de Tarefa não encontrada');

            $taskStatusName = $taskStatus->name;
            $taskStatus->delete();

            return ['status' => true, 'data' => $taskStatusName];
        }catch(Exception $error) {
            return ['status' => false, 'error' => $error->getMessage()];
        }
    }
}
