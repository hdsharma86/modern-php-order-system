<?php

declare(strict_types=1);
namespace App\Contract;

interface Loggable
{
    public function toLogContext(): array;
}