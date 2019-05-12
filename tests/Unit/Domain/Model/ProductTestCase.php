<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model;

use App\Domain\Model\ApiFormatProduct;
use App\Domain\Model\Product;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ProductTestCase extends TestCase
{
    public function test_it_transform_as_array(): void
    {
        $product = new Product('my_product', ['shoes', 'clothes']);
        Assert::assertSame(
            [
                'categories' => 'shoes,clothes',
                'identifier' => 'my_product'
            ],
            $product->toArray()
        );
    }

    public function test_it_gets_csv_headers(): void
    {
        $product = new Product('my_product', ['shoes', 'clothes']);
        Assert::assertSame(
            [
                'identifier',
                'categories'
            ],
            $product->headers()
        );
    }
}
