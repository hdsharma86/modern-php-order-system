<?php
declare(strict_types=1);

namespace App\Model;

use InvalidArgumentException;
final readonly class OrderItem
{
    public function __construct(
        public int $productId,
        public string $productName,
        public Money $unitPrice,
        public int $quantity
    ){
        if($productId < 1){
            throw new InvalidArgumentException('Product ID must be a positive integer.');
        }

        if(trim($productName) === ''){
            throw new InvalidArgumentException('Product name cannot be empty.');
        }

        if($quantity < 1){
            throw new InvalidArgumentException('Quantity must be a positive integer.');
        }
    }

    public function lineTotal(): Money
    {
        return $this->unitPrice->multiply($this->quantity);
    }
}