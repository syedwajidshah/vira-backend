<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

use Exception;

class ProjectController extends Controller
{
    public function projectStore(Request $request)
    {
        try {
            // Validate request data
            $validatedData = $request->validate([
                'project_name' => 'required|string|max:255',
                'project_start_date' => 'required|date',
                'project_end_date' => 'required|date|after_or_equal:project_start_date',
                'project_status' => 'nullable|integer',
                'project_manager' => 'nullable|integer', // Not a foreign key, just an integer
            ]);

            // Create the project
            $project = Project::create($validatedData);

            return response()->json([
                'message' => 'Project created successfully',
                'project' => $project,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating the project',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function projectList()
    {
        try {
            $projects = Project::all();

            return response()->json([
                'projects' => $projects,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching projects',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    

    public function projectById($id)
    {
        try {
            $project = Project::findOrFail($id);

            return response()->json([
                'project' => $project,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Project not found',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching the project',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function projectUpdate(Request $request, $id)
    {
        try {
            $project = Project::findOrFail($id);

            // Validate request data
            $validatedData = $request->validate([
                'project_name' => 'sometimes|string|max:255',
                'project_start_date' => 'sometimes|date',
                'project_end_date' => 'sometimes|date|after_or_equal:project_start_date',
                'project_status' => 'nullable|integer',
                'project_manager' => 'nullable|integer',
            ]);

            // Update project
            $project->update($validatedData);

            return response()->json([
                'message' => 'Project updated successfully',
                'project' => $project,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Project not found',
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating the project',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function projectDelete($id)
    {
        try {
            $project = Project::findOrFail($id);
            $project->delete();

            return response()->json([
                'message' => 'Project deleted successfully',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Project not found',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while deleting the project',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


}
