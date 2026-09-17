<?php

declare(strict_types=1);

namespace App\Model;
use InvalidArgumentException;

final readonly class Money
{
    public function __construct(
        public int $amountInMinorUnits,
        public string $currency = 'INR'
    ){
        if($amountInMinorUnits < 0){
            throw new InvalidArgumentException('Amount cannot be negative');
        }

        if(strlen($currency) !== 3){
            throw new InvalidArgumentException('Currency must be a 3-letter code');
        }
    }

    public function add(self $money): self
    {
        $this->ensureSameCurrency($money);
        return new self(
            amountInMinorUnits: $this->amountInMinorUnits + $money->amountInMinorUnits,
            currency: $this->currency
        );
    }

    public function multiply(int $quentity): self
    {
        if($quentity < 1){
            throw new InvalidArgumentException('Quantity must be at least 1');
        }
        return new self(
            amountInMinorUnits: $this->amountInMinorUnits * $quentity,
            currency: $this->currency
        );
    }

    public function formatted(): string
    {
        return sprintf('%s %.2f', $this->currency, $this->amountInMinorUnits / 100);
    }

    public function ensureSameCurrency(self $money): void
    {
        if ($this->currency !== $money->currency) {
            throw new InvalidArgumentException('Cannot operate on different currencies');
        }
    }
}