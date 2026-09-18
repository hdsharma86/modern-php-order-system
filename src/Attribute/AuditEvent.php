<?php
declare(strict_types=1);

namespace App\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
final readonly class AuditEvent
{
    public function __construct(
        public string $eventName
    ) {}
}