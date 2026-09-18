<?php
declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
ini_set('error_reporting', E_ALL);

require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\Model\User;
use App\Model\Order;
use App\Model\OrderItem;
use App\Model\Money;
use App\Service\OrderService;
use App\Exception\InvalidOrderStateException;
use App\Repository\InMemoryOrderRepository;
use App\Enum\OrderStatus;
use App\Collection\OrderItemCollection;
use App\Support\AuditEventReader;

$customer = new User(1, 'Hardev Sharma', 'hdsharma@example.com', '123-456-7890');
$keyboard = new OrderItem (
    productId: 101,
    productName: 'Wireless Keyboard',
    unitPrice: new Money(
        amountInMinorUnits: 150000,
        currency: 'INR'
    ),
    quantity: 2
);

$mouse = new OrderItem (
    productId: 102,
    productName: 'Wireless Mouse',
    unitPrice: new Money(
        amountInMinorUnits: 80000,
        currency: 'INR'
    ),
    quantity: 1
);

$items = new OrderItemCollection($keyboard, $mouse);
$order = new Order(
    id: 1,
    customer: $customer,
    items: $items
);

echo "Order ID: {$order->id}\n";
echo "Customer: {$order->customer->name}\n";
echo "Items:\n";
foreach ($order->items() as $item) {
    echo "- {$item->productName}: {$item->quantity} x {$item->unitPrice->formatted()} {$item->unitPrice->currency} = {$item->lineTotal()->formatted()} {$item->lineTotal()->currency}\n";
}
echo "Total: {$order->total()->formatted()} {$order->total()->currency}\n";
echo "Status: {$order->status()->value}\n";

$repository = new InMemoryOrderRepository();
$orderService = new OrderService($repository);

$orderService->create($order);

echo "Initial status: {$order->status()->value}" . PHP_EOL;

$orderService->changeOrderStatus(
    orderId: $order->id,
    newStatus: OrderStatus::PROCESSING
);

echo "Updated status: {$order->status()->value}" . PHP_EOL;

$orderService->changeOrderStatus(
    orderId: $order->id,
    newStatus: OrderStatus::COMPLETED
);

try {
    $orderService->changeOrderStatus(
        orderId: $order->id,
        newStatus: OrderStatus::PENDING
    );
} catch (InvalidOrderStateException $exception) {
    echo $exception->getMessage() . PHP_EOL;
}

echo "Final status: {$order->status()->value}" . PHP_EOL;


$auditEventReader = new AuditEventReader();
$auditEvent = $auditEventReader->read(
    class: Order::class,
    method: 'changeStatus'
);
if($auditEvent !== null) {
    echo "Audit Event Name: {$auditEvent->eventName}" . PHP_EOL;
} else {
    echo "No Audit Event found for method changeOrderStatus" . PHP_EOL;
}