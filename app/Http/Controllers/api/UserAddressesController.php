<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserAddressesController extends Controller
{
    //
    public function list(Request $request)
    {

        return response()->json([
            'addresses' => $request->user()->addresses,
        ]);
    }
}
