@extends('layouts.app')
@section('title', '购物车')

@section('content')
<div class="row">
<div class="col-lg-10 offset-lg-1">
<div class="card">
  <div class="card-header">我的购物车</div>
  <div class="card-body">
    <table class="table table-striped">
      <thead>
      <tr>
        <th><input type="checkbox" id="select-all"></th>
        <th>商品信息</th>
        <th>单价</th>
        <th>数量</th>
        <th>操作</th>
      </tr>
      </thead>
      <tbody class="product_list">
      @foreach($cartItems as $item)
        <tr data-id="{{ $item->productSku->id }}">
          <td>
            <input type="checkbox" name="select" value="{{ $item->productSku->id }}" {{ $item->productSku->product->on_sale ? 'checked' : 'disabled' }}>
          </td>
          <td class="product_info">
            <div class="preview">
              <a target="_blank" href="{{ route('products.show', [$item->productSku->product_id]) }}">
                <img src="{{ $item->productSku->product->image_url }}">
              </a>
            </div>
            <div @if(!$item->productSku->product->on_sale) class="not_on_sale" @endif>
              <span class="product_title">
                <a target="_blank" href="{{ route('products.show', [$item->productSku->product_id]) }}">{{ $item->productSku->product->title }}</a>
              </span>
              <span class="sku_title">{{ $item->productSku->title }}</span>
              @if(!$item->productSku->product->on_sale)
                <span class="warning">该商品已下架</span>
              @endif
            </div>
          </td>
          <td><span class="price">￥{{ $item->productSku->price }}</span></td>
          <td>
            <input type="text" class="form-control form-control-sm amount" @if(!$item->productSku->product->on_sale) disabled @endif name="amount" value="{{ $item->amount }}">
          </td>
          <td>
            <button class="btn btn-sm btn-danger btn-remove">移除</button>
          </td>
        </tr>
      @endforeach
      </tbody>
    </table>
    <!-- 开始 -->
    <div>
      <form class="form-horizontal" role="form" id="order-form">
        <div class="form-group row">
          <label class="col-form-label col-sm-3 text-md-right">选择收货地址</label>
          <div class="col-sm-9 col-md-7">
            <select class="form-control" name="address">
              @foreach($addresses as $address)
                <option value="{{ $address->id }}">{{ $address->full_address }} {{ $address->contact_name }} {{ $address->contact_phone }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-group row">
          <label class="col-form-label col-sm-3 text-md-right">备注</label>
          <div class="col-sm-9 col-md-7">
            <textarea name="remark" class="form-control" rows="3"></textarea>
          </div>
        </div>

        <!-- 优惠码开始 -->
        <div class="form-group row">
          <label class="col-form-label col-sm-3 text-md-right">优惠码</label>
          <div class="col-sm-4">
            <input type="text" class="form-control" name="coupon_code">
            <span class="form-text text-muted" id="coupon_desc"></span>
          </div>
          <div class="col-sm-3">
            <button type="button" class="btn btn-success" id="btn-check-coupon">检查</button>
            <button type="button" class="btn btn-danger" style="display: none;" id="btn-cancel-coupon">取消</button>
          </div>
        </div>
        <div class="form-group">
          <div class="offset-sm-3 col-sm-3">
            <button type="button" class="btn btn-primary btn-create-order">提交订单</button>
          </div>
        </div>
      </form>
    </div>
    <!-- 结束 -->
  </div>
</div>
</div>
</div>
@endsection

@section('scriptsAfterJs')
<script>
  $('.btn-create-order').click(function(){
    var req = {
      address_id: $('#order-form').find('select[name=address]').val(),
      items: [],
      remark: $('#order-form').find('textarea[name=remark]').val(),
      _token: '{{ csrf_token() }}',
    };
    //遍历table标签内所有带有data-id属性的tr标签
    $('table tr[data-id]').each(function(){
      var $checkbox = $(this).find('input[name=select][type=checkbox]');
      if($checkbox.prop('disabled') || !$checkbox.prop('checked')) {
        return;
      }
      var $input = $(this).find('input[name=amount]');
      if($input.val() == 0 || isNaN($input.val())) {
        return;
      }
      req.items.push({
        sku_id: $(this).data('id'),
        amount: $input.val(),
      })
    });
    axios.post("{{ route('orders.store') }}", req)
      .then(function (response) {
        Swal.fire('订单提交成功', '', 'success')
        location.href = '/orders/' + response.data.id
      }, function (error) {
        if(error.response.status === 422) {
          var html = '<div>';
          _.each(error.response.data.errors, function (errors) {
            _.each(errors, function (error) {
              html += error+'<br>';
            })
          });
          html += '</div>';
          Swal.fire({
            html: $(html)[0], 
            icon: 'error'
          })
        } else {
          Swal.fire('系统错误', '', 'error');
        }
      });
  })

  $('#btn-check-coupon').click(function(){
    var code = $('input[name=coupon_code]').val();
    if(!code) {
      Swal.fire('请输入优惠码', '', 'warning');
      return;
    }
    
    axios.get('/coupon_codes/' + encodeURIComponent(code))
      .then(function (response) {
        $('#coupon_desc').text(response.data.description);
        $('input[name=coupon_code]').prop('readonly', true);
        $('#btn-cancel-coupon').show();
        $('#btn-check-coupon').hide();
      }, function (error) {
        if(error.response.status === 404) {
          Swal.fire('优惠码不存在', '', 'error');
        } else if(error.response.status === 403) {
          Swal.fire(error.response.data.msg, '', 'error');
        } else {
          Swal.fire('系统内部错误', '', 'error');
        }
      });

    //隐藏 按钮点击事件
    $('#btn-cancel-coupon').click(function(){
      $('#coupon_desc').text('');
      $('input[name=coupon_code]').prop('readonly', false);
      $('#btn-cancel-coupon').hide();
      $('#btn-check-coupon').show();
    });
  });
</script>
@endsection
