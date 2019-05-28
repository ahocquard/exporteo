<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Model\Product\ProductList;

interface Page
{
    public function productList(): ProductList;

    public function nextPage(): Page;

    public function hasNextPage(): bool ;
}
