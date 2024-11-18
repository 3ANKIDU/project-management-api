<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::paginate(10);

        if ($projects->isEmpty()) {
            return response()->json([
                "status"=> "Success",
                "message"=> "No projects found"
            ], Response::HTTP_OK);
        }

        return response()->json([
            "status"=> "success",
            "message"=> "retrieved projects successfully",
            "projects"=> ProjectResource::collection($projects)
        ],Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProjectRequest $request)
    {
        $projectData = $request->validated();

        try {

            $project = Project::create($projectData);

            return response()->json([
                "status"=> "Success",
                "message"=> "Project created successfully",
                "project"=> $project
            ],Response::HTTP_CREATED);
        } catch (\Exception $e) {
            \Log::error('Failed to create project', [
                "user"=> Auth::user(),
                "error" => $e->getMessage(),
            ]);

            return response()->json([
                "message" => "An error occurred while creating the project.",
                "error" => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        return response()->json([
            "status"=> "Success",
            "message"=> "Project retrieved successfully",
            "project" => new ProjectResource($project),
        ],Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProjectRequest $request, Project $project)
    {
        $projectData = $request->validated();

        try {
            $project->update($projectData);

            return response()->json([
                "message" => "Project updated successfully.",
                "project" => new ProjectResource($project),
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            \Log::error('Failed to update project', [
                "user"=> Auth::user(),
                "project_id" => $project->id,
                "error" => $e->getMessage(),
            ]);

            return response()->json([
                "message" => "An error occurred while updating the project.",
                "error" => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        try {
            $project->delete();
            return response()->json([
                "status" => "Success",
                "message"=> "Project deleted successfully"
            ], Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            \Log::error('Failed to delete project', [
                "user"=> Auth::user(),
                "project_id"=> $project->id,
                "error" => $e->getMessage(),
            ]);

            return response()->json([
                "status" => "Error",
                "message"=> "Failed to deled project",
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
