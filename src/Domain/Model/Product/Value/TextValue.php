<?php

declare(strict_types=1);

namespace App\Domain\Model\Product\Value;

final class TextValue extends AbstractValue
{
    public function toArray(): array
    {
        return [$this->header => $this->data];
    }
}
