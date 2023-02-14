<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    //
    public function store(UserRequest $request)
    {
        $credentials['name'] = $request->name;
        $credentials['password'] = $request->password;
        if(!$token = Auth::guard('api')->attempt($credentials)){
            throw new AuthenticationException('用户名或密码错误');
        }
        return response()->json([
            'access_token' => $token,
            'token_type' => "Bearer",
            'expires_in' => Auth::guard('api')->factory()->getTTL()*60*60*24
        ])->setStatusCode(201);
    }
}
