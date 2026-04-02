<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use \App\Traits\HasTreeStructure;

    protected $fillable = [
        'name',
        'parent_id',
        'sort_order',
    ];

    public function parent()
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(ProductCategory::class, 'parent_id')
            ->orderBy('sort_order')
            ->orderBy('name');
    }

    /**
     * Lấy toàn bộ danh mục theo cấu trúc cây (Nested Tree)
     */
    public static function getAllCategoriesWithDepth()
    {
        return self::orderBy('sort_order')->orderBy('name')->get();
    }
}