<?php

declare(strict_types=1);
namespace App\Model;

use App\Enum\OrderStatus;
use InvalidArgumentException;
use App\Exception\InvalidOrderStateException;
use App\Contract\Loggable;
use App\Collection\OrderItemCollection;
use App\Attribute\AuditEvent;

final class Order implements Loggable
{
    #[AuditEvent(eventName: 'order_created')]
    public function __construct(
        public readonly int $id,
        public readonly User $customer,
        public readonly OrderItemCollection $items,
        private OrderStatus $status = OrderStatus::PENDING
    ){
        if($id < 1){
            throw new InvalidArgumentException('Order ID must be a positive integer.');
        }

        if($items->isEmpty()){
            throw new InvalidArgumentException('Order must contain at least one item.');
        }

        foreach($items->all() as $item){
            if(!$item instanceof OrderItem){
                throw new InvalidArgumentException('All items must be instances of OrderItem.');
            }
        }
    }

    #[AuditEvent(eventName: 'order_status_changed')]
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
        return $this->items->all();
    }

    public function status(): OrderStatus
    {
        return $this->status;
    }

    public function total(): Money
    {
        return $this->items->total();
    }

    #[AuditEvent(eventName: 'order_to_log_context')]
    public function toLogContext(): array
    {
        return [
            'order_id' => $this->id,
            'customer_id' => $this->customer->id,
            'status' => $this->status->value,
            'total_amount' => $this->total()->amountInMinorUnits,
            'currency' => $this->total()->currency,
            'items' => array_map(fn($item) => [
                'product_id' => $item->productId,
                'product_name' => $item->productName,
                'unit_price' => $item->unitPrice->amountInMinorUnits,
                'quantity' => $item->quantity,
                'line_total' => $item->lineTotal()->amountInMinorUnits
            ], $this->items->all())
        ];
    }
}