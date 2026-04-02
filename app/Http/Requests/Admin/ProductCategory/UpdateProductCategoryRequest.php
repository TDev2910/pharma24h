<?php

namespace App\Http\Requests\Admin\ProductCategory;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\ProductCategory;

class UpdateProductCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true; 
    }

    public function rules()
    {
        // Lấy ID từ URL parameter
        $id = $this->route('product_category') ?: $this->route('id'); 
        
        return [
            'name' => 'required|string|max:255|unique:product_categories,name,' . $id,
            'parent_id' => [
                'nullable',
                'exists:product_categories,id',
                function ($attribute, $value, $fail) use ($id) {
                    if ($value) {
                        // Sử dụng Trait để kiểm tra tham chiếu vòng tròn
                        $trait = new class { use \App\Traits\HasTreeStructure; };
                        if ($trait->hasCircularReference($id, $value)) {
                            $fail('Không thể chọn danh mục này làm cha (gây ra tham chiếu vòng tròn).');
                        }
                    }
                }
            ],
            'sort_order' => 'nullable|integer|min:0'
        ];
    }
}
