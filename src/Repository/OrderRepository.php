<?php
declare(strict_types=1);
namespace App\Repository;

use App\Model\Order;

interface OrderRepository
{
    public function save(Order $order): void;
    public function findById(int $id): ?Order;
    public function all(): array;
}