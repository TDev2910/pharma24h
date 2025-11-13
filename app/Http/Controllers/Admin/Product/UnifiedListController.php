<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UnifiedListController extends Controller
{
    /**
     * Display unified list page (initially empty, user selects type from dropdown)
     */
    public function index()
    {
        return Inertia::render('Admin/Products/Lists/UnifiedList', [
            'productType' => null, // No type selected initially
            'medicines' => [],
            'goods' => [],
            'services' => [],
            'categories' => [],
            'data' => []
        ]);
    }
}

