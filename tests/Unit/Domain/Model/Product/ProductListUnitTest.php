<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model\Product;

use App\Domain\Model\ExportHeaders;
use App\Domain\Model\Product\Product;
use App\Domain\Model\Product\ProductList;
use App\Domain\Model\Product\Value\ScalarValue;
use App\Domain\Model\Product\ValueCollection;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ProductListUnitTest extends TestCase
{
    public function test_it_transforms_as_array(): void
    {
        $products = new ProductList(
            new Product('my_product_1', ['shoes', 'clothes'], new ValueCollection(
                    new ScalarValue('attribute_code_1', null, null, 'data_1'),
                    new ScalarValue('attribute_code_2', 'en_US', null, 'data_2'),
                    new ScalarValue('attribute_code_3', null, 'tablet', 'data_3'),
                    new ScalarValue('attribute_code_4', 'fr_FR', 'ecommerce', 'data_4'),
                ),
            ),
            new Product('my_product_2', [], new ValueCollection())
        );

        $headers = new ExportHeaders();
        $headers->addHeaders(
            'identifier',
            'categories',
            'attribute_code_1',
            'attribute_code_2-en_US',
            'attribute_code_3-tablet',
            'attribute_code_4-fr_FR-ecommerce',
            'attribute_code_5',
            'categories',
            'identifier',
        );

        Assert::assertSame(
            [
                [
                    'attribute_code_1' => 'data_1',
                    'attribute_code_2-en_US' => 'data_2',
                    'attribute_code_3-tablet' => 'data_3',
                    'attribute_code_4-fr_FR-ecommerce' => 'data_4',
                    'attribute_code_5' => null,
                    'categories' => 'shoes,clothes',
                    'identifier' => 'my_product_1',
                ],
                [
                    'attribute_code_1' => null,
                    'attribute_code_2-en_US' => null,
                    'attribute_code_3-tablet' => null,
                    'attribute_code_4-fr_FR-ecommerce' => null,
                    'attribute_code_5' => null,
                    'categories' => '',
                    'identifier' => 'my_product_2',
                ]
            ],
            $products->toArray($headers)
        );
    }

    public function test_it_gets_csv_headers(): void
    {
        $products = new ProductList(
            new Product('my_product_1', ['shoes', 'clothes'], new ValueCollection(
                new ScalarValue('attribute_code_1', null, null, 'data_1'),
                new ScalarValue('attribute_code_2', 'en_US', null, 'data_2'),
                new ScalarValue('attribute_code_3', null, 'tablet', 'data_3'),
                new ScalarValue('attribute_code_4', 'fr_FR', 'ecommerce', 'data_4'),
                ),
            ),
            new Product('my_product_2', [], new ValueCollection())
        );

        Assert::assertSame(
            [
                'attribute_code_1',
                'attribute_code_2-en_US',
                'attribute_code_3-tablet',
                'attribute_code_4-fr_FR-ecommerce',
                'categories',
                'identifier'
            ],
            $products->headers()
        );
    }
}
