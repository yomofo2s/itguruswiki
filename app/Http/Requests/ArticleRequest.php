<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // checked with policies in the controllers
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:5', 'max:200'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string', 'min:30', 'max:100000'],
            'source_url' => ['nullable', 'url:http,https', 'max:500'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048', 'dimensions:max_width=4000,max_height=4000'],
            'remove_cover' => ['nullable', 'boolean'],
            'action' => ['nullable', 'in:draft,submit,publish'],
        ];
    }

    public function attributes(): array
    {
        return ['category_id' => 'topic', 'body' => 'content', 'source_url' => 'source link'];
    }
}
