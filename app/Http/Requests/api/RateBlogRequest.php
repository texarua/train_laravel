<?php

namespace App\Http\Requests\api;

use App\Http\Requests\api\FormRequest;
class RateBlogRequest extends FormRequest
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
            'blog_id' => 'required',
            'user_id'=>'required',
            'rate' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'required'=>':attribute Không được để trống'
        ];
    }
}
