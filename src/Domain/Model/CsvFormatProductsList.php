<?php

declare(strict_types=1);

namespace App\Domain\Model;

final class CsvFormatProductsList
{
    /** @var CsvFormatProduct[] */
    private $products = [];

    public function __construct(CsvFormatProduct ... $products)
    {
        $this->products = $products;
    }

    public function products(): array
    {
        return $this->products;
    }

    public function add(CsvFormatProduct $product)
    {
        $products = $this->products;
        $products[] = $product;

        return new self(...$products);
    }

    public function toArray(): array
    {
        $productsAsArray = [];

        foreach ($this->products as $product) {
            $productsAsArray[] = $product->toArray();
        }

        return $productsAsArray;
    }
}
