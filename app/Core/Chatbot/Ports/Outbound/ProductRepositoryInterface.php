<?php

namespace App\Core\Chatbot\Ports\Outbound;

interface ProductRepositoryInterface
{
    /**
     * Tìm kiếm sản phẩm dựa trên tin nhắn người dùng
     * 
     * @param string $query
     * @return array 
     */
    public function searchProducts(string $query): array;
}
