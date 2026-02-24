<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TreeBaseController extends Controller
{
    protected function buildFlattenedTree($elements, $parentId = null, $level = 0)
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

    protected function buildTreeNodes($elements, $parentId = null) 
    {
        $branch = array();
        
        foreach ($elements as $element) {
            if ($element->parent_id == $parentId) {
                $children = $this->buildTreeNodes($elements, $element->id);
                
                $node = [
                    'key' => (string)$element->id,
                    'label' => $element->name,
                    'data' => $element->id
                ];
                
                if ($children) {
                    $node['children'] = $children;
                }
                
                $branch[] = $node;
            }
        }
        return $branch;
    }
}
