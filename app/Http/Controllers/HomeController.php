<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Hiển thị trang chủ công cộng
     */
    public function index()
    {
        return view('public.home');
    }

    /**
     * Hiển thị trang cơ sở khám bệnh
     */
    public function cosokhambenh()
    {
        return view('public.cosokhambenh');
    }

    /**
     * Hiển thị trang sản phẩm công cộng
     */
    public function products()
    {
        return view('public.products');
    }

    /**
     * Hiển thị trang giới thiệu
     */
    public function about()
    {
        return view('public.about');
    }

    /**
     * Hiển thị trang liên hệ
     */
    public function contact()
    {
        return view('public.contact');
    }
}
