<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends Controller
{
    /**
     * return all the tasks related to a project.
     */
    public function index(Project $project)
    {
        $tasks = $project->tasks()->get();

        if($tasks->isEmpty()){
            return response()->json([
                "status"=> "success",
                "message"=> "No tasks found"
            ], Response::HTTP_OK);
        }

        return response()->json([
            "status"=> "Success",
            "message"=> "Tasks retrieved successfully",
            "tasks" => $tasks
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Project $project, TaskRequest $request)
    {
        $taskData = $request->validated();
        $taskData['project_id'] = $project->id;

        try {

            $task = Task::create($taskData);
            return response()->json([
                "status"=> "Success",
                "message"=> "Task created successfully",
                "task"=> $task
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            \Log::error('Failed to create task', [
                "user"=> Auth::user(),
                "error" => $e->getMessage(),
            ]);

            return response()->json([
                "message" => "An error occurred while creating the task.",
                "error" => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return response()->json([
            "status"=> "Success",
            "message"=> "Task retrieved successfully",
            "task" => $task,
        ],Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskRequest $request, Task $task)
    {
        $taskData = $request->validated();

        try {
            $task->update($taskData);
            return response()->json([
                "status"=> "Success",
                "message"=> "Task updated successfully",
                "task"=> $task
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            \Log::error('Failed to update task', [
                "user"=> Auth::user(),
                "error" => $e->getMessage(),
            ]);

            return response()->json([
                "message" => "An error occurred while updating the task.",
                "error" => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        try {
            $task->delete();
            return response()->json([
                "status" => "Success",
                "message"=> "Task deleted successfully"
            ], Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            \Log::error('Failed to delete Task', [
                "user"=> Auth::user(),
                "task_id"=> $task->id,
                "error" => $e->getMessage(),
            ]);

            return response()->json([
                "status" => "Error",
                "message"=> "Failed to deled task",
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function tasksCreatedBy(User $user)
    {
        $tasks = $user->createdTasks()->get();

        if ($tasks->isEmpty()) {
            return response()->json([
                "status"=> "Error",
                "message"=> "User didn't create any tasks"
            ], Response::HTTP_NO_CONTENT);
        }

        return response()->json([
            "status"=> "Success",
            "message"=> "Tasks retrieved successfully",
            "tasks" => $tasks
        ], Response::HTTP_OK);
    }

    public function tasksAssignedTo(User $user)
    {
        $tasks = $user->assignedTasks()->get();

        if ($tasks->isEmpty()) {
            return response()->json([
                "status"=> "Error",
                "message"=> "User didn't create any tasks"
            ], Response::HTTP_NO_CONTENT);
        }

        return response()->json([
            "status"=> "Success",
            "message"=> "Tasks retrieved successfully",
            "tasks" => $tasks
        ], Response::HTTP_OK);
    }
}
