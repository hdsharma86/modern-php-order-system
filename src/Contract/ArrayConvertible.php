<?php

declare(strict_types=1);

namespace App\Contract;

interface ArrayConvertible
{
    public function toArray(): array;
}