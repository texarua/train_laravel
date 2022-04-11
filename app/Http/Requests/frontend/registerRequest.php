<?php

namespace App\Http\Requests\frontend;

use Illuminate\Foundation\Http\FormRequest;

class registerRequest extends FormRequest
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
            'name'=>'required|max:191',
            'password'=>'required',
            'email' => 'required|email|unique:users',
            // 'address'=>'required',
            'country'=>'required',
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    public function messages()
    {
        return [
            'required'=>':attribute :Không được để trống',
            'max'=>':attribute :Không được quá :max ký tự',
            'email.email' => ':attribute :email sai định dạng',
            'email.unique' => ':attribute :email da ton tai',
            'avatar' => ':attribute : Hinh anh upload len phai la hình ảnh',
            'mimes' => ':attribute : Hinh anh upload len phai dinh dang như sau:jpeg,png,jpg,gif',
            'avatar.max' => ':attribute : Hinh anh upload len vuot qua kich thuoc cho phep :max'
        ];
    }
}
