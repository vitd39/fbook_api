<?php

namespace App\Http\Requests\Api\Book;

use App\Http\Requests\Api\AbstractRequest;

class SearchRequest extends AbstractRequest
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
        $rules = [
            'search.field' => 'in:' . implode(',', config('model.book.fields')),
            'conditions' => 'array',
            'conditions.*' => 'array',
            'sort.field' => 'in:' . implode(',', array_keys(config('model.sort_field'))),
            'sort.order_by' => 'in:' . implode(',', config('model.sort_type')),
        ];

        return $rules;
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
