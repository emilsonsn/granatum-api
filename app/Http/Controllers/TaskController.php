<?php

namespace App\Http\Controllers;

use App\Services\Task\TasksService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    private $tasksService;

    public function __construct(TasksService $tasksService)
    {
        $this->tasksService = $tasksService;
    }

    public function search(Request $request)
    {
        $result = $this->tasksService->search($request);

        return $result;
    }

    public function create(Request $request)
    {
        $result = $this->tasksService->create($request);

        if ($result['status']) $result['message'] = "Tarefa criada com sucesso";
        return $this->response($result);
    }

    public function update(Request $request, $id)
    {
        $result = $this->tasksService->update($request, $id);

        if ($result['status']) $result['message'] = "Tarefa atualizada com sucesso";
        return $this->response($result);
    }

    public function delete($id)
    {
        $result = $this->tasksService->delete($id);

        if ($result['status']) $result['message'] = "Tarefa Deletada com sucesso";
        return $this->response($result);
    }

    // Sub task

    public function change_status_sub_tasks($id)
    {
        $result = $this->tasksService->change_status_sub_tasks($id);

        if ($result['status']) $result['message'] = "Sub-Tarefa atualizada com sucesso";
        return $this->response($result);
    }


    public function delete_sub_tasks($id)
    {
        $result = $this->tasksService->delete_sub_tasks($id);

        if ($result['status']) $result['message'] = "Sub-Tarefa deletada com sucesso";
        return $this->response($result);
    }

    // Status

    public function getStatus()
    {
        $result = $this->tasksService->getStatus();

        return $this->response($result);
    }


    public function create_status($request)
    {
        $result = $this->tasksService->create_status($request);

        if ($result['status']) $result['message'] = "Sub-Tarefa deletada com sucesso";
        return $this->response($result);
    }

    public function delete_task_file($id)
    {
        $result = $this->tasksService->delete_task_file($id);

        if ($result['status']) $result['message'] = "Anexo deletado com sucesso";
        return $this->response($result);
    }

    public function delete_status($id)
    {
        $result = $this->tasksService->delete_status($id);

        if ($result['status']) $result['message'] = "Sub-Tarefa deletada com sucesso";
        return $this->response($result);
    }

    private function response($result)
    {
        return response()->json([
            'status' => $result['status'],
            'message' => $result['message'] ?? null,
            'data' => $result['data'] ?? null,
            'error' => $result['error'] ?? null
        ], $result['statusCode'] ?? 200);
    }
}
