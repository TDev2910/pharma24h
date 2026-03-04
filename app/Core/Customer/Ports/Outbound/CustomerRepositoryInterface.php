<?php
namespace App\Core\Customer\Ports\Outbound;

use App\Models\User;
use App\Core\Customer\Domain\CustomerData;

interface CustomerRepositoryInterface
{
    public function getPaginatedCustomers(?string $search, int $perPage);
    public function countTotalCustomers(): int;
    public function countActiveCustomers(): int;
    public function findById(int|string $id): ?User;
    public function create(CustomerData $data): User;
    public function update(User $user, CustomerData $data): bool;
    public function delete(User $user): bool;
}
