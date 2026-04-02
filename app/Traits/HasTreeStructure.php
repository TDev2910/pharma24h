<?php

namespace App\Traits;

trait HasTreeStructure
{
    /**
     * Build flattened tree structure
     *
     * @param iterable $elements
     * @param int|null $parentId
     * @param int $level
     * @return array
     */
    public function buildFlattenedTree($elements, $parentId = null, $level = 0)
    {
        $flat = [];
        foreach ($elements as $element) {
            if ($element->parent_id == $parentId) {
                $element->level = $level;
                $flat[] = $element;
                $children = $this->buildFlattenedTree($elements, $element->id, $level + 1);
                $flat = array_merge($flat, $children);
            }
        }
        return $flat;
    }

    /**
     * Build nested tree structure
     *
     * @param iterable $elements
     * @param int|null $parentId
     * @param array $mapping
     * @return array
     */
    public function buildTreeNodes($elements, $parentId = null, $mapping = ['key' => 'id', 'label' => 'name'])
    {
        $branch = [];
        
        foreach ($elements as $element) {
            if ($element->parent_id == $parentId) {
                $children = $this->buildTreeNodes($elements, $element->id, $mapping);
                
                $node = [
                    'key'   => (string)$element->{$mapping['key']},
                    'label' => $element->{$mapping['label']},
                    'data'  => $element->id,
                    
                    // Thêm sẵn các trường này cho chuẩn chung với cả 2 bên (Post & Product)
                    'id'    => $element->id,
                    'name'  => $element->name,
                    'parent_id' => $element->parent_id,
                ];
                
                // Trường hợp Product Category bắt buộc có mảng children rỗng nếu không có con
                if (!empty($children)) {
                    $node['children'] = $children;
                } else {
                    $node['children'] = [];
                }
                
                $branch[] = $node;
            }
        }
        return $branch;
    }

    /**
     * Build parent-prefixed labels for standard HTML select options.
     */
    public function buildSelectOptions($categories, $parentId = null, $level = 0, $prefix = '– ') {
        $options = [];
        foreach ($categories as $category) {
            if ($category->parent_id == $parentId) {
                // Use 'name' by default, fallback to other common name fields if needed
                $label = isset($category->name) ? $category->name : (isset($category->ten_danh_muc) ? $category->ten_danh_muc : 'Untitled');
                $options[$category->id] = str_repeat($prefix, $level) . $label;
                $options += $this->buildSelectOptions($categories, $category->id, $level + 1, $prefix);
            }
        }
        return $options;
    }

    /**
     * Check if a parent selection exceeds the allowed depth limit.
     * Use this in FormRequests for universal depth validation.
     */
    public function isDepthLimitExceeded($parentId, $maxDepth = 3) {
        if (!$parentId) return false;

        $depth = 0;
        $current = \Illuminate\Support\Facades\DB::table('product_categories')->where('id', $parentId)->first(); // Default fallback
        
        // This is a generic way to check depth by climbing up the parent tree
        while ($current) {
            $depth++;
            if ($depth >= $maxDepth) return true;
            $current = \Illuminate\Support\Facades\DB::table('product_categories')->where('id', $current->parent_id)->first();
        }

        return false;
    }

    /**
     * Check for circular reference in hierarchy.
     */
    public function hasCircularReference($id, $parentId) {
        if (!$parentId) return false;
        if ($id == $parentId) return true;

        $current = \Illuminate\Support\Facades\DB::table('product_categories')->where('id', $parentId)->first();
        while ($current) {
            if ($current->id == $id) return true;
            $current = \Illuminate\Support\Facades\DB::table('product_categories')->where('id', $current->parent_id)->first();
        }

        return false;
    }
}
