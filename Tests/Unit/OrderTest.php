<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Model\Order;
use App\Enum\OrderStatus;
use PHPUnit\Framework\TestCase;
use App\Exception\InvalidOrderStateException;
use App\Collection\OrderItemCollection;

final class OrderTest extends TestCase
{
    public function test_pending_order_can_move_to_processing(): void
    {
        // Arrange
        $order = new Order(
            id: 1,
            customer: new \App\Model\User(
                1,
                'John Doe',
                'john.doe@example.com'
            ),
            items: new OrderItemCollection(
                new \App\Model\OrderItem(
                    productId: 101,
                    productName: 'Sample Product',
                    unitPrice: new \App\Model\Money(
                        amountInMinorUnits: 1000,
                        currency: 'USD'
                    ),
                    quantity: 1
                )
            )
        );
        $order->changeStatus(OrderStatus::PROCESSING);
        $this->assertSame(OrderStatus::PROCESSING, $order->status());
    }

    public function test_pending_order_can_be_cancelled(): void
    {
        // Arrange
        $order = new Order(
            id: 1,
            customer: new \App\Model\User(
                1,
                'John Doe',
                'john.doe@example.com'
            ),
            items: new OrderItemCollection(
                new \App\Model\OrderItem(
                    productId: 101,
                    productName: 'Sample Product',
                    unitPrice: new \App\Model\Money(
                        amountInMinorUnits: 1000,
                        currency: 'USD'
                    ),
                    quantity: 1
                )
            )
        );

        // Act
        $order->changeStatus(OrderStatus::CANCELLED);

        // Assert
        $this->assertSame(
            OrderStatus::CANCELLED,
            $order->status()
        );
    }

    public function test_processing_order_can_be_completed(): void
    {
        // Arrange
        $order = new Order(
            id: 1,
            customer: new \App\Model\User(
                1,
                'John Doe',
                'john.doe@example.com'
            ),
            items: new OrderItemCollection(
                new \App\Model\OrderItem(
                    productId: 101,
                    productName: 'Sample Product',
                    unitPrice: new \App\Model\Money(
                        amountInMinorUnits: 1000,
                        currency: 'USD'
                    ),
                    quantity: 1
                )
            )
        );

        // Order ko pehle PROCESSING mein le jana zaroori hai
        $order->changeStatus(OrderStatus::PROCESSING);

        // Act
        $order->changeStatus(OrderStatus::COMPLETED);

        // Assert
        $this->assertSame(
            OrderStatus::COMPLETED,
            $order->status()
        );
    }

    public function test_pending_order_cannot_move_directly_to_completed(): void
    {
        // Arrange
        $order = new Order(
            id: 1,
            customer: new \App\Model\User(
                1,
                'John Doe',
                'john.doe@example.com'
            ),
            items: new OrderItemCollection(
                new \App\Model\OrderItem(
                    productId: 101,
                    productName: 'Sample Product',
                    unitPrice: new \App\Model\Money(
                        amountInMinorUnits: 1000,
                        currency: 'USD'
                    ),
                    quantity: 1
                )
            )
        );

        // Assert: PHPUnit ko batate hain ki exception expected hai
        $this->expectException(InvalidOrderStateException::class);

        // Act: Yeh invalid transition exception throw karega
        $order->changeStatus(OrderStatus::COMPLETED);
    }
}