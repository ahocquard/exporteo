<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Model\Product\ProductCollection;

interface Page
{
    public function productList(): ProductCollection;

    public function nextPage(): Page;

    public function hasNextPage(): bool ;
}
