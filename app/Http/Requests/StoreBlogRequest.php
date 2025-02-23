<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBlogRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()  
    { 
        if ($this->tags) {  
            $this->merge([  
                'tags' => array_map('strtolower', $this->tags),  
            ]);  
        }
    } 

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'min:1',
                'max:50',
            ],
            'content' => [
                'required',
                'min:1',
                'max:16300',
            ],
            'tags' => [
                'sometimes',
                'array', // Expecting an array of tags  
            ],
            'tags.*' => [
                'string',
                'max:50',
                'distinct',
                'regex:/^[A-Za-z\p{Arabic}\s\-]+$/iu', // Accept latin letters, arabic letters and dash; also enable case-insensitivity
                'not_regex:/^-+$/',
            ]  
        ];
    }

}
