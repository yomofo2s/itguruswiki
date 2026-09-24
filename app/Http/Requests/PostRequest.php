<?php

namespace App\Http\Requests;

use App\Enums\PostType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // route is behind can:access-admin
    }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(PostType::class)],
            'title' => ['required', 'string', 'min:5', 'max:200'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['required', 'string', 'min:10', 'max:100000'],
            'event_starts_at' => ['nullable', 'required_if:type,event', 'date'],
            'event_location' => ['nullable', 'string', 'max:200'],
            'event_url' => ['nullable', 'url:http,https', 'max:500'],
            'published_at' => ['nullable', 'date'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'remove_cover' => ['nullable', 'boolean'],
        ];
    }
}
