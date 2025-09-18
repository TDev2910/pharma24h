<?php

namespace App\Http\Controllers\Admin\Customer;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = User::where('role','user')
            ->select('id', 'avatar', 'name', 'email', 'phone', 'address', 'created_at')
            ->paginate(10);
        //tính tổng số khách hàng có trong hệ thống
        $totalCustomers = User::where('role','user')->count();
        $newCustomers = User::where('role','user')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)  
            ->count();
        return view('admin.customer.index', compact('customers', 'totalCustomers', 'newCustomers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:255',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone' => $request->phone,
            'address' => $request->address,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
