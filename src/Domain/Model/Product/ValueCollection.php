<?php

declare(strict_types=1);

namespace App\Domain\Model\Product;

use App\Domain\Model\Product\Value\Value;

final class ValueCollection
{
    /** @var Value[] */
    private $values;

    public function __construct(Value ... $values)
    {
        $this->values = $values;
    }

    public function toArray(): array
    {
        if (empty($this->values)) {
            return [];
        }

        $valuesAsArray = [];
        foreach ($this->values as $value) {
            $valuesAsArray[] = $value->toArray();
        }

        return empty($valuesAsArray) ? [] : array_merge(...$valuesAsArray);
    }

    public function headers(): array
    {
        $headers = [];
        foreach ($this->values as $value) {
            $headers[] = $value->headers();
        }

        return empty($headers) ? [] : array_merge(...$headers);
    }
}
