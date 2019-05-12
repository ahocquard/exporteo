<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model;

use App\Domain\Model\ApiFormatProduct;
use App\Domain\Model\ApiFormatProductsList;
use App\Domain\Model\Product;
use App\Domain\Model\ProductList;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ProductListTestCase extends TestCase
{
    public function test_it_transform_as_array(): void
    {
        $products = new ProductList(
            new Product('my_product_1', ['shoes', 'clothes']),
            new Product('my_product_2', [])
        );

        Assert::assertSame(
            [
                [
                    'categories' => 'shoes,clothes',
                    'identifier' => 'my_product_1',
                ],
                [
                    'categories' => '',
                    'identifier' => 'my_product_2',
                ]
            ],
            $products->toArray()
        );
        $products->toArray();
    }

    public function test_it_gets_csv_headers(): void
    {
        $products = new ProductList(
            new Product('my_product_1', ['shoes', 'clothes']),
            new Product('my_product_2', [])
        );

        Assert::assertSame(
            [
                'categories',
                'identifier'
            ],
            $products->headers()
        );
    }
}
