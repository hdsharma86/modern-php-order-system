<?php

declare(strict_types=1);

namespace App\Support;

use App\Attribute\AuditEvent;
use ReflectionMethod;

final class AuditEventReader
{
    public function read(
        object|string $class,
        string $method
    ): ?AuditEvent {
        $reflectionMethod = new ReflectionMethod(
            $class,
            $method
        );

        $attributes = $reflectionMethod->getAttributes(
            AuditEvent::class
        );

        if ($attributes === []) {
            return null;
        }

        return $attributes[0]->newInstance();
    }
}