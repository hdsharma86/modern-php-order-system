<?php
declare(strict_types=1);
namespace App\Exception;

use RuntimeException;

final class OrderNotFoundException extends RuntimeException
{
    public static function forId(int $id): self
    {
        return new self("Order with ID {$id} not found.");
    }
}