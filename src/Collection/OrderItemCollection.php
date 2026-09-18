<?php

declare(strict_types=1);

namespace App\Collection;

use App\Model\OrderItem;
use App\Model\Money;
use InvalidArgumentException;

final class OrderItemCollection
{
   private array $items;

   public function __construct(OrderItem ...$items)
   {
       $this->items = $items;
   }

   public function add(OrderItem $item): void
   {
       $this->items[] = $item;
   }

   public function all(): array
   {
       return $this->items;
   }

   public function count(): int
   {
       return count($this->items);
   }

   public function isEmpty(): bool
   {
       return empty($this->items);
   }

   public function total(): Money
    {
        if ($this->isEmpty()) {
            throw new InvalidArgumentException(
                'Cannot calculate total for an empty collection.'
            );
        }

        $total = $this->items[0]->lineTotal();

        foreach (array_slice($this->items, 1) as $item) {
            $total = $total->add(
                $item->lineTotal()
            );
        }

        return $total;
    }
}