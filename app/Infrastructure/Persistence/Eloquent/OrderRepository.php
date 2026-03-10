<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Core\Order\Ports\Outbound\OrderRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Models\Order;

class OrderRepository implements OrderRepositoryInterface
{
    public function countTotal(): int
    {
        return Order::count();
    }

    public function countPending(): int
    {
        return Order::whereIn('order_status', ['new', 'pending'])->count();
    }

    public function countCompleted(): int
    {
        return Order::where('order_status', 'completed')->count();
    }

    public function getAdminPaginatedOrders(array $filters, int $perPage = 10)
    {
        $query = Order::with(['user', 'items'])->latest();

        if (!empty($filters['search'])) {
            $term = $filters['search'];
            $query->where(function ($q) use ($term) {
                $q->where('order_code', 'like', "%{$term}%")
                  ->orWhere('customer_name', 'like', "%{$term}%")
                  ->orWhere('customer_phone', 'like', "%{$term}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('order_status', $filters['status']);
        }

        if (!empty($filters['from_date']) && !empty($filters['to_date'])) {
            $query->whereBetween('created_at', [$filters['from_date'], $filters['to_date']]);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function getStaffPaginatedOrders(array $filters, int $perPage = 10)
    {
        $query = Order::with(['user', 'items'])->latest();

        if (!empty($filters['search'])) {
            $term = $filters['search'];
            $query->where(function ($q) use ($term) {
                $q->where('order_code', 'like', "%{$term}%")
                  ->orWhere('customer_name', 'like', "%{$term}%")
                  ->orWhere('customer_phone', 'like', "%{$term}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('order_status', $filters['status']);
        }

        if (!empty($filters['from_date']) && !empty($filters['to_date'])) {
            $query->whereBetween('created_at', [$filters['from_date'], $filters['to_date']]);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function getAdminTransportOrders(array $filters, int $perPage = 20)
    {
        $filterGroups = [
            'delivering'     => ['ready_to_pick', 'picking', 'storing', 'transporting', 'delivering'],
            'completed'      => ['delivered'],
            'cancelled'      => ['cancel', 'return', 'returned', 'damage', 'lost'],
            'pending_pickup' => ['ready_to_pick'],
        ];

        $query = Order::query()
            ->where('delivery_method', 'shipping')
            ->whereNotNull('ghn_order_code');

        if (!empty($filters['status'])) {
            $inputStatuses = is_array($filters['status']) ? $filters['status'] : [$filters['status']];
            $dbStatuses = [];
            foreach ($inputStatuses as $status) {
                if (isset($filterGroups[$status])) {
                    $dbStatuses = array_merge($dbStatuses, $filterGroups[$status]);
                } else {
                    $dbStatuses[] = $status;
                }
            }
            $query->whereIn('ghn_status', array_unique($dbStatuses));
        }

        if (!empty($filters['partner']) && $filters['partner'] === 'ghn') {
            $query->whereNotNull('ghn_order_code');
        }

        if (!empty($filters['cod']) && $filters['cod'] !== 'all') {
            if ($filters['cod'] === 'yes') {
                $query->where('ghn_cod_amount', '>', 0);
            } else {
                $query->where(function ($subQ) {
                    $subQ->whereNull('ghn_cod_amount')->orWhere('ghn_cod_amount', 0);
                });
            }
        }

        if (!empty($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function ($subQ) use ($searchTerm) {
                $subQ->where('ghn_order_code', 'like', "%{$searchTerm}%")
                     ->orWhere('order_code', 'like', "%{$searchTerm}%")
                     ->orWhere('customer_name', 'like', "%{$searchTerm}%")
                     ->orWhere('customer_phone', 'like', "%{$searchTerm}%");
            });
        }

        return $query->latest('ghn_created_at')->paginate($perPage)->withQueryString();
    }

    public function findById(int $id)
    {
        return Order::findOrFail($id);
    }

    public function findByIdWithRelations(int $id, array $relations = [])
    {
        return Order::with($relations)->findOrFail($id);
    }

    public function update(int $id, array $data): bool
    {
        $order = Order::findOrFail($id);
        $order->fill($data);
        return $order->save();
    }
    
    public function updateStatus(int $id, string $status, ?string $note): bool
    {
        return DB::transaction(function () use ($id, $status, $note) {
            $order = Order::findOrFail($id);
            $order->order_status = $status;
            
            if ($status === 'completed') {
                $order->payment_status = 'paid';
            }
            
            if (!empty($note)) {
                $order->note = $note;
            }
            
            return $order->save();
        });
    }

    public function updateCancellationStatus(int $id, string $orderStatus, string $paymentStatus, string $cancellationStatus, ?string $cancellationNote = null): bool
    {
        return DB::transaction(function () use ($id, $orderStatus, $paymentStatus, $cancellationStatus, $cancellationNote) {
            $order = Order::findOrFail($id);
            $order->order_status = $orderStatus;
            $order->payment_status = $paymentStatus;
            $order->cancellation_status = $cancellationStatus;
            
            if (!empty($cancellationNote)) {
                $order->cancellation_note = $cancellationNote;
            }
            
            return $order->save();
        });
    }

    public function delete(int $id): bool
    {
        $order = Order::findOrFail($id);
        return $order->delete();
    }
}
