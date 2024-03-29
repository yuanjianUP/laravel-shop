<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Models\CouponCode;
use App\Models\Order;
use App\Models\ProductSku;
use App\Models\UserAddress;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::query()
        ->with(['items.product','items.productSku'])
        ->where('user_id',$request->user()->id)
        ->orderBy('created_at','desc')
        ->paginate();
        return view('orders.index',['orders' => $orders]);
    }

    public function store(OrderRequest $request)
    {
        $user = $request->user();
        $coupon = null;
        if($code = $request->input('coupon_code')){
            $coupon = CouponCode::where('code',$code)->first();
            if(!$coupon){
                throw new \App\Exceptions\CouponCodeUnavailableException('优惠券不存在');
            }
        }
        $order = \DB::transaction(function () use ($user, $request) {
            $address = UserAddress::find($request->input('address_id'));
            $address->update(['last_used_at' => Carbon::now()]);
            //创建一个订单
            $order = new Order([
                'address' => [
                    'address' => $address->full_address,
                    'zip' => $address->zip,
                    'contact_name' => $address->contact_name,
                    'contact_phone' => $address->contact_phone,
                ],
                'remark' => $request->input('remark'),
                'total_amount' => 0,
            ]);
            //订单关联到当前用户
            $order->user()->associate($user);
            $order->save();

            $totalAmount = 0;
            $items = $request->input('items');
            //遍历用户提交的SKU
            foreach ($items as $data) {
                $sku = ProductSku::find($data['sku_id']);
                //创建一个OrderItem 并直接与当前订单关联
                $item = $order->items()->make([
                    'amount' => $data['amount'],
                    'price' => $sku->price,
                ]);
                $item->product()->associate($sku->product_id);
                $item->productSku()->associate($sku);
                $item->save();
                $totalAmount += $sku->price * $data['amount'];
                if ($sku->decreaseStock($data['amount']) <= 0) {
                    throw new \Exception('该商品库存不足');
                }
            }
            $order->update(['total_amount' => $totalAmount]);
            //将下单的商品从购物车中移除
            $skuIds = collect($request->input('items'))->pluck('sku_id');
            $user->cartItems()->whereIn('product_sku_id', $skuIds)->delete();
            //出发任务
            $this->dispatch(new \App\Jobs\CloseOrder($order,config('app.order_ttl')));
            return $order;
        });
    }

    public function show(Order $order,Request $request)
    {
        $this->authorize('own',$order);
        return view('orders.show',['order' => $order->load(['items.productSku','items.product'])]);
    }
}
