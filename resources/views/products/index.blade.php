@extends('layouts.app')
@section('title', '商品列表')

@section('content')
<div class="row">
  <div class="col-lg-10 offset-lg-1">
    <div class="card">
      <div class="card-body">
        <form action="{{ route('products.index') }}" class="search-form">
          <div class="form-row">
            <div class="col-md-9">
              <div class="form-row">
                <div class="col-auto"><input type="text" class="form-control form-control-sm" name="search" placeholder="搜索"></div>
                <div class="col-auto"><button class="btn btn-primary btn-sm">搜索</button></div>
              </div>
            </div>
            <div class="col-md-3">
              <select name="order" class="form-control form-control-sm float-right">
                <option value="">排序方式</option>
                <option value="price_asc">价格从低到高</option>
                <option value="price_desc">价格从高到低</option>
                <option value="sold_count_desc">销量从高到低</option>
                <option value="sold_count_asc">销量从低到高</option>
                <option value="rating_desc">评价从高到低</option>
                <option value="rating_asc">评价从低到高</option>
              </select>
            </div>
          </div>
        </form>
        <div class="row products-list">
          @foreach($products as $product)
          <div class="col-3 product-item">
            <div class="product-content">
              <div class="top">
                <div class="img"><img src="{{ $product->image_url }}" alt=""></div>
                <div class="price"><b>￥</b>{{ $product->price }}</div>
                <div class="title"><a href="{{ route('products.show',['product'=>$product->id]) }}">{{ $product->title }}</a></div>
              </div>
              <div class="bottom">
                <div class="sold_count">销量 <span>{{ $product->sold_count }}笔</span></div>
                <div class="review_count">评价 <span>{{ $product->review_count }}</span></div>
              </div>
            </div>
          </div>
          @endforeach
        </div>
        <div class="float-right">{{ $products->render() }}</div> <!-- 只需要添加这一行 -->
      </div>
    </div>
  </div>
</div>
@endsection
@section('scriptsAfterJs')
<script>
  $(document).ready(function () {
    // 监听搜索框的 input 事件，当用户输入时触发
    $('.search-form input[name=search]').on('input', function () {
      // 获取搜索框的值
      var value = $(this).val();
      // 调用 Laravel 的方法发起一个 get 请求，请求的 url 为当前 url，参数为 {search: value}
      axios.get('{{ route('products.index') }}', {
        params: {
          search: value
        }
      })
      .then(function (response) {
        // 请求成功会执行这个回调
        // console.log(response);
        // 将搜索结果渲染到模板中
        $('.products-list').html(response.data);
      }, function (error) {
        // 请求失败会执行这个回调
        console.log(error);
      })
    });

    // 监听排序 select
    $('.search-form select[name=order]').on('change', function () {
      // 获取排序方式的值
      var value = $(this).val();
      // 调用 Laravel 的方法发起一个 get 请求，请求的 url 为当前 url，参数为 {order: value}
      axios.get('{{ route('products.index') }}', {
        params: {
          order: value
        }
      })
      .then(function (response) {
        // 请求成功会执行这个回调
        // console.log(response);
        // 将排序结果渲染到模板中
        $('.products-list').html(response.data);
      }, function (error) {
        // 请求失败会执行这个回调
        console.log(error);
      })
    });
  });
@endsection