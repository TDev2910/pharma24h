<?php

namespace App\Core\Order\Application\Services;

use App\Core\Order\Ports\Inbound\OrderUseCaseInterface;
use App\Core\Order\Ports\Outbound\OrderRepositoryInterface;
use App\Core\Order\Domain\DTOs\OrderData;
use App\Services\CheckoutService;

class OrderService implements OrderUseCaseInterface
{
    public function __construct(
        protected OrderRepositoryInterface $repository,
        protected CheckoutService $checkoutService
    ) {}

    public function getAdminDashboardData(array $filters): array
    {
        $orders = $this->repository->getAdminPaginatedOrders($filters, 10);
        
        $orders->through(function ($order) {
            return [
                'id'             => $order->id,
                'order_code'     => $order->order_code,
                'customer_name'  => $order->customer_name ?? 'Khách lẻ',
                'customer_phone' => $order->customer_phone ?? '',
                'total_amount'   => $order->total_amount,
                'order_status'   => $order->order_status,
                'payment_status' => $order->payment_status,
                'created_at'     => $order->created_at ? $order->created_at->format('d/m/Y H:i') : '',
                'customer_email'   => $order->customer_email,
                'payment_method'   => $order->payment_method,
                'delivery_method'  => $order->delivery_method,
                'shipping_address' => $order->shipping_address,
                'province'         => $order->province,
                'district'         => $order->district,
                'ward'             => $order->ward,
                'pickup_location'  => $order->pickup_location,
                'note'             => $order->note,
                'items'            => $order->items,
                'shipping_code'  => $order->shipping_code,
                'ghn_order_code' => $order->ghn_order_code,
                'district_id'    => $order->district_id,
                'ward_code'      => $order->ward_code,
            ];
        });

        return [
            'stats' => [
                'total' => $this->repository->countTotal(),
                'pending' => $this->repository->countPending(),
                'completed' => $this->repository->countCompleted()
            ],
            'orders' => $orders,
        ];
    }

    public function getStaffDashboardData(array $filters): array
    {
        $orders = $this->repository->getStaffPaginatedOrders($filters, 10);
        
        $orders->through(function ($order) {
            return [
                'id'             => $order->id,
                'order_code'     => $order->order_code,
                'customer_name'  => $order->customer_name ?? 'Khách lẻ',
                'customer_phone' => $order->customer_phone ?? '',
                'total_amount'   => $order->total_amount,
                'order_status'   => $order->order_status,
                'payment_status' => $order->payment_status,
                'created_at'     => $order->created_at ? $order->created_at->format('d/m/Y H:i') : '',
                'customer_email'   => $order->customer_email,
                'payment_method'   => $order->payment_method,
                'delivery_method'  => $order->delivery_method,
                'shipping_address' => $order->shipping_address,
                'province'         => $order->province,
                'district'         => $order->district,
                'ward'             => $order->ward,
                'pickup_location'  => $order->pickup_location,
                'note'             => $order->note,
                'items'            => $order->items,
                'shipping_code'  => $order->shipping_code,
                'ghn_order_code' => $order->ghn_order_code,
                'district_id'    => $order->district_id,
                'ward_code'      => $order->ward_code,
            ];
        });

        return [
            'stats' => [
                'total' => $this->repository->countTotal(),
                'pending' => $this->repository->countPending(),
                'completed' => $this->repository->countCompleted()
            ],
            'orders' => $orders,
        ];
    }

    public function getOrderDetails(int $id): array
    {
        $order = $this->repository->findById($id);
        
        return [
            'order' => $order,
            'items' => $order->items,
        ];
    }

    public function updateOrderInfo(int $id, OrderData $data)
    {
        return $this->repository->update($id, $data->toArray());
    }

    public function deleteOrder(int $id): bool
    {
        return $this->repository->delete($id);
    }
    
    public function updateOrderStatus(int $id, string $status, ?string $note): void
    {
        $this->repository->updateStatus($id, $status, $note);
    }
    
    public function approveCancellation(int $id): void
    {
        $this->repository->updateCancellationStatus($id, 'cancelled', 'cancelled', 'approved');
    }
    
    public function rejectCancellation(int $id, string $note): void
    {
        $this->repository->updateCancellationStatus($id, 'delivering', 'pending', 'rejected', $note);
    }
    
    public function markCompleted(int $id): object
    {
        return $this->checkoutService->completeOrder($id);
    }
    
    public function getOrderForInvoice(int $id): object
    {
        return $this->repository->findByIdWithRelations($id, ['items.item', 'user']);
    }
    
    public function getAdminTransportData(array $filters): array
    {
        $displayMap = [
            'ready_to_pick' => 'delivering',
            'picking'       => 'delivering',
            'storing'       => 'delivering',
            'transporting'  => 'delivering',
            'delivering'    => 'delivering',
            'delivered'     => 'completed',
            'return'        => 'cancelled',
            'returned'      => 'cancelled',
            'cancel'        => 'cancelled',
            'damage'        => 'cancelled',
            'lost'          => 'cancelled',
        ];

        $orders = $this->repository->getAdminTransportOrders($filters, 20);
        
        $orders->through(function ($order) use ($displayMap) {
            $displayStatus = $displayMap[$order->ghn_status] ?? 'delivering';
            $createdDate = $order->ghn_created_at ?? $order->created_at;
            $deliveryDate = $order->ghn_expected_delivery_time;

            return [
                'id'                   => $order->id,
                'code'                 => $order->ghn_order_code,
                'order_code'           => $order->order_code,
                'created_at_formatted' => $createdDate?->format('d/m/Y H:i'),
                'customer_name'        => $order->customer_name,
                'customer_phone'       => $order->customer_phone,
                'partner'              => 'ghn',
                'status'               => $displayStatus,
                'original_status'      => $order->ghn_status,
                'cod_amount'           => $order->ghn_cod_amount ?? 0,
                'delivery_time'        => $deliveryDate?->format('d/m/Y H:i'),
                'shipper_name'         => $order->ghn_shipper_name,
                'shipper_phone'        => $order->ghn_shipper_phone,
                'tracking_url'         => $order->ghn_tracking_url,
            ];
        });

        return [
            'orders' => $orders
        ];
    }
}
