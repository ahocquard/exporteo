<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model;

use App\Domain\Model\ApiFormatProduct;
use App\Domain\Model\CsvFormatProduct;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class CsvFormatProductTestCase extends TestCase
{
    public function test_it_transform_as_array(): void
    {
        $product = new CsvFormatProduct('my_product', ['shoes', 'clothes']);
        Assert::assertSame(
            [
                'categories' => 'shoes,clothes',
                'identifier' => 'my_product'
            ],
            $product->toArray()
        );
    }

    public function test_it_creates_from_an_api_format_product(): void
    {
        $product = CsvFormatProduct::fromApiFormatProduct(new ApiFormatProduct('my_product', ['shoes', 'clothes']));
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
        $product = new CsvFormatProduct('my_product', ['shoes', 'clothes']);
        Assert::assertSame(
            [
                'identifier',
                'categories'
            ],
            $product->headers()
        );
    }
}
