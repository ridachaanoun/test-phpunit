<?php

namespace App\Http\Controllers\api\permissionrole;

use App\Models\Permission;
use App\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PermissionRoleController extends Controller
{
    // Add a permission to a role
    public function addPermissionToRole(Request $request, $roleId, $permissionId)
    {
        try {
            $validator = Validator::make($request->all(), []);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 401);
            }

            $role = Role::find($roleId);
            $permission = Permission::find($permissionId);

            if (!$role || !$permission) {
                return response()->json([
                    'status' => false,
                    'message' => 'Role or permission not found',
                ], 404);
            }

            $role->permissions()->attach($permission);
            return response()->json([
                'status' => true,
                'message' => 'Permission added to role successfully',
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Get all Permissions for a role
    public function getAllPermissionRoles() {

        try {
            $permissions = DB::table('permission_role')->get();
            return response()->json([
                'status' => true,
                'message' => 'All Permissions for a role',
                'permissions' => $permissions
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Delete a permission from a role
    public function deletePermissionFromRole($roleId, $permissionId) {
        try {
            $role = Role::find($roleId);
            $permission = Permission::find($permissionId);

            if (!$role || !$permission) {
                return response()->json([
                    'status' => false,
                    'message' => 'Role or permission not found',
                ], 404);
            }

            $role->permissions()->detach($permission);
            return response()->json([
                'status' => true,
                'message' => 'Permission deleted from role successfully',
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
