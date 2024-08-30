<?php

namespace App\Http\Controllers\api\suppliers;

use App\Models\Supplier;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    // Create a new supplier
    public function create(Request $request)
    {
        try {
            $validateSupplier = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|max:255|unique:suppliers',
                'phone' => 'required|string|max:255|unique:suppliers',
                'address' => 'required|string|max:255',
            ]);

            if ($validateSupplier->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateSupplier->errors()
                ], 401);
            }

            $supplier = Supplier::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Supplier created successfully',
                'supplier' => $supplier,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Get all suppliers
    public function index()
    {
        try {
            $suppliers = Supplier::all();
            return response()->json([
                'status' => true,
                'message' => 'All suppliers',
                'suppliers' => $suppliers
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Update a supplier
    public function update(Request $request, $id)
    {
        try {
            $supplier = Supplier::find($id);

            if (!$supplier) {
                return response()->json([
                    'status' => false,
                    'message' => 'Supplier not found',
                ], 404);
            }

            $validateSupplier = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:suppliers,email,' . $id,
                'phone' => 'required|string|max:255|unique:suppliers,phone,' . $id,
                'address' => 'required|string|max:255',
            ]);

            if ($validateSupplier->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateSupplier->errors()
                ], 401);
            }

            $supplier->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Supplier updated successfully',
                'supplier' => $supplier,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

        // Delete a supplier
    public function delete($id) {
        try {
            $supplier = Supplier::find($id);

            if (!$supplier) {
                return response()->json([
                    'status' => false,
                    'message' => 'Supplier not found',
                ], 404);
            }

            $supplier->delete();
            return response()->json([
                'status' => true,
                'message' => 'Supplier deleted successfully',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
