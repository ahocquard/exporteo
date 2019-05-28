<?php

declare(strict_types=1);

namespace App\Domain\Model\Product;

use App\Domain\Model\Product\Value\Value;

final class ValueList
{
    /** @var Value[] */
    private $values;

    public function __construct(Value ... $values)
    {
        $this->values = $values;
    }

    public function toArray()
    {
        if (empty($this->values)) {
            return [];
        }

        $valuesAsArray = [];
        foreach ($this->values as $value) {
            $valuesAsArray[] = $value->toArray();
        }

        return array_merge(... $valuesAsArray);
    }

    public function headers()
    {
        $headers = [];
        foreach ($this->values as $value) {
            $headers[] = $value->header();
        }

        return $headers;
    }
}
