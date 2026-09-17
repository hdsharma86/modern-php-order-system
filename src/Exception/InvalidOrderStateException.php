<?php
declare(strict_types=1);
namespace App\Exception;

use App\Enum\OrderStatus;
use DomainException;

final class InvalidOrderStateException extends DomainException
{
    public static function forTransition(OrderStatus $currentStatus, OrderStatus $newStatus): self
    {
        return new self(sprintf(
            'Invalid order state transition from "%s" to "%s".',
            $currentStatus->value,
            $newStatus->value
        ));
    }
}