<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Product extends Model
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'name',
        'price',
        'description',
        'category_id',
        'supplier_id',  
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class)->withPivot('quantity', 'price')->withTimestamps();
    }

    public function inventories()
    {
        return $this->belongsToMany(Inventory::class)->withPivot('quantity')->withTimestamps();
    }
}
