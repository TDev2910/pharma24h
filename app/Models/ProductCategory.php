<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    protected $fillable = ['name', 'parent_id'];

    public function parent()  // danh muc cha
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    public function children() //danh muc con
    {
        return $this->hasMany(ProductCategory::class, 'parent_id');
    }

    public static function getHierarchicalCategories()
    {
        return self::whereNull('parent_id')
            ->with('children')
            ->orderBy('name')
            ->get();
    }

    // Lấy tất cả categories cho dropdown 
    public static function getCategoriesForSelect()
    {
        $categories = self::getHierarchicalCategories();
        $result = [];
        
        foreach ($categories as $category) {
            $result[$category->id] = $category->name;
            foreach ($category->children as $child) {
                $result[$child->id] = ' + ' . $child->name;
            }
        }
        
        return $result;
    }

    // Lấy tất cả categories cho dropdown (chỉ nhóm cha)
    public static function getParentCategories()
    {
        return self::whereNull('parent_id')
            ->orderBy('name')
            ->get();
    }
}
