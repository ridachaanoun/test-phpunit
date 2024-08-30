<?php

namespace App\Http\Controllers\api\orderproduct;

use App\Models\Order;
use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderProductController extends Controller
{
    // Add a product to an order
    public function addProductToOrder(Request $request, $orderId, $productId)
    {
        try {
            $validator = Validator::make($request->all(), [
                'quantity' => 'required|integer',
                'price' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 401);
            }

            $order = Order::find($orderId);
            $product = Product::find($productId);

            if (!$order || !$product) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order or product not found',
                ], 404);
            }

            $order->products()->attach($product, [
                'quantity' => $request->quantity,
                'price' => $request->price,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Product added to order successfully',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Get all OrderProducts
    public function getAllOrderProducts()
    {
        try {
            $orderProducts = DB::table('order_product')->get();
    
            return response()->json([
                'status' => true,
                'message' => 'All OrderProducts',
                'orderProducts' => $orderProducts
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Update a product in an order (by product id and order id)
    public function updateProductInOrder(Request $request, $orderId, $productId) {

        try {
            $validator = Validator::make($request->all(), [
                'quantity' => 'required|integer',
                'price' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 401);
            }

            $order = Order::find($orderId);
            $product = Product::find($productId);

            if (!$order || !$product) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order or product not found',
                ], 404);
            }

            $order->products()->updateExistingPivot($product, [
                'quantity' => $request->quantity,
                'price' => $request->price,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Product updated in order successfully',
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Delete a product from an order (by product id and order id)
    public function deleteProductFromOrder($orderId, $productId) {
        try {
            $order = Order::find($orderId);
            $product = Product::find($productId);

            if (!$order || !$product) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order or product not found',
                ], 404);
            }

            $order->products()->detach($product);

            return response()->json([
                'status' => true,
                'message' => 'Product deleted from order successfully',
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    } 
}
