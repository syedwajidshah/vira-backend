<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubProject;
use illuminate\Validation\ValidationException;
use illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;
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
            ]);

            $subproject = SubProject::create($validatedData);

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
            $subprojects = SubProject::all();

            return response()->json([
                'subprojects' => $subprojects,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching subprojects',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function subprojectById($id)
    {
        try {
            $subproject = SubProject::findOrFail($id);

            return response()->json([
                'subproject' => $subproject,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Subproject not found',
            ], 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching the subproject',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function subprojectUpdate(Request $request, $id)
    {
        try {
            $subproject = SubProject::findOrFail($id);

            $validatedData = $request->validate([
                'sub_project_name' => 'sometimes|string|max:255',
                'sub_project_start_date' => 'sometimes|date',
                'sub_project_end_date' => 'sometimes|date|after_or_equal:sub_project_start_date',
                'sub_project_status' => 'nullable|integer',
                'sub_project_manager' => 'nullable|integer',
                'project_id' => 'required|integer',
            ]);

            $subproject->update($validatedData);

            return response()->json([
                'message' => 'Subproject updated successfully',
                'subproject' => $subproject,
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Subproject not found',
            ], 404);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (Exception $e) {
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
                'message' => 'An error occurred while deleting the subproject',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


}
