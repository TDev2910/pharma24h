<?php
namespace App\Core\Customer\Application\Services;

use App\Core\Customer\Ports\Inbound\CustomerUseCaseInterface;
use App\Core\Customer\Ports\Outbound\CustomerRepositoryInterface;
use App\Core\Customer\Domain\CustomerData;
use Illuminate\Support\Facades\Storage;

class CustomerService implements CustomerUseCaseInterface
{
    public function __construct(
        private CustomerRepositoryInterface $repository
    ) {}

    public function getDashboardData(?string $search, int $perPage): array
    {
        $customers = $this->repository->getPaginatedCustomers($search, $perPage);
        
        $customers->through(function ($customer) {
            return [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'address' => $customer->address,
                'province' => $customer->province,
                'district' => $customer->district,
                'ward' => $customer->ward,
                'avatar_url' => $this->getAvatarUrl($customer->avatar),
                'orders_count' => $customer->orders_count ?? 0,
                'total_amount' => $customer->orders_sum_total_amount ?? 0
            ];
        });

        $stats = [
            'totalCustomers' => $this->repository->countTotalCustomers(),
            'activeCustomers' => $this->repository->countActiveCustomers(),
        ];

        return [
            'customers' => $customers,
            'stats' => $stats
        ];
    }

    public function createCustomer(CustomerData $data)
    {
        return $this->repository->create($data);
    }

    public function updateCustomer(int|string $id, CustomerData $data)
    {
        $user = $this->repository->findById($id);
        return $this->repository->update($user, $data);
    }

    public function deleteCustomer(int|string $id)
    {
        $user = $this->repository->findById($id);
        return $this->repository->delete($user);
    }

    private function getAvatarUrl($avatarPath)
    {
        if (!$avatarPath) return null;
        if (str_starts_with($avatarPath, 'http')) return $avatarPath;

        $path = str_starts_with($avatarPath, 'avatars/') ? $avatarPath : 'avatars/' . $avatarPath;

        if (file_exists(public_path('storage/' . $path))) {
             return url('storage/' . $path); 
        }

        return Storage::url($path);
    }
}
