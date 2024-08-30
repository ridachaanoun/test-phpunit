<?php

namespace App\Http\Controllers\api\permissions;

use App\Models\Permission;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    // Create a new permission
    public function create(Request $request)
    {
        try {
            $validatePermission = Validator::make($request->all(), [
                'label' => 'required|string|max:255',
                'description' => 'required|string|max:255',
            ]);

            if ($validatePermission->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validatePermission->errors()
                ], 401);
            }

            $permission = Permission::create([
                'label' => $request->label,
                'description' => $request->description
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Permission created successfully',
                'permission' => $permission,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Get all permissions
    public function index()
    {
        try {
            $permissions = Permission::all();
            return response()->json([
                'status' => true,
                'message' => 'Permissions fetched successfully',
                'permissions' => $permissions,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Update a permission
    public function update(Request $request, $id)
    {
        try {
            $permission = Permission::find($id);

            if (!$permission) {
                return response()->json([
                    'status' => false,
                    'message' => 'Permission not found',
                ], 404);
            }

            $validatePermission = Validator::make($request->all(), [
                'label' => 'required|string|max:255',
                'description' => 'required|string|max:255',
            ]);

            if ($validatePermission->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validatePermission->errors()
                ], 401);
            }

            $permission->update([
                'label' => $request->label,
                'description' => $request->description
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Permission updated successfully',
                'permission' => $permission,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Delete a permission
    public function delete($id)
    {
        try {
            $permission = Permission::find($id);

            if (!$permission) {
                return response()->json([
                    'status' => false,
                    'message' => 'Permission not found',
                ], 404);
            }

            $permission->delete();
            return response()->json([
                'status' => true,
                'message' => 'Permission deleted successfully',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
