<?php

declare(strict_types=1);

namespace App\Domain\Model;

interface PageInterface
{
    public function productList(): ApiFormatProductsList;

    public function nextPage(): PageInterface;
}
