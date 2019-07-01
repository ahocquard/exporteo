<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model\Product;

use App\Domain\Model\ExportHeaders;
use App\Domain\Model\Product\Product;
use App\Domain\Model\Product\ProductCollection;
use App\Domain\Model\Product\Value\MetricValue;
use App\Domain\Model\Product\Value\ScalarCollectionValue;
use App\Domain\Model\Product\Value\PriceValue;
use App\Domain\Model\Product\Value\ScalarValue;
use App\Domain\Model\Product\ValueCollection;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ProductCollectionUnitTest extends TestCase
{
    public function test_it_transforms_as_array(): void
    {
        $products = new ProductCollection(
            new Product(
                'my_product_1',
                'family_code',
                'parent_code',
                ['group_code_1', 'group_code_2'],
                ['shoes', 'clothes'],
                true,
                new ValueCollection(
                    new ScalarValue('attribute_code_1', null, null, 'data_1'),
                    new ScalarValue('attribute_code_2', 'en_US', null, 'data_2'),
                    new ScalarValue('attribute_code_3', null, 'tablet', 'data_3'),
                    new ScalarValue('attribute_code_4', 'fr_FR', 'ecommerce', 'data_4'),
                    new ScalarCollectionValue('attribute_code_5', null, null, ['foo', 'baz']),
                    new ScalarCollectionValue('attribute_code_6', 'en_US', null, ['foo', 'baz']),
                    new ScalarCollectionValue('attribute_code_7', null, 'tablet', ['foo', 'baz']),
                    new ScalarCollectionValue('attribute_code_8', 'fr_FR', 'ecommerce', ['foo', 'baz']),
                    new PriceValue('attribute_code_9', null, null,
                        [
                            ['amount' => '45.00', 'currency' => 'USD'],
                            ['amount' => '50.50', 'currency' => 'EUR']
                        ]
                    ),
                    new PriceValue('attribute_code_10', 'fr_FR', null,
                        [
                            ['amount' => '45.00', 'currency' => 'USD'],
                            ['amount' => '50.50', 'currency' => 'EUR']
                        ]
                    ),
                    new PriceValue('attribute_code_11', null, 'ecommerce',
                        [
                            ['amount' => '45.00', 'currency' => 'USD'],
                            ['amount' => '50.50', 'currency' => 'EUR']
                        ]
                    ),
                    new PriceValue('attribute_code_12', 'fr_FR', 'ecommerce',
                        [
                            ['amount' => '45.00', 'currency' => 'USD'],
                            ['amount' => '50.50', 'currency' => 'EUR']
                        ]
                    ),
                    new MetricValue('attribute_code_13', null, null, ['amount' => '10.50', 'unit' => 'KILOWATT']),
                    new MetricValue('attribute_code_14', 'fr_FR', null, ['amount' => '20.50', 'unit' => 'KILOWATT']),
                    new MetricValue('attribute_code_15', null, 'ecommerce', ['amount' => '30.50', 'unit' => 'KILOWATT']),
                    new MetricValue('attribute_code_16', 'fr_FR', 'ecommerce', ['amount' => '40.50', 'unit' => 'KILOWATT']),
                ),
            ),
            new Product('my_product_2', null, null, [], [], false, new ValueCollection())
        );

        Assert::assertSame(
            [
                [
                    'attribute_code_1' => 'data_1',
                    'attribute_code_10-fr_FR-EUR' => '50.50',
                    'attribute_code_10-fr_FR-USD' => '45.00',
                    'attribute_code_11-ecommerce-EUR' => '50.50',
                    'attribute_code_11-ecommerce-USD' => '45.00',
                    'attribute_code_12-fr_FR-ecommerce-EUR' => '50.50',
                    'attribute_code_12-fr_FR-ecommerce-USD' => '45.00',
                    'attribute_code_13' => '10.50',
                    'attribute_code_13-unit' => 'KILOWATT',
                    'attribute_code_14-fr_FR' => '20.50',
                    'attribute_code_14-fr_FR-unit' => 'KILOWATT',
                    'attribute_code_15-ecommerce' => '30.50',
                    'attribute_code_15-ecommerce-unit' => 'KILOWATT',
                    'attribute_code_16-fr_FR-ecommerce' => '40.50',
                    'attribute_code_16-fr_FR-ecommerce-unit' => 'KILOWATT',
                    'attribute_code_2-en_US' => 'data_2',
                    'attribute_code_3-tablet' => 'data_3',
                    'attribute_code_4-fr_FR-ecommerce' => 'data_4',
                    'attribute_code_5' => 'foo,baz',
                    'attribute_code_6-en_US' => 'foo,baz',
                    'attribute_code_7-tablet' => 'foo,baz',
                    'attribute_code_8-fr_FR-ecommerce' => 'foo,baz',
                    'attribute_code_9-EUR' => '50.50',
                    'attribute_code_9-USD' => '45.00',
                    'categories' => 'shoes,clothes',
                    'enabled' => true,
                    'family' => 'family_code',
                    'groups' => 'group_code_1,group_code_2',
                    'identifier' => 'my_product_1',
                    'parent' => 'parent_code'
                ],
                [
                    'categories' => '',
                    'enabled' => false,
                    'family' => null,
                    'groups' => '',
                    'identifier' => 'my_product_2',
                    'parent' => null
                ]
            ],
            $products->toArray()
        );
    }

    public function test_it_gets_csv_headers(): void
    {
        $products = new ProductCollection(
            new Product(
                'my_product_1',
                'family_code',
                'parent_code',
                ['group_code_1', 'group_code_2'],
                ['shoes', 'clothes'],
                true,
                new ValueCollection(
                    new ScalarValue('attribute_code_1', null, null, 'data_1'),
                    new ScalarValue('attribute_code_2', 'en_US', null, 'data_2'),
                    new ScalarValue('attribute_code_3', null, 'tablet', 'data_3'),
                    new ScalarValue('attribute_code_4', 'fr_FR', 'ecommerce', 'data_4'),
                    new ScalarCollectionValue('attribute_code_5', null, null, ['foo', 'baz']),
                    new ScalarCollectionValue('attribute_code_6', 'en_US', null, ['foo', 'baz']),
                    new ScalarCollectionValue('attribute_code_7', null, 'tablet', ['foo', 'baz']),
                    new ScalarCollectionValue('attribute_code_8', 'fr_FR', 'ecommerce', ['foo', 'baz']),
                    new PriceValue('attribute_code_9', null, null,
                        [
                            ['amount' => '45.00', 'currency' => 'USD'],
                            ['amount' => '50.50', 'currency' => 'EUR']
                        ]
                    ),
                    new PriceValue('attribute_code_10', 'fr_FR', null,
                        [
                            ['amount' => '45.00', 'currency' => 'USD'],
                            ['amount' => '50.50', 'currency' => 'EUR']
                        ]
                    ),
                    new PriceValue('attribute_code_11', null, 'ecommerce',
                        [
                            ['amount' => '45.00', 'currency' => 'USD'],
                            ['amount' => '50.50', 'currency' => 'EUR']
                        ]
                    ),
                    new PriceValue('attribute_code_12', 'fr_FR', 'ecommerce',
                        [
                            ['amount' => '45.00', 'currency' => 'USD'],
                            ['amount' => '50.50', 'currency' => 'EUR']
                        ]
                    ),
                    new MetricValue('attribute_code_13', null, null, ['amount' => '10.50', 'unit' => 'KILOWATT']),
                    new MetricValue('attribute_code_14', 'fr_FR', null, ['amount' => '20.50', 'unit' => 'KILOWATT']),
                    new MetricValue('attribute_code_15', null, 'ecommerce', ['amount' => '30.50', 'unit' => 'KILOWATT']),
                    new MetricValue('attribute_code_16', 'fr_FR', 'ecommerce', ['amount' => '40.50', 'unit' => 'KILOWATT']),
                    ),
                ),
            new Product('my_product_2', null, null, [], [], false, new ValueCollection())
        );

        $expected = [
            'attribute_code_1',
            'attribute_code_2-en_US',
            'attribute_code_3-tablet',
            'attribute_code_4-fr_FR-ecommerce',
            'attribute_code_5',
            'attribute_code_6-en_US',
            'attribute_code_7-tablet',
            'attribute_code_8-fr_FR-ecommerce',
            'attribute_code_9-USD',
            'attribute_code_9-EUR',
            'attribute_code_10-fr_FR-USD',
            'attribute_code_10-fr_FR-EUR',
            'attribute_code_11-ecommerce-USD',
            'attribute_code_11-ecommerce-EUR',
            'attribute_code_12-fr_FR-ecommerce-USD',
            'attribute_code_12-fr_FR-ecommerce-EUR',
            'attribute_code_13',
            'attribute_code_13-unit',
            'attribute_code_14-fr_FR',
            'attribute_code_14-fr_FR-unit',
            'attribute_code_15-ecommerce',
            'attribute_code_15-ecommerce-unit',
            'attribute_code_16-fr_FR-ecommerce',
            'attribute_code_16-fr_FR-ecommerce-unit',
            'categories',
            'enabled',
            'family',
            'groups',
            'identifier',
            'parent',
        ];

        $headers = $products->headers();
        sort($expected);
        sort($headers);

        Assert::assertSame($expected, $headers);
    }
}
