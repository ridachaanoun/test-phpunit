<?php

namespace App\Http\Controllers\api\inventoryproduct;

use App\Models\Inventory;
use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InventoryProductController extends Controller
{
    // Add a product to an inventory
    public function addProductToInventory(Request $request, $inventoryId, $productId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'quantity' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 401);
            }

            $inventory = Inventory::find($inventoryId);
            $product = Product::find($productId);

            if (!$inventory || !$product) {
                return response()->json([
                    'status' => false,
                    'message' => 'Inventory or product not found',
                ], 404);
            }

            $inventory->products()->attach($product, [
                'quantity' => $request->quantity,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Product added to inventory successfully',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Get all InventoryProducts
    public function getAllInventoryProducts()
    {
        try {
            $inventoryProducts = DB::table('inventory_product')->get();

            return response()->json([
                'status' => true,
                'message' => 'All InventoryProducts',
                'inventoryProducts' => $inventoryProducts
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Update a product in an inventory (by product id and inventory id)
    public function updateProductInInventory(Request $request, $inventoryId, $productId) {
        try {
            $validator = Validator::make($request->all(), [
                'quantity' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 401);
            }

            $inventory = Inventory::find($inventoryId);
            $product = Product::find($productId);

            if (!$inventory || !$product) {
                return response()->json([
                    'status' => false,
                    'message' => 'Inventory or product not found',
                ], 404);
            }

            $inventory->products()->updateExistingPivot($product, [
                'quantity' => $request->quantity,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Product updated in inventory successfully',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Delete a product from an inventory
    public function deleteProductFromInventory($inventoryId, $productId) {
        try {
            $inventory = Inventory::find($inventoryId);
            $product = Product::find($productId);

            if (!$inventory || !$product) {
                return response()->json([
                    'status' => false,
                    'message' => 'Inventory or product not found',
                ], 404);
            }

            $inventory->products()->detach($product);

            return response()->json([
                'status' => true,
                'message' => 'Product deleted from inventory successfully',
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
