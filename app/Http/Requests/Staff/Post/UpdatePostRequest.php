<?php

namespace App\Http\Requests\Staff\Post;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
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
        $postId = $this->route('posts') ? $this->route('posts')->id : null;
        return [
            'title' => 'required|string|max:255|unique:posts,title,' . $postId,
            'summary' => 'required|string|max:500',
            'content' => 'required|string',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif|max:3072',
            'is_published' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề không được để trống.',
            'title.unique'   => 'Tiêu đề này đã được sử dụng bài viết khác.',
        ];
    }
}
