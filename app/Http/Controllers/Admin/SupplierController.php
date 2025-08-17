<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of suppliers
     */
    public function index()
    {
        // Empty collections for now - will be replaced with actual database queries
        $suppliers = collect([]);
        $supplierGroups = collect([]);
        $provinces = collect([]);
        $businessTypes = collect([]);

        return view('admin.products.Nhacungcap.index', compact('suppliers', 'supplierGroups', 'provinces', 'businessTypes'));
    }

    /**
     * Show the form for creating a new supplier
     */
    public function create()
    {

    }

    /**
     * Store a newly created supplier
     */
    public function store(Request $request)
    {
        
    }

    /**
     * Display the specified supplier
     */
    public function show($id)
    {

    }

    /**
     * Show the form for editing the specified supplier
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified supplier
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Remove the specified supplier
     */
    public function destroy($id)
    {

    }
}
