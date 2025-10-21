<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $articleId = $this->route('article') ? $this->route('article')->id : null;

        return [
            'title' => 'required|string|max:255|min:10',
            'content' => 'required|string|min:100',
            'excerpt' => 'nullable|string|max:500',
            'category_id' => 'required|exists:categories,id',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
            'tags' => 'nullable|string|max:1000',
            'type' => 'required|in:berita,artikel',
            'is_featured' => 'boolean',
            'is_breaking' => 'boolean',
            'status' => 'required|in:draft,pending_review,published,rejected',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Judul artikel wajib diisi.',
            'title.min' => 'Judul artikel minimal 10 karakter.',
            'title.max' => 'Judul artikel maksimal 255 karakter.',
            'content.required' => 'Konten artikel wajib diisi.',
            'content.min' => 'Konten artikel minimal 100 karakter.',
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'featured_image.image' => 'File yang diupload harus berupa gambar.',
            'featured_image.mimes' => 'Gambar harus berformat JPEG, PNG, JPG, GIF, atau WebP.',
            'featured_image.max' => 'Ukuran gambar maksimal 5MB.',
            'type.required' => 'Tipe konten wajib dipilih.',
            'type.in' => 'Tipe konten harus berita atau artikel.',
            'status.required' => 'Status artikel wajib dipilih.',
            'status.in' => 'Status artikel tidak valid.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title' => 'judul artikel',
            'content' => 'konten artikel',
            'excerpt' => 'ringkasan artikel',
            'category_id' => 'kategori',
            'featured_image' => 'gambar unggulan',
            'tags' => 'tag',
            'type' => 'tipe konten',
            'status' => 'status artikel',
        ];
    }
}
