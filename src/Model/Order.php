<?php

declare(strict_types=1);
namespace App\Model;

use App\Enum\OrderStatus;
use InvalidArgumentException;
use App\Exception\InvalidOrderStateException;

final class Order
{
    public function __construct(
        public readonly int $id,
        public readonly User $customer,
        public readonly array $items,
        private OrderStatus $status = OrderStatus::PENDING
    ){
        if($id < 1){
            throw new InvalidArgumentException('Order ID must be a positive integer.');
        }

        if(empty($items)){
            throw new InvalidArgumentException('Order must contain at least one item.');
        }

        foreach($items as $item){
            if(!$item instanceof OrderItem){
                throw new InvalidArgumentException('All items must be instances of OrderItem.');
            }
        }
    }

    public function changeStatus(OrderStatus $newStatus): void
    {
        $isAllowed = match($this->status){
            OrderStatus::PENDING => in_array(
                $newStatus,
                [OrderStatus::PROCESSING, OrderStatus::CANCELLED],
                true
            ),
            OrderStatus::PROCESSING => in_array(
                $newStatus,
                [OrderStatus::COMPLETED, OrderStatus::CANCELLED],
                true
            ),
            OrderStatus::COMPLETED,
            OrderStatus::CANCELLED => false,
        };

        if (!$isAllowed) {
            throw InvalidOrderStateException::forTransition(
                currentStatus: $this->status,
                newStatus: $newStatus
            );
        }

        $this->status = $newStatus;
    }

    public function items(): array
    {
        return $this->items;
    }

    public function status(): OrderStatus
    {
        return $this->status;
    }

    public function total(): Money
    {
        $firstItem = $this->items[0];
        $total = new Money(amountInMinorUnits: 0, currency: $firstItem->unitPrice->currency);
        foreach ($this->items as $item) {
            $total = $total->add($item->lineTotal());
        }
        return $total;
    }
}