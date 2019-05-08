<?php

declare(strict_types=1);

namespace App\Domain\Model;

interface Page
{
    public function productList(): ApiFormatProductsList;

    public function nextPage(): Page;

    public function hasNextPage(): bool ;
}
