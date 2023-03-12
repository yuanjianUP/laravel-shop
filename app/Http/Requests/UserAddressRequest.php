<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserAddressRequest extends Request
{
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
            'province' => 'required',
            'city' => 'required',
            'district' => 'required',
            'address' => 'required|min:5',
            'zip' => 'required',
            'contact_name' => 'required',
            'contact_phone' => 'required',
        ];
    }
}
