<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];

        if ($this->isMethod('post')) {
            $rules['slug'] = ['required', 'string', 'max:255', 'unique:categories,slug'];
        } else {
            $rules['slug'] = [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'slug')->ignore($this->route('category'))
            ];
        }

        return $rules;
    }
}