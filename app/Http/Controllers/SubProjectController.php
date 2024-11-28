<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubProject;
use App\Models\SubProjectUser;
use illuminate\Validation\ValidationException;
use illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
use Illuminate\Database\QueryException;
class SubProjectController extends Controller
{
     
    public function subprojectStore(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'sub_project_name' => 'required|string|max:255',
                'sub_project_start_date' => 'required|date',
                'sub_project_end_date' => 'required|date|after_or_equal:sub_project_start_date',
                'sub_project_status' => 'nullable|integer',
                'sub_project_manager' => 'nullable|integer',
                'project_id' => 'required|integer',
                'user_ids' => 'nullable|array',
                'user_ids.*' => 'integer|exists:users,user_id',
            ]);
    
    
            $subproject = SubProject::create($validatedData);
    
            if (!empty($validatedData['user_ids'])) {
                foreach ($validatedData['user_ids'] as $userId) {
                    SubProjectUser::create([
                        'sub_project_id' => $subproject->sub_project_id,
                        'user_id' => $userId,
                    ]);
                }
            }
            $subproject->load('assignedUsers'); 
    
            return response()->json([
                'message' => 'Subproject created successfully',
                'subproject' => $subproject,
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating the subproject',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    public function subprojectList()
    {
        try {
          
            $subprojects = SubProject::with('assignedUsers')->get();
            if ($subprojects->isEmpty()) {
                return response()->json([
                    'message' => 'No subprojects found',
                ], 404);
            }
            return response()->json([
                'subprojects' => $subprojects,
            ], 200);

        } catch (QueryException $e) {

            return response()->json([
                'message' => 'A database error occurred',
                'error' => $e->getMessage(),
            ], 500);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function subprojectById($id)
    {
        try {

            $subproject = SubProject::with('assignedUsers')->findOrFail($id);
            return response()->json([
                'subproject' => $subproject,
            ], 200);

        } catch (ModelNotFoundException $e) {
        
            return response()->json([
                'message' => 'Subproject not found',
            ], 404);

        } catch (Exception $e) {
          
            return response()->json([
                'message' => 'An unexpected error occurred',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getSubprojectsByProjectId($project_id)
{
    try {
        // Fetch subprojects that belong to the given project_id
        $subprojects = SubProject::with('assignedUsers')  // Assuming `assignedUsers` is a relationship on SubProject
                                 ->where('project_id', $project_id)  // Filter subprojects by project_id
                                 ->get();  // Get all subprojects for the project
        
        if ($subprojects->isEmpty()) {
            return response()->json([
                'message' => 'No subprojects found for this project',
            ], 404);
        }

        return response()->json([
            'subprojects' => $subprojects,
        ], 200);
    } catch (Exception $e) {
        // Catch any other exceptions
        return response()->json([
            'message' => 'An unexpected error occurred',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    public function subprojectUpdate(Request $request, $id)
    {
        try {
            // Validate incoming request data
            $validatedData = $request->validate([
                'sub_project_name' => 'required|string|max:255',
                'sub_project_start_date' => 'required|date',
                'sub_project_end_date' => 'required|date|after_or_equal:sub_project_start_date',
                'sub_project_status' => 'nullable|integer',
                'sub_project_manager' => 'nullable|integer',
                'project_id' => 'required|integer',
                'user_ids' => 'nullable|array',  // Array of user IDs to update association
                'user_ids.*' => 'integer|exists:users,user_id',  // Ensure the user exists
            ]);
    
            // Find the subproject by ID, or return a 404 error if not found
            $subproject = SubProject::findOrFail($id);
    
            // Update the subproject with the validated data
            $subproject->update($validatedData);
    
            // Check if user_ids are provided in the request
            if (isset($validatedData['user_ids'])) {
                // Remove existing associations before adding new ones
                SubProjectUser::where('sub_project_id', $subproject->sub_project_id)->delete();
    
                // Add new user associations
                foreach ($validatedData['user_ids'] as $userId) {
                    SubProjectUser::create([
                        'sub_project_id' => $subproject->sub_project_id,
                        'user_id' => $userId,
                    ]);
                }
            }
    
            // Reload the subproject with its updated associated users
            $subproject->load('assignedUsers'); // Assuming you have a relationship defined in the SubProject model
    
            return response()->json([
                'message' => 'Subproject updated successfully',
                'subproject' => $subproject,
            ], 200);
    
        } catch (ValidationException $e) {
            // Handle validation errors
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (ModelNotFoundException $e) {
            // Handle case where subproject is not found
            return response()->json([
                'message' => 'Subproject not found',
            ], 404);
        } catch (Exception $e) {
            // Handle unexpected errors
            return response()->json([
                'message' => 'An error occurred while updating the subproject',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    

    public function subprojectDelete($id)
    {
        try {
            $subproject = SubProject::findOrFail($id);
            $subproject->delete();

  
            return response()->json([
                'message' => 'Subproject deleted successfully',
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Subproject not found',
            ], 404);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred while deleting the subproject',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


}
