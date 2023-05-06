<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class CouponCodeUnavailableException extends Exception
{
    public function __construct($message,int $code = 403)
    {
        parent::__construct($message, $code);
    }

    public function render(Request $request)
    {
        if($request->expectsJson()){
            return response()->json(['message' => $this->message], $this->code);
        }
        return redirect()->back()->withErrors(['message' => $this->message]);
    }
}
