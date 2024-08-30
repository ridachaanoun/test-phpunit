<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\auth\AuthController;
use App\Http\Controllers\api\roles\RoleController;
use App\Http\Controllers\api\users\UserController;
use App\Http\Controllers\api\customers\CustomerController;
use App\Http\Controllers\api\orders\OrderController;
use App\Http\Controllers\api\categories\CategoryController;
use App\Http\Controllers\api\suppliers\SupplierController;
use App\Http\Controllers\api\products\ProductController;
use App\Http\Controllers\api\inventories\InventoryController;
use App\Http\Controllers\api\permissions\PermissionController;
use App\Http\Controllers\api\orderproduct\OrderProductController;
use App\Http\Controllers\api\inventoryproduct\InventoryProductController;
use App\Http\Controllers\api\permissionrole\PermissionRoleController;

// Register & login routes
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    // Roles & Permissions CRUD routes with Role (Admin) middleware
    Route::post('roles/create', [RoleController::class, 'create'])
        ->middleware('role:Admin');
    Route::get('roles/index', [RoleController::class, 'index'])
        ->middleware('role:Admin');
    Route::put('roles/update/{id}', [RoleController::class, 'update'])
        ->middleware('role:Admin');
    Route::delete('roles/delete/{id}', [RoleController::class, 'delete'])
        ->middleware('role:Admin');

    Route::post('permissions/create', [PermissionController::class, 'create'])
        ->middleware('role:Admin');
    Route::get('permissions/index', [PermissionController::class, 'index'])
        ->middleware('role:Admin');
    Route::put('permissions/update/{id}', [PermissionController::class, 'update'])
        ->middleware('role:Admin');
    Route::delete('permissions/delete/{id}', [PermissionController::class, 'delete'])
        ->middleware('role:Admin');

    Route::post('roles/{roleId}/permissions/{permissionId}/create', [PermissionRoleController::class, 'addPermissionToRole'])
        ->middleware('role:Admin');
    Route::get('roles/permissions/index', [PermissionRoleController::class, 'getAllPermissionRoles'])
        ->middleware('role:Admin');
    Route::delete('roles/{roleId}/permissions/{permissionId}/delete', [PermissionRoleController::class, 'deletePermissionFromRole'])
        ->middleware('role:Admin');   

    // Users CRUD routes with Permissions middleware
    Route::get('users/index', [UserController::class, 'index']);
        // ->middleware('permission:Index users');
    Route::put('users/update/{id}', [UserController::class, 'update']);
        // ->middleware('permission:Update user');
    Route::delete('users/delete/{id}', [UserController::class, 'delete']);
        // ->middleware('permission:Delete user');

    // Logout route
    Route::post('auth/logout', [AuthController::class, 'logout']);

    // Customers routes
    Route::post('customers/create', [CustomerController::class, 'create']);
    Route::get('customers/index', [CustomerController::class, 'index']);
    Route::put('customers/update/{id}', [CustomerController::class, 'update']);
    Route::delete('customers/delete/{id}', [CustomerController::class, 'delete']);

    // Orders routes
    Route::post('orders/create', [OrderController::class, 'create']);
    Route::get('orders/index', [OrderController::class, 'index']);
    Route::put('orders/update/{id}', [OrderController::class, 'update']);
    Route::delete('orders/delete/{id}', [OrderController::class, 'delete']);

    // Categories routes
    Route::post('categories/create', [CategoryController::class, 'create']);
    Route::get('categories/index', [CategoryController::class, 'index']);
    Route::put('categories/update/{id}', [CategoryController::class, 'update']);
    Route::delete('categories/delete/{id}', [CategoryController::class, 'delete']);

    // Suppliers routes
    Route::post('suppliers/create', [SupplierController::class, 'create']);
    Route::get('suppliers/index', [SupplierController::class, 'index']);
    Route::put('suppliers/update/{id}', [SupplierController::class, 'update']);
    Route::delete('suppliers/delete/{id}', [SupplierController::class, 'delete']);

    // Products routes
    Route::post('products/create', [ProductController::class, 'create']);
    Route::get('products/index', [ProductController::class, 'index']);
    Route::put('products/update/{id}', [ProductController::class, 'update']);
    Route::delete('products/delete/{id}', [ProductController::class, 'delete']);

    // Inventories routes
    Route::post('inventories/create', [InventoryController::class, 'create']);
    Route::get('inventories/index', [InventoryController::class, 'index']);
    Route::put('inventories/update/{id}', [InventoryController::class, 'update']);
    Route::delete('inventories/delete/{id}', [InventoryController::class, 'delete']);

    // OrderProduct routes
    Route::post('orders/{orderId}/products/{productId}/create', [OrderProductController::class, 'addProductToOrder']);
    Route::get('orders/products/index', [OrderProductController::class, 'getAllOrderProducts']);
    Route::put('orders/{orderId}/products/{productId}/update', [OrderProductController::class, 'updateProductInOrder']);
    Route::delete('orders/{orderId}/products/{productId}/delete', [OrderProductController::class, 'deleteProductFromOrder']);

    // InventoryProduct routes
    Route::post('inventories/{inventoryId}/products/{productId}/create', [InventoryProductController::class, 'addProductToInventory']);
    Route::get('inventories/products/index', [InventoryProductController::class, 'getAllInventoryProducts']);
    Route::put('inventories/{inventoryId}/products/{productId}/update', [InventoryProductController::class, 'updateProductInInventory']);
    Route::delete('inventories/{inventoryId}/products/{productId}/delete', [InventoryProductController::class, 'deleteProductFromInventory']);
});
