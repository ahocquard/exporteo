<?php

declare(strict_types=1);

namespace App\Domain\Model;

final class ApiFormatProductsList
{
    /** @var ApiFormatProduct[] */
    private $products;

    public function __construct(ApiFormatProduct ... $products)
    {
        $this->products = $products;
    }

    public function products(): array
    {
        return $this->products;
    }

    public function add(ApiFormatProduct $product)
    {
        $products = $this->products;
        $products[] = $product;

        return new self(...$products);
    }
}
