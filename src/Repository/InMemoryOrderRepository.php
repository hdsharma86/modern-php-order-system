<?php

declare(strict_types=1);
namespace App\Repository;
use App\Model\Order;

final class InMemoryOrderRepository implements OrderRepository
{
    private array $orders = [];

    public function save(Order $order): void
    {
        $this->orders[$order->id] = $order;
    }

    public function findById(int $id): ?Order
    {
        return $this->orders[$id] ?? null;
    }

    public function all(): array
    {
        return array_values($this->orders);
    }
}