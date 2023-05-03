<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddCartRequest;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cartItems = $request->user()->cartItems()->with(['productSku.product'])->get();
        $addresses = $request->user()->addresses()->orderBy('last_used_at','desc')->get();
        return view('cart.index',[
            'cartItems' => $cartItems,
            'addresses' => $addresses,
        ]);
    }

    public function add(AddCartRequest $request)
    {
        $user = $request->user();
        $skuId = $request->input('sku_id');
        $amount = $request->input('amount');
        //从数据库中查询该商品是否存在购物车中
        if ($cart = $user->cartItems()->where('product_sku_id', $skuId)->first()) {
            $cart->update([
                'amount' => $cart->amount + $amount
            ]);
        } else {
            $cart= new \App\Models\CartItem(['amount'=>$amount]);
            $cart->user()->associate($user);
            $cart->productSku()->associate($skuId);
            $cart->save();
        }
        return [];
    }
}
