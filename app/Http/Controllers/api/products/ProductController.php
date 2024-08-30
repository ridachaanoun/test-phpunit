<?php

namespace App\Http\Controllers\api\products;

use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    // Create a new product
    public function create(Request $request)
    {
        try {
            $validateProduct = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'description' => 'required|string|max:255',
                'price' => 'required|numeric',
                'category_id' => 'required|integer|exists:categories,id',
                'supplier_id' => 'required|integer|exists:suppliers,id',
            ]);

            if ($validateProduct->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateProduct->errors()
                ], 401);
            }

            $product = Product::create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'category_id' => $request->category_id,
                'supplier_id' => $request->supplier_id,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Product created successfully',
                'product' => $product,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Get all products
    public function index()
    {
        try {
            $products = Product::all();
            return response()->json([
                'status' => true,
                'message' => 'All products',
                'products' => $products
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Update an product
    public function update(Request $request, $id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'status' => false,
                    'message' => 'Product not found',
                ], 404);
            }

            $validateProduct = Validator::make($request->all(), [
                'name' => 'required|string',
                'description' => 'required|string',
                'price' => 'required|numeric',
                'category_id' => 'required|integer|exists:categories,id',
                'supplier_id' => 'required|integer|exists:suppliers,id',
            ]);

            if ($validateProduct->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateProduct->errors()
                ], 401);
            }

            $product->update([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'category_id' => $request->category_id,
                'supplier_id' => $request->supplier_id,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Product updated successfully',
                'product' => $product
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Delete an product
    public function delete($id)
    {
        try {
            $product = Product::find($id);

            if (!$product) {
                return response()->json([
                    'status' => false,
                    'message' => 'Product not found',
                ], 404);
            }

            $product->delete();
            return response()->json([
                'status' => true,
                'message' => 'Product deleted successfully',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }
}
