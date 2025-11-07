<?php

namespace App\Http\Controllers\Admin; 

use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends \App\Http\Controllers\Controller
{
    public function dashboard()
    {
        return Inertia::render('Admin/AdminDashboard');
    }
    
}