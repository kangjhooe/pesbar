<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9_-]+$/',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'bio' => ['nullable', 'string', 'max:1000'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'website' => ['nullable', 'url', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'social_links' => ['nullable', 'array'],
            'social_links.facebook' => ['nullable', 'url', 'max:255'],
            'social_links.twitter' => ['nullable', 'url', 'max:255'],
            'social_links.instagram' => ['nullable', 'url', 'max:255'],
            'social_links.linkedin' => ['nullable', 'url', 'max:255'],
        ];
    }
}
