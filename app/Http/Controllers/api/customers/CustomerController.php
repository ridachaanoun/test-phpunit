<?php

namespace App\Http\Controllers\api\customers;

use App\Models\Customer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    // Create a new customer
    public function create(Request $request)
    {
        try {
            $validateCustomer = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|max:255|unique:customers',
                'phone' => 'required|string|max:255|unique:customers',
                'address' => 'required|string|max:255',
            ]);

            if ($validateCustomer->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateCustomer->errors()
                ], 401);
            }

            $customer = Customer::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Customer created successfully',
                'customer' => $customer,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Get all customers
    public function index()
    {
        try {
            $customers = Customer::all();
            return response()->json([
                'status' => true,
                'message' => 'All customers',
                'customers' => $customers
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Update a customer
    public function update(Request $request, $id)
    {
        try {
            $customer = Customer::find($id);

            if (!$customer) {
                return response()->json([
                    'status' => false,
                    'message' => 'Customer not found',
                ], 404);
            }

            $validateCustomer = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:customers,email,' . $id,
                'phone' => 'required|string|max:255|unique:customers,phone,' . $id,
                'address' => 'required|string|max:255',
            ]);

            if ($validateCustomer->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateCustomer->errors()
                ], 401);
            }

            $customer->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Customer updated successfully',
                'customer' => $customer,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

        // Delete a customer
    public function delete($id) {
        try {
            $customer = Customer::find($id);

            if (!$customer) {
                return response()->json([
                    'status' => false,
                    'message' => 'Customer not found',
                ], 404);
            }

            $customer->delete();
            return response()->json([
                'status' => true,
                'message' => 'Customer deleted successfully',
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage()
            ], 500);
        }
    }

}
