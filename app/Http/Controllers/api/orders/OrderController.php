<?php

namespace App\Http\Controllers\api\orders;

use App\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    // Create a new order
    public function create(Request $request)
    {
        try {
            $validateOrder = Validator::make($request->all(), [
                'total_price' => 'required|numeric',
                'status' => 'nullable|in:processing,completed,cancelled',
                'customer_id' => 'required|integer|exists:customers,id',
                'user_id' => 'required|integer|exists:users,id',
            ]);

            if ($validateOrder->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateOrder->errors()
                ], 401);
            }

            $order = order::create([
                'total_price' => $request->total_price,
                'status' => $request->status ?? 'processing',
                'customer_id' => $request->customer_id,
                'user_id' => $request->user_id,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Order created successfully',
                'order' => $order,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Get all orders
    public function index()
    {
        try {
            $orders = Order::all();
            return response()->json([
                'status' => true,
                'message' => 'All orders',
                'orders' => $orders
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Update an order
    public function update(Request $request, $id)
    {
        try {
            $order = Order::find($id);

            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order not found',
                ], 404);
            }

            $validateOrder = Validator::make($request->all(), [
                'total_price' => 'required|numeric',
                'status' => 'nullable|in:processing,completed,cancelled',
                'customer_id' => 'required|integer|exists:customers,id',
                'user_id' => 'required|integer|exists:users,id',

            ]);

            if ($validateOrder->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateOrder->errors()
                ], 401);
            }

            $order->update([
                'total_price' => $request->total_price,
                'status' => $request->status ?? 'processing',
                'customer_id' => $request->customer_id,
                'user_id' => $request->user_id,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Order updated successfully',
                'order' => $order,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

        // Delete an order
    public function delete($id) {
        try {
            $order = Order::find($id);

            if (!$order) {
                return response()->json([
                    'status' => false,
                    'message' => 'Order not found',
                ], 404);
            }

            $order->delete();
            return response()->json([
                'status' => true,
                'message' => 'Order deleted successfully',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

}
