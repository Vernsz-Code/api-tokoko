<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'title', 
        'description', 
        'thumbnail', 
        'store_id', 
        'price', 
        'stock', 
        'status', 
        'category_id'
    ];

    // Add relationships if necessary
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
