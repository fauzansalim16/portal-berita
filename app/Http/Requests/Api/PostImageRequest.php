<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class PostImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [];

        if ($this->routeIs('*.featured-image.*')) {
            $rules['image'] = ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'];
        }

        if ($this->routeIs('*.gallery.*')) {
            $rules['images'] = ['required', 'array'];
            $rules['images.*'] = ['image', 'mimes:jpeg,png,jpg,gif', 'max:2048'];
        }

        return $rules;
    }
}