<?php
declare(strict_types=1);
namespace App\Service;

use App\Enum\OrderStatus;
use App\Model\Order;
use App\Repository\OrderRepository;
use App\Exception\OrderNotFoundException;


final readonly class OrderService
{
    public function __construct(private OrderRepository $orderRepository)
    {
    }

    public function create(Order $order): void
    {
        $this->orderRepository->save($order);
    }

    public function getOrderById(int $orderId): Order
    {
        $order = $this->orderRepository->findById($orderId);

        if ($order === null) {
            throw OrderNotFoundException::forId($orderId);
        }

        return $order;
    }

    public function changeOrderStatus(int $orderId, OrderStatus $newStatus): Order
    {
        $order = $this->orderRepository->findById($orderId);

        if ($order === null) {
            throw OrderNotFoundException::forId($orderId);
        }

        $order->changeStatus($newStatus);
        $this->orderRepository->save($order);
        return $order;
    }
}