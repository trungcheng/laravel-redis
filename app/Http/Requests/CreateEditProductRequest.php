<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class CreateEditProductRequest extends Request
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
            'proName' => 'required|min:1',
            'proDesc' => 'required|min:1',
            'proPrice' => 'required|numeric|min:2',
            'proImage' => 'mimes:jpeg,png|image|max:5120'
        ];
    }

    public function response(array $error)
    {
        return response()->json(['msg' => $error], 422);
    }

    public function messages()
    {
        return [
            'proName.required' => 'The product name is required',
            'proName.min' => 'The product name length at least :min characters',
            'proDesc.required'  => 'The product description is required',
            'proDesc.min'  => 'The product description at least :min characters',
            'proPrice.required'  => 'The product price is required',
            'proPrice.numeric'  => 'The product price must be an digit',
            'proImage.mimes'  => 'Please upload the file with the extension jpg,jpeg,png,gif',
            'proImage.max'  => 'Please upload files within 5MB.',
        ];
    }
}
