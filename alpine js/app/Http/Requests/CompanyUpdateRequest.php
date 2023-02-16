<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rules\File;
use Illuminate\Foundation\Http\FormRequest;

class CompanyUpdateRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'image' => ['nullable', File::image()->max(2048)],
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'Name cannot be blank!',
            'image.required' => 'Image cannot be blank!',
        ]; 
    }
}