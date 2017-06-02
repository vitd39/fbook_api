<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

abstract class AbstractRequest extends FormRequest
{
    protected function formatErrors(Validator $validator)
    {
        return [
            'message' => [
                'status' => false,
                'code' => 422,
                'description' => $validator->errors()->all(),
            ]
        ];
    }

    public function all()
    {
        $input = parent::all();

        if (isset($input['search']) && isset($input['search']['field'])) {
            $input['search']['field'] = strtolower($input['search']['field']);
        }

        if (isset($input['sort'])) {
            if (isset($input['sort']['field'])) {
                $input['sort']['field'] = strtolower($input['sort']['field']);
            }

            if (isset($input['sort']['order_by'])) {
                $input['sort']['order_by'] = strtolower($input['sort']['order_by']);
            }
        }

        return $input;
    }
}
