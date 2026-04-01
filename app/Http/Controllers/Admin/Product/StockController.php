<?php

namespace App\Http\Controllers\Admin\Product; 

use App\Http\Controllers\Controller;
use App\Models\Medicine;
use App\Models\Goods;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StockController extends Controller
{
    public function index()
    {
        $medicines = Medicine::with(['category', 'manufacturer'])
            ->select('id', 'ma_hang', 'ten_thuoc', 'ton_kho', 'don_vi_tinh', 'gia_ban', 'nhom_hang_id', 'manufacturer_id','ton_thap_nhat', 'ton_cao_nhat')
            ->get()
            ->map(function ($medicine) {
                return [
                    'id' => $medicine->id,
                    'type' => 'medicine',
                    'ma_hang' => $medicine->ma_hang,
                    'ten_san_pham' => $medicine->ten_thuoc,
                    'ton_kho' => $medicine->ton_kho ?? 0,
                    'ton_thap_nhat' => $medicine->ton_thap_nhat ?? 0,
                    'ton_cao_nhat' => $medicine->ton_cao_nhat ?? 0,
                    'don_vi_tinh' => $medicine->don_vi_tinh,
                    'gia_ban' => $medicine->gia_ban,
                    'category' => $medicine->category?->name,
                    'manufacturer' => $medicine->manufacturer?->name,
                ];
            });
        $goods = Goods::with(['category','manufacturer'])
        ->select('id', 'ma_hang', 'ten_hang_hoa', 'ton_kho', 'don_vi_tinh', 'gia_ban', 'nhom_hang_id', 'manufacturer_id','ton_thap_nhat', 'ton_cao_nhat')
        ->get()
        ->map(function ($goods) {
            return [
                'id' => $goods->id,
                'ma_hang' => $goods->ma_hang,
                'ten_san_pham' => $goods->ten_hang_hoa,
                'type' => 'goods',
                'ton_kho' => $goods->ton_kho ?? 0,
                'ton_thap_nhat' => $goods->ton_thap_nhat ?? 0,
                'ton_cao_nhat' => $goods->ton_cao_nhat ?? 0,
                'don_vi_tinh' => $goods->don_vi_tinh,
                'gia_ban' => $goods->gia_ban,
                'category' => $goods->category?->name,
                'manufacturer' => $goods->manufacturer?->name,
            ];
        });
        $products = $medicines->merge($goods)->sortBy('ma_hang')->values();
        return Inertia::render('Admin/Products/Lists/StockProducts', [
            'products' => $products,
        ]);    
    }

    public function apiIndex(Request $request)
    {

    }
}