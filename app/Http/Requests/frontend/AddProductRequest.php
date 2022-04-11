<?php

namespace App\Http\Requests\frontend;

use Illuminate\Foundation\Http\FormRequest;

class AddProductRequest extends FormRequest
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
            'id_category' => 'required',
            'id_brand' => 'required',
            'name' => 'required|max:50|min:5',
            'image' => 'required',
            'image.*' => 'image|mimes:jpeg,png,jpg',
            'web_id' => 'required|max:20',
            'price' => 'required|integer',
            'condition' => 'required|max:20',
            'detail' => 'required',
            
        ]; 
    }
    public function messages()
    {
        return [
            
            'id_category.required' => 'Please choose category',
            'id_brand.required' => 'Please choose brand',
            'required'=>'Please enter :attribute',
            'max'=>':attribute cannot more than :max character',
            'min'=>':attribute cannot less than :min character',            
            'integer' =>':attribute only accepts number',
            'image' => 'Image only allow image file',
            'mimes' => 'Image must be a file of type: :values',
            
        ];
    }
}
