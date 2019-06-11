<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model\Product;

use Akeneo\Pim\ApiClient\Api\AttributeApiInterface;
use App\Domain\Model\Product\Value\MetricValue;
use App\Domain\Model\Product\Value\ScalarValue;
use App\Domain\Model\Product\ValueCollection;
use App\Infrastructure\Persistence\Api\Product\ValueCollectionFactory;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ValueCollectionFactoryUnitTest extends TestCase
{
    public function test_it_creates_values(): void
    {
        $attributeApi = $this->prophesize(AttributeApiInterface::class);
        $attributeApi->get('text')->willReturn(['code' => 'name', 'type' => 'pim_catalog_text']);
        $attributeApi->get('textarea')->willReturn(['code' => 'textarea', 'type' => 'pim_catalog_textarea']);
        $attributeApi->get('identifier')->willReturn(['code' => 'identifier', 'type' => 'pim_catalog_identifier']);
        $attributeApi->get('file')->willReturn(['code' => 'file', 'type' => 'pim_catalog_file']);
        $attributeApi->get('image')->willReturn(['code' => 'image', 'type' => 'pim_catalog_image']);
        $attributeApi->get('date')->willReturn(['code' => 'date', 'type' => 'pim_catalog_date']);
        $attributeApi->get('simpleselect')->willReturn(['code' => 'simpleselect', 'type' => 'pim_catalog_simpleselect']);
        $attributeApi->get('reference_data_simpleselect')->willReturn(['code' => 'reference_data_simpleselect', 'type' => 'pim_catalog_reference_data_simpleselect']);
        $attributeApi->get('number')->willReturn(['code' => 'number', 'type' => 'pim_catalog_number']);
        $attributeApi->get('boolean')->willReturn(['code' => 'boolean', 'type' => 'pim_catalog_boolean']);
        $attributeApi->get('reference_entity_simple_select')->willReturn(['code' => 'reference_entity_simple_select', 'type' => 'reference_entity_simple_select']);
        $attributeApi->get('metric')->willReturn(['code' => 'metric', 'type' => 'pim_catalog_metric']);

        $valueCollectionFactory = new ValueCollectionFactory($attributeApi->reveal());
        Assert::assertEqualsCanonicalizing(
            new ValueCollection(
                new ScalarValue('text', null, null, 'text_value'),
                new ScalarValue('textarea', 'en_US', 'tablet', 'textarea_value'),
                new ScalarValue('file', null, null, 'file_code'),
                new ScalarValue('image', null, null, 'image_code'),
                new ScalarValue('date', null, null, '2012-03-13T00:00:00+01:00'),
                new ScalarValue('simpleselect', null, null, 'simpleselect_value'),
                new ScalarValue('reference_data_simpleselect', null, null, 'reference_data_simpleselect_value'),
                new ScalarValue('number', null, null, 1),
                new ScalarValue('boolean', null, null, true),
                new ScalarValue('reference_entity_simple_select', null, null, 'reference_entity_simple_select_value'),
                new MetricValue('metric', null, null, ['amount' => '14', 'unit' => 'KILOWATT']),
            ),
            $valueCollectionFactory->fromApiFormat(
                [
                    'text' => [
                        [
                            'locale' => null,
                            'scope' => null,
                            'data' => 'text_value'
                        ]
                    ],
                    'textarea' => [
                        [
                            'locale' => 'en_US',
                            'scope' => 'tablet',
                            'data' => 'textarea_value'
                        ]
                    ],
                    'file' => [
                        [
                            'locale' => null,
                            'scope' => null,
                            'data' => 'file_code'
                        ]
                    ],
                    'image' => [
                        [
                            'locale' => null,
                            'scope' => null,
                            'data' => 'image_code'
                        ]
                    ],
                    'date' => [
                        [
                            'locale' => null,
                            'scope' => null,
                            'data' => '2012-03-13T00:00:00+01:00'
                        ]
                    ],
                    'simpleselect' => [
                        [
                            'locale' => null,
                            'scope' => null,
                            'data' => 'simpleselect_value'
                        ]
                    ],
                    'reference_data_simpleselect' => [
                        [
                            'locale' => null,
                            'scope' => null,
                            'data' => 'reference_data_simpleselect_value'
                        ]
                    ],
                    'number' => [
                        [
                            'locale' => null,
                            'scope' => null,
                            'data' => 1
                        ]
                    ],
                    'boolean' => [
                        [
                            'locale' => null,
                            'scope' => null,
                            'data' => true
                        ]
                    ],
                    'reference_entity_simple_select' => [
                        [
                            'locale' => null,
                            'scope' => null,
                            'data' => 'reference_entity_simple_select_value'
                        ]
                    ],
                    'metric' => [
                        [
                            'locale' => null,
                            'scope' => null,
                            'data' => ['amount' => '14', 'unit' => 'KILOWATT']
                        ]
                    ],
                ]
            ),
        );
    }
}
