<?php

namespace App\Core\Order\Ports\Inbound;

use App\Core\Order\Domain\DTOs\OrderData;

interface OrderUseCaseInterface
{
    public function getAdminDashboardData(array $filters): array;
    
    public function getStaffDashboardData(array $filters): array;
    
    public function getOrderDetails(int $id): array;
    
    public function updateOrderInfo(int $id, OrderData $data);
    
    public function deleteOrder(int $id): bool;
    
    public function getAdminTransportData(array $filters): array;
}
