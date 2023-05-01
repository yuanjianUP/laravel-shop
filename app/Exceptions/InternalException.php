<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
class InternalException extends Exception
{
    //
    protected $msgForUser;
    public function __construct(string $message = "",string $msgForUser = '系统内部错误', int $code = 500)
    {
        parent::__construct($message, $code);
        $this->msgForUser = $msgForUser;
    }
    // 当这个异常被触发时，会调用 render 方法来输出给用户
    public function render(Request $request)
    {
        if ($request->expectsJson()) {
            // code 500 代表的是服务器内部错误
            return response()->json(['msg' => $this->msgForUser], $this->code);
        }
        // 否则返回上一页并带上错误信息
        return view('pages.error', ['msg' => $this->message]);
    }
}
