@extends('layouts.app')
@section('title', $product->title)

@section('content')
<div class="row">
      <div class="col-lg-10 offset-lg-1">
            <div class="card">
                  <div class="card-body product-info">
                        <div class="row">
                              <div class="col-5">
                                    <img class="cover" src="{{ $product->image_url }}" alt="">
                              </div>
                              <div class="col-7">
                                    <div class="title">{{ $product->title }}</div>
                                    <div class="price"><label>价格</label><em>￥</em><span>{{ $product->price }}</span></div>
                                    <div class="sales_and_reviews">
                                          <div class="sold_count">累计销量 <span class="count">{{ $product->sold_count }}</span></div>
                                          <div class="review_count">累计评价 <span class="count">{{ $product->review_count }}</span></div>
                                          <div class="rating" title="评分 {{ $product->rating }}">评分 <span class="count">{{ str_repeat('★', floor($product->rating)) }}{{ str_repeat('☆', 5 - floor($product->rating)) }}</span></div>
                                    </div>
                                    <div class="skus">
                                          <label>选择</label>
                                          <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                @foreach($product->skus as $sku)
                                                <label class="btn sku-btn {{ $loop->first ? 'active' : '' }}" data-price="{{ $sku->price }}" data-stock="{{ $sku->stock }}" data-toggle="tooltip" title="{{ $sku->description }}" data-placement="bottom">
                                                      <input type="radio" name="skus" autocomplete="off" value="{{ $sku->id }}"> {{ $sku->title }}
                                                </label>
                                                @endforeach
                                          </div>
                                    </div>
                                    <div class="cart_amount"><label>数量</label><input name="amount" type="text" class="form-control form-control-sm" value="1"><span>件</span><span class="stock"></span></div>
                                    <div class="buttons">
                                          <button class="btn btn-success btn-favor">❤ 收藏</button>
                                          <button class="btn btn-primary btn-add-to-cart">加入购物车</button>
                                    </div>
                              </div>
                        </div>
                        <div class="product-detail">
                              <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                          <a class="nav-link active" href="#product-detail-tab" aria-controls="product-detail-tab" role="tab" data-toggle="tab" aria-selected="true">商品详情</a>
                                    </li>
                                    <li class="nav-item">
                                          <a class="nav-link" href="#product-reviews-tab" aria-controls="product-reviews-tab" role="tab" data-toggle="tab" aria-selected="false">用户评价</a>
                                    </li>
                              </ul>
                              <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane active" id="product-detail-tab">
                                          {!! $product->description !!}
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="product-reviews-tab">
                                    </div>
                              </div>
                        </div>
                  </div>
            </div>
      </div>
</div>
@endsection

@section('scriptsAfterJs')
<script>
      $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip({
                  trigger: 'hover'
            });
            $('.sku-btn').click(function() {
                  $('.product-info .price span').text($(this).data('price'));
                  $('.product-info .stock').text('库存：' + $(this).data('stock') + '件');
            });
            $('.btn-add-to-cart').click(function() {
                  //发起ajax请求
                  axios.post("{{ route('cart.add') }}", {
                              sku_id: $('label.active input[name=skus]').val(),
                              amount: $('input[name=amount]').val()
                        })
                        .then(function(response) {
                              Swal.fire('加入购物车成功', '', 'success');
                        },function(error) {
                              if (error.response.status == 401) {
                                    Swal.fire('请先登录', '', 'error');
                              } else if (error.response.status == 422) {
                                    // http 状态码为 422 代表用户输入校验失败
                                    var html = '<div>';
                                    _.each(error.response.data.errors, function(errors) {
                                          _.each(errors, function(error) {
                                                html += error + '<br>';
                                          })
                                    });
                                    html += '</div>';
                                    Swal.fire({
                                          html: $(html)[0],
                                          icon: 'error'
                                    })
                              }else{
                                    Swal.fire('系统错误', '', 'error');
                              }
                        });
            })
      });
</script>
@endsection