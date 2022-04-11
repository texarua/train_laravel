<?php

namespace App\Http\Requests\api;

use App\Http\Requests\api\FormRequest;

class CommentRequest extends FormRequest
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
            'id_blog' => 'required',
            'id_user'=>'required',
            'name_user' => 'required',
            'id_comment' => 'required',
            'comment' => 'required',
            'image_user' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'id_blog.required'=>':attribute Không được để trống',
            'id_user.required'=>':attribute Không được để trống',
            'name_user.required'=>':attribute Không được để trống',
            'id_comment.required'=>':attribute Không được để trống',
            'comment.required'=>':attribute Không được để trống',
            'image_user.required'=>':attribute Không được để trống',
        ];
    }
}
