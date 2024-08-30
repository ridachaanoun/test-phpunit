<?php

namespace App\Http\Controllers\api\users;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    // Get all users
    public function index()
    {
        try {
            $users = User::all();
            return response()->json([
                'status' => 'success',
                'users' => $users
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'fail',
                'message' => $th->getMessage()
            ]);
        }
    }

    // Update a user
    public function update(Request $request, $id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'User not found'
                ]);
            }

            $validateUser = Validator::make($request->all(), [
                'role_id' => 'required|integer|exists:roles,id',
            ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Validation error',
                    'errors' => $validateUser->errors()
                ]);
            }

            $user->update([
                'role_id' => $request->role_id
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'User updated successfully',
                'user' => $user
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'fail',
                'message' => $th->getMessage()
            ]);
        }
    }

    // Delete a user
    public function delete($id)
    {
        try {
            $user = User::find($id);

            if (!$user) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'User not found'
                ]);
            }

            if ($user->role->name !== 'Admin') {
                $user->delete();
            } else if ($user->role->name === 'Admin') {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'Admin cannot be deleted'
                ]);
            }
            
            return response()->json([
                'status' => 'success',
                'message' => 'User deleted successfully'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 'fail',
                'message' => $th->getMessage()
            ]);
        }
    }
}
