<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\AbstractRequest;

class LoginRequest extends AbstractRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if (isset($this->all()['refresh_token'])) {
            return [
                'refresh_token' => 'max:100',
            ];
        }
        
        return [
            'email' => 'required|email|max:100',
            'password' => 'required|min:6|max:64',
        ];
    }
}
