<?php

declare(strict_types=1);

namespace App\Domain\Model\Product;

final class ProductList
{
    /** @var Product[] */
    private $products = [];

    public function __construct(Product ... $products)
    {
        $this->products = $products;
    }

    public function products(): array
    {
        return $this->products;
    }

    public function add(Product $product)
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

    public function headers(): array
    {
        $headers = [];
        foreach ($this->products() as $product) {
            $headers[] = $product->headers();
        }
        $headers = array_unique(array_merge(...$headers));
        sort($headers);

        return $headers;
    }
}
