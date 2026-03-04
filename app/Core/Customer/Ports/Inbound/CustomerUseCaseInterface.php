<?php
namespace App\Core\Customer\Ports\Inbound;

use App\Core\Customer\Domain\CustomerData;

interface CustomerUseCaseInterface
{
    public function getDashboardData(?string $search, int $perPage): array;
    public function createCustomer(CustomerData $data);
    public function updateCustomer(int|string $id, CustomerData $data);
    public function deleteCustomer(int|string $id);
}
