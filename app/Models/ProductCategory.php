<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Product Category Model - Hierarchical Categories with Self-Referencing
 * 
 * Supports unlimited depth category tree structure using Adjacency List Model
 * Implements best practices: caching, type hints, documentation, clean methods
 * 
 * @property int $id
 * @property string $name
 * @property int|null $parent_id
 * @property int $sort_order
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class ProductCategory extends Model
{
    protected $fillable = [
        'name',
        'parent_id',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'parent_id'  => 'integer',
    ];

    /**
     * Get the parent category
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    /**
     * Get all child categories (ordered)
     */
    public function children(): HasMany
    {
        return $this->hasMany(ProductCategory::class, 'parent_id')
            ->orderBy('sort_order')
            ->orderBy('name');
    }

    // ===========================================
    // STATIC QUERY METHODS - OPTIMIZED
    // ===========================================

    /**
     * Get root categories only (for parent selection)
     * Cached for better performance
     * 
     * @return Collection
     */
    public static function getRootCategories(): Collection
    {
        return Cache::remember('categories.roots', 3600, function () {
            return self::whereNull('parent_id')
                ->orderByRaw("CASE 
                    WHEN name = 'Thuốc' THEN 1 
                    WHEN name = 'Hàng hóa' THEN 2 
                    WHEN name = 'Dịch vụ' THEN 3 
                    ELSE 4 
                END")
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get complete category tree with eager loading (up to 4 levels)
     * Optimized with caching and proper ordering
     * 
     * @return Collection
     */
    public static function getCategoryTree(): Collection
    {
        return Cache::remember('categories.tree', 3600, function () {
            return self::whereNull('parent_id')
                ->with([
                    'children' => function ($query) {
                        $query->orderBy('sort_order')
                              ->orderBy('name')
                              ->with([
                                  'children' => function ($subQuery) {
                                      $subQuery->orderBy('sort_order')
                                               ->orderBy('name')
                                               ->with([
                                                   'children' => function ($subSubQuery) {
                                                       $subSubQuery->orderBy('sort_order')
                                                                   ->orderBy('name');
                                                   }
                                               ]);
                                  }
                              ]);
                    }
                ])
                ->orderByRaw("CASE 
                    WHEN name = 'Thuốc' THEN 1 
                    WHEN name = 'Hàng hóa' THEN 2 
                    WHEN name = 'Dịch vụ' THEN 3 
                    ELSE 4 
                END")
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * Get all categories as flat array with hierarchical prefixes
     * RECOMMENDED for dropdowns - unlimited depth with proper formatting
     * 
     * @return array [id => "prefix + name"]
     */
    public static function getAllCategoriesWithDepth(): array
    {
        return Cache::remember('categories.dropdown', 3600, function () {
            $result = [];
            $rootCategories = self::whereNull('parent_id')
                ->orderByRaw("CASE 
                    WHEN name = 'Thuốc' THEN 1 
                    WHEN name = 'Hàng hóa' THEN 2 
                    WHEN name = 'Dịch vụ' THEN 3 
                    ELSE 4 
                END")
                ->orderBy('sort_order') 
                ->orderBy('name')
                ->get();

            foreach ($rootCategories as $root) {
                self::addCategoryWithChildren($root, $result, 0);
            }

            return $result;
        });
    }

    /**
     * Recursive helper to build hierarchical array
     * 
     * @param ProductCategory $category
     * @param array $result
     * @param int $depth
     */
    private static function addCategoryWithChildren($category, &$result, $depth): void
    {
        $prefix = str_repeat(' - ', $depth);
        $result[$category->id] = $prefix . $category->name;

        $children = $category->children()->orderBy('sort_order')->orderBy('name')->get();
        foreach ($children as $child) {
            self::addCategoryWithChildren($child, $result, $depth + 1);
        }
    }

    // ===========================================
    // UTILITY METHODS
    // ===========================================

    /**
     * Check if category is root (has no parent)
     */
    public function isRoot(): bool
    {
        return is_null($this->parent_id);
    }

    /**
     * Check if category is leaf (has no children)
     */
    public function isLeaf(): bool
    {
        return $this->children()->count() === 0;
    }

    /**
     * Get full breadcrumb path (e.g., "Thuốc > Thuốc dị ứng > Thuốc say xe")
     */
    public function getFullPath(): string
    {
        $path = [$this->name];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }

        return implode(' > ', $path);
    }

    /**
     * Get all descendants of this category
     */
    public function getAllDescendants(): Collection
    {
        $descendants = collect();

        foreach ($this->children as $child) {
            $descendants->push($child);
            $descendants = $descendants->merge($child->getAllDescendants());
        }

        return $descendants;
    }

    /**
     * Get depth level of this category (0 = root)
     */
    public function getDepth(): int
    {
        $depth = 0;
        $parent = $this->parent;

        while ($parent) {
            $depth++;
            $parent = $parent->parent;
        }

        return $depth;
    }

    // ===========================================
    // LEGACY METHODS - DEPRECATED (for backward compatibility)
    // ===========================================

    /**
     * @deprecated Use getRootCategories() instead
     */
    public static function getParentCategories(): Collection
    {
        return self::getRootCategories();
    }

    /**
     * @deprecated Use getAllCategoriesWithDepth() instead
     */
    public static function getCategoriesForSelect(): array
    {
        return self::getAllCategoriesWithDepth();
    }

    /**
     * @deprecated Use getAllCategoriesWithDepth() instead
     */
    public static function getFlatCategoriesWithPrefix(): array
    {
        return self::getAllCategoriesWithDepth();
    }

    // ===========================================
    // CACHE MANAGEMENT
    // ===========================================

    /**
     * Clear all category caches
     */
    public static function clearCache(): void
    {
        Cache::forget('categories.roots');
        Cache::forget('categories.tree');
        Cache::forget('categories.dropdown');
    }

    /**
     * Model events - clear cache when categories are modified
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            self::clearCache();
        });

        static::deleted(function () {
            self::clearCache();
        });
    }
}