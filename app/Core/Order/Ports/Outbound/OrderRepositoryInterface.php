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
    
    public function findByIdWithRelations(int $id, array $relations = []);
    
    public function update(int $id, array $data): bool;
    
    public function updateStatus(int $id, string $status, ?string $note): bool;
    
    public function updateCancellationStatus(int $id, string $orderStatus, string $paymentStatus, string $cancellationStatus, ?string $cancellationNote = null): bool;
    
    public function delete(int $id): bool;
}
