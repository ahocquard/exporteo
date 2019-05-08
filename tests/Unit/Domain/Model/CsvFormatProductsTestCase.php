<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model;

use App\Domain\Model\ApiFormatProduct;
use App\Domain\Model\ApiFormatProductsList;
use App\Domain\Model\CsvFormatProduct;
use App\Domain\Model\CsvFormatProductsList;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class CsvFormatProductsTestCase extends TestCase
{
    public function test_it_transform_as_array(): void
    {
        $products = new CsvFormatProductsList(
            new CsvFormatProduct('my_product_1', ['shoes', 'clothes']),
            new CsvFormatProduct('my_product_2', [])
        );

        Assert::assertSame(
            [
                [
                    'identifier' => 'my_product_1',
                    'categories' => 'shoes,clothes'
                ],
                [
                    'identifier' => 'my_product_2',
                    'categories' => ''
                ]
            ],
            $products->toArray()
        );
        $products->toArray();
    }

    public function test_it_creates_from_an_api_format_product_list(): void
    {
        $product = CsvFormatProductsList::fromApiFormatProductList(
            new ApiFormatProductsList(
                new ApiFormatProduct('my_product', ['shoes', 'clothes'])
            )
        );

        Assert::assertSame(
            [
                [
                    'identifier' => 'my_product',
                    'categories' => 'shoes,clothes'
                ]
            ],
            $product->toArray()
        );
    }

    public function test_it_gets_csv_headers(): void
    {
        $products = new CsvFormatProductsList(
            new CsvFormatProduct('my_product_1', ['shoes', 'clothes']),
            new CsvFormatProduct('my_product_2', [])
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
