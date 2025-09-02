<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Hiển thị trang cơ sở khám bệnh
     */
    public function cosokhambenh()
    {
        return view('user.cosokhambenh');
    }       

    /**
     * Hiển thị trang sản phẩm
     */
    public function products()
    {
        return view('user.products');
    }

    /**
     * Hiển thị trang giỏ hàng
     */
    public function cart()
    {
        return view('user.cart');
    }

    /**
     * Hiển thị trang profile của user (cần đăng nhập)
     */
    public function profile()
    {
        $user = Auth::user();
        return view('user.profile.user-profile', compact('user'));
    }
}