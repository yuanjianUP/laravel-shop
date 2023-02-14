<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function list()
    {
        $products = Product::query()->where(['on_sale'=>true])->paginate();
        return response()->json($products);
    }
}
