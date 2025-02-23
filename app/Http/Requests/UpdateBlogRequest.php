<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class UpdateBlogRequest extends FormRequest
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
            'title' => 'required_without_all:content,tags|min:1|max:50',
            'content' => 'required_without_all:title,tags|min:1|max:16300',
            'tags' => 'required_without_all:content,title|array', // Expecting an array of tags  
            'tags.*' => [
                'string',
                'max:50',
                'distinct',
                'regex:/^[A-Za-z\p{Arabic}\-]+$/iu', // Accept latin letters, arabic letters and dash; also enable case-insensitivity
                'not_regex:/^-+$/',
            ]
        ];
    }
}
