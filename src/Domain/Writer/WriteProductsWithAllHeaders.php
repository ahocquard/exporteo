<?php

declare(strict_types=1);

namespace App\Domain\Writer;

use App\Domain\Model\ExportHeaders;

interface WriteProductsWithAllHeaders
{
    public function write(iterable $products, ExportHeaders $headers): void;
}
