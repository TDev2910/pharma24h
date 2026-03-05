<?php

namespace App\Core\Order\Ports\Outbound;

interface OrderRepositoryInterface
{
    public function countTotal(): int;
    
    public function countPending(): int;
    
    public function countCompleted(): int;
    
    public function getAdminPaginatedOrders(array $filters, int $perPage = 10);
    
    public function getStaffPaginatedOrders(array $filters, int $perPage = 10);
    
    public function getAdminTransportOrders(array $filters, int $perPage = 20);
    
    public function findById(int $id);
    
    public function update(int $id, array $data): bool;
    
    public function delete(int $id): bool;
}
