<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TaskService;
use App\Http\Resources\TaskResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
            return response()->json(['message' => 'You are not authorized to view tasks.'], 403);
        }

        try {
            $tasks = $this->taskService->getAllTasks();
            return TaskResource::collection($tasks);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve tasks. Please try again later.'], 500);
        }
    }

    public function store(Request $request)
    {
        if (!Gate::allows('create-task', Auth::user())) {
            return response()->json(['message' => 'You are not authorized to create tasks.'], 403);
        }

        try {
            $task = $this->taskService->createTask($request);
            return new TaskResource($task);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create task. Please try again later.'], 500);
        }
    }

    public function show($id)
    {
        try {
            $task = $this->taskService->getTask($id);

            if (!Gate::allows('view-task', $task)) {
                return response()->json(['message' => 'You are not authorized to view this task.'], 403);
            }

            return new TaskResource($task);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Task not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to retrieve task. Please try again later.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $task = $this->taskService->getTask($id);

            if (!Gate::allows('update-task', $task)) {
                return response()->json(['message' => 'You are not authorized to update this task.'], 403);
            }

            $updatedTask = $this->taskService->updateTask($request, $id);
            return new TaskResource($updatedTask);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Task not found.'], 404);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update task. Please try again later.'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $task = $this->taskService->getTask($id);

            if (!Gate::allows('delete-task', $task)) {
                return response()->json(['message' => 'You are not authorized to delete this task.'], 403);
            }

            $this->taskService->deleteTask($id);
            return response()->json(['message' => 'Task deleted successfully.'], 204);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Task not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete task. Please try again later.'], 500);
        }
    }
}