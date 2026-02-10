<?php

namespace App\Http\Requests\Staff\Post;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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
        return [
            'title' => 'required|string|max:255|unique:posts,title',
            'summary' => 'required|string|max:500',
            'content' => 'required|string',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'is_published' => 'required|boolean',
        ];
    }
    public function messages(): array
    {
        return [
            'title.required'     => 'Vui lòng nhập tiêu đề bài viết.',
            'title.unique'       => 'Tiêu đề này đã tồn tại.',
            'thumbnail.required' => 'Bạn cần chọn ảnh đại diện cho bài viết.',
            'thumbnail.max'      => 'Dung lượng ảnh không được quá 3MB.',
        ];
    }
}
