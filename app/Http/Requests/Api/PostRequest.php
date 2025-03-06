<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'excerpt' => ['nullable', 'string'],
            'is_published' => ['sometimes', 'boolean'],
            'published_at' => ['nullable', 'date'],
        ];

        if ($this->isMethod('post')) {
            $rules['slug'] = ['required', 'string', 'max:255', 'unique:posts,slug'];
        } else {
            $rules['slug'] = [
                'required',
                'string',
                'max:255',
                Rule::unique('posts', 'slug')->ignore($this->route('post'))
            ];
        }

        return $rules;
    }
}