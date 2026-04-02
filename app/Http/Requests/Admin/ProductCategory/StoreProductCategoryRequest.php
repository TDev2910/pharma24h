<?php

namespace App\Http\Requests\Admin\ProductCategory;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\HasTreeStructure;
use App\Models\ProductCategory;

class StoreProductCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Phân quyền nếu cần thiết, tạm để true
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:product_categories,name',
            'parent_id' => [
                'nullable',
                'exists:product_categories,id',
                function ($attribute, $value, $fail) {
                    if ($value) {
                        // Prevent creating too deep hierarchy
                        if ($value) {
                            // Sử dụng Trait để kiểm tra độ sâu thay vì viết vòng lặp thủ công
                            if ((new class {
                                use HasTreeStructure;
                            })->isDepthLimitExceeded($value, 3)) {
                                $fail('Không thể tạo danh mục quá 4 cấp.');
                            }
                        }
                    }
                }
            ],
            'sort_order' => 'nullable|integer|min:0'
        ];
    }
}
