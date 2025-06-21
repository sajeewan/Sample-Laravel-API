<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TaskService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Http\Resources\TaskResource;

class TaskController extends Controller
{
    protected $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    public function index()
    {
        if (!Gate::allows('view-any-task', Auth::user())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return TaskResource::collection($this->taskService->getAllTasks());
    }

    public function store(Request $request)
    {
        if (!Gate::allows('create-task', Auth::user())) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $task = $this->taskService->createTask($request);
        return new TaskResource($task);
    }

    public function show($id)
    {
        $task = $this->taskService->getTask($id);

        if (!Gate::allows('view-task', $task)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return new TaskResource($task);
    }

    public function update(Request $request, $id)
    {
        $task = $this->taskService->getTask($id);

        if (!Gate::allows('update-task', $task)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $updatedTask = $this->taskService->updateTask($request, $id);
        return new TaskResource($updatedTask);
    }

    public function destroy($id)
    {
        $task = $this->taskService->getTask($id);

        if (!Gate::allows('delete-task', $task)) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $this->taskService->deleteTask($id);
        return response()->json(['message' => 'Task deleted'], 204);
    }
}