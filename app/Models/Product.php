<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'title','description','image','on_sale','rating','sold_count','review_count','price'
    ];
    protected $casts = [
        'on_sale' => 'boolean',
    ];
    public function skus()
    {
        return $this->hasMany(ProductSku::class);
    }
}
