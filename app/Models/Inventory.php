<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Inventory extends Model
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'capacity',
        'current_stock',
        'location',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('quantity')->withTimestamps();
    }
}
