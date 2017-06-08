<?php

namespace App\Http\Requests\Api\Book;

use App\Http\Requests\Api\AbstractRequest;

class UpdateRequest extends AbstractRequest
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
        return [
            'title' => 'required|max:255',
            'author' => 'nullable|max:255',
            'publish_date' => 'nullable|date_format:Y-m-d',
            'code' => 'required|max:100',
            'category_id' => 'required|numeric|exists:categories,id',
            'office_id' => 'required|numeric|exists:offices,id',
            'medias' => 'array|max:5',
            'medias.*' => 'array|max:1',
            'medias.*.file' => 'image|mimes:jpeg,jpg,gif,bmp,png|max:10240',
        ];
    }
}
