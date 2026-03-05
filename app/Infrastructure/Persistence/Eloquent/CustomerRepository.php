<?php
namespace App\Infrastructure\Persistence\Eloquent;

use App\Models\User;
use App\Core\Customer\Ports\Outbound\CustomerRepositoryInterface;
use App\Core\Customer\Domain\DTOs\CustomerData;
use Illuminate\Support\Facades\Storage;

class CustomerRepository implements CustomerRepositoryInterface
{
    public function getPaginatedCustomers(?string $search, int $perPage)
    {
        $query = User::where('role', 'user')
            ->when($search, function ($q, $search) {
                $q->where(function ($subQ) use ($search) {
                    $subQ->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            });

        return $query->withCount('orders')
            ->withSum('orders', 'total_amount')
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function countTotalCustomers(): int {
        return User::where('role', 'user')->count();
    }

    public function countActiveCustomers(): int {
        return User::where('role', 'user')->whereNotNull('email_verified_at')->count();
    }

    public function findById(int|string $id): ?User {
        return User::where('role', 'user')->findOrFail($id);
    }

    public function create(CustomerData $data): User {
        return User::create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => bcrypt($data->password),
            'phone' => $data->phone,
            'address' => $data->address,
            'province' => $data->province,
            'district' => $data->district,
            'ward' => $data->ward,
            'role' => 'user',
        ]);
    }

    public function update(User $user, CustomerData $data): bool {
        $updateData = [
            'name' => $data->name,
            'email' => $data->email,
            'phone' => $data->phone,
            'address' => $data->address,
            'province' => $data->province,
            'district' => $data->district,
            'ward' => $data->ward,
        ];
        
        if ($data->password) {
            $updateData['password'] = bcrypt($data->password);
        }

        return $user->update($updateData);
    }

    public function delete(User $user): bool {
        return $user->delete();
    }
}
