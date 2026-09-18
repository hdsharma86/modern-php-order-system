<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Collection\OrderItemCollection;
use App\Model\Money;
use App\Model\OrderItem;
use InvalidArgumentException;
use LogicException;
use PHPUnit\Framework\TestCase;

final class OrderItemCollectionTest extends TestCase
{
    public function test_new_collection_can_be_empty(): void
    {
        $collection = new OrderItemCollection();

        $this->assertTrue($collection->isEmpty());
        $this->assertSame(0, $collection->count());
        $this->assertSame([], $collection->all());
    }

    public function test_collection_can_be_created_with_items(): void
    {
        $itemOne = $this->createItem(
            productId: 101,
            productName: 'Keyboard',
            amount: 1000
        );

        $itemTwo = $this->createItem(
            productId: 102,
            productName: 'Mouse',
            amount: 500
        );

        $collection = new OrderItemCollection(
            $itemOne,
            $itemTwo
        );

        $this->assertFalse($collection->isEmpty());
        $this->assertSame(2, $collection->count());

        $this->assertSame(
            [$itemOne, $itemTwo],
            $collection->all()
        );
    }

    public function test_item_can_be_added_to_collection(): void
    {
        $collection = new OrderItemCollection();

        $item = $this->createItem(
            productId: 101,
            productName: 'Keyboard',
            amount: 1000
        );

        $collection->add($item);

        $this->assertFalse($collection->isEmpty());
        $this->assertSame(1, $collection->count());
        $this->assertSame([$item], $collection->all());
    }

    public function test_collection_calculates_total(): void
    {
        $keyboard = $this->createItem(
            productId: 101,
            productName: 'Keyboard',
            amount: 1000,
            quantity: 2
        );

        $mouse = $this->createItem(
            productId: 102,
            productName: 'Mouse',
            amount: 500,
            quantity: 1
        );

        $collection = new OrderItemCollection(
            $keyboard,
            $mouse
        );

        $total = $collection->total();

        $this->assertSame(2500, $total->amountInMinorUnits);
        $this->assertSame('USD', $total->currency);
        $this->assertSame('USD 25.00', $total->formatted());
    }

    public function test_total_cannot_be_calculated_for_empty_collection(): void
    {
        $collection = new OrderItemCollection();

        $this->expectException(InvalidArgumentException::class);

        $collection->total();
    }

    public function test_collection_rejects_mixed_currencies(): void
    {
        $usdItem = $this->createItem(
            productId: 101,
            productName: 'Keyboard',
            amount: 1000,
            currency: 'USD'
        );

        $inrItem = $this->createItem(
            productId: 102,
            productName: 'Mouse',
            amount: 50000,
            currency: 'INR'
        );

        $collection = new OrderItemCollection(
            $usdItem,
            $inrItem
        );

        $this->expectException(InvalidArgumentException::class);

        $collection->total();
    }

    private function createItem(
        int $productId,
        string $productName,
        int $amount,
        int $quantity = 1,
        string $currency = 'USD'
    ): OrderItem {
        return new OrderItem(
            productId: $productId,
            productName: $productName,
            unitPrice: new Money(
                amountInMinorUnits: $amount,
                currency: $currency
            ),
            quantity: $quantity
        );
    }
}