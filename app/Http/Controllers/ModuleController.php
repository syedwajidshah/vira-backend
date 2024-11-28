<?php
namespace App\Http\Controllers;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\Module;
use App\Models\Subproject;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class ModuleController extends Controller
{
    public function moduleStore(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'module_title' => 'required|string|max:255',
                'module_start_date' => 'required|date',
                'module_end_date' => 'required|date|after_or_equal:module_start_date',
                'module_status' => 'required|string',
                'sub_project_id' => 'required|integer',
            ]);

            $module = Module::create($validatedData);

            return response()->json([
                'message' => 'Module created successfully',
                'module' => $module,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);

        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Database error while creating the module',
                'error' => $e->getMessage(),
            ], 500);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred while creating the module',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function moduleList()
    {
        try {
            $modules = Module::all();

            return response()->json([
                'message' => 'Modules retrieved successfully',
                'modules' => $modules,
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while retrieving the modules',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function moduleShow($id)
    {
        try {
            $module = Module::findOrFail($id);

            return response()->json([
                'message' => 'Module retrieved successfully',
                'module' => $module,
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Module not found',
                'error' => $e->getMessage(),
            ], 404);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while retrieving the module',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getModulesBySubproject($sub_project_id)
{
    try {
        // Find the subproject by ID and load its related modules
        $subproject = Subproject::with('modules')->findOrFail($sub_project_id);

        // Return the modules of the subproject
        return response()->json([
            'message' => 'Modules retrieved successfully',
            'modules' => $subproject->modules,
        ], 200);

    } catch (ModelNotFoundException $e) {
        return response()->json([
            'message' => 'Subproject not found',
            'error' => $e->getMessage(),
        ], 404);

    } catch (Exception $e) {
        return response()->json([
            'message' => 'An error occurred while retrieving the modules',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    public function moduleUpdate(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'module_title' => 'required|string|max:255',
                'module_start_date' => 'required|date',
                'module_end_date' => 'required|date|after_or_equal:module_start_date',
                'module_status' => 'required|string',
                'sub_project_id' => 'required|integer',
            ]);

            $module = Module::findOrFail($id);
            $module->update($validatedData);

            return response()->json([
                'message' => 'Module updated successfully',
                'module' => $module,
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Module not found',
                'error' => $e->getMessage(),
            ], 404);

        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Database error while updating the module',
                'error' => $e->getMessage(),
            ], 500);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred while updating the module',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Delete a specific module by ID
    public function moduleDestroy($id)
    {
        try {
            $module = Module::findOrFail($id);
            $module->delete();

            return response()->json([
                'message' => 'Module deleted successfully',
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Module not found',
                'error' => $e->getMessage(),
            ], 404);

        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Database error while deleting the module',
                'error' => $e->getMessage(),
            ], 500);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred while deleting the module',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}