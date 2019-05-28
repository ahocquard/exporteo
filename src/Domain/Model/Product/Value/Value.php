<?php

declare(strict_types=1);

namespace App\Domain\Model\Product\Value;

interface Value
{
    public function toArray();

    public function header();
}
