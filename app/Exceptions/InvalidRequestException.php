<?php

namespace App\Exceptions;


use Exception;
use Illuminate\Http\Request;

class InvalidRequestException extends Exception
{
    //
    public function __construct(string $message = "", int $code = 400)
    {
        parent::__construct($message, $code);
    }

    // 当这个异常被触发时，会调用 render 方法来输出给用户
    public function render(Request $request)
    {
        // 如果是 AJAX 请求，则返回 JSON 格式的数据
        if ($request->expectsJson()) {
            // code 400 代表的是请求参数有误
            return response()->json(['msg' => $this->message], $this->code);
        }
        // 否则返回上一页并带上错误信息
        return view('pages.error', ['msg' => $this->message]);
    }
}
