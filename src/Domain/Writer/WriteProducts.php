<?php

declare(strict_types=1);

namespace App\Domain\Writer;

interface WriteProducts
{
    public function asCsv(iterable $products): void;

    public function asJson(iterable $products): void;
}
