<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\TaskRepositoryInterface;


class TaskService
{
    public $taskRepository;

    public function __construct(TaskRepositoryInterface $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function getAllTasks()
    {
        return $this->taskRepository->all();
    }

    public function createTask(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $data['user_id'] = Auth::id();
        return $this->taskRepository->create($data);
    }

    public function getTask($id)
    {
        return $this->taskRepository->find($id);
    }

    public function updateTask(Request $request, $id)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        return $this->taskRepository->update($id, $data);
    }

    public function deleteTask($id)
    {
        $this->taskRepository->delete($id);
    }
}