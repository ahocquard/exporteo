<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model\Product;

use Akeneo\Pim\ApiClient\Api\AttributeApiInterface;
use App\Domain\Model\Product\Value\TextValue;
use App\Domain\Model\Product\ValueList;
use App\Infrastructure\API\Product\ValueCollectionFactory;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ValueCollectionFactoryUnitTest extends TestCase
{
    public function test_it_creates_text_value(): void
    {
        $attributeApi = $this->prophesize(AttributeApiInterface::class);
        $attributeApi->get('name')->willReturn(['code' => 'name', 'type' => 'pim_catalog_text']);

        $valueCollectionFactory = new ValueCollectionFactory($attributeApi->reveal());
        Assert::assertEqualsCanonicalizing(
            new ValueList(new TextValue('name', null, null, 'Big boot')),
            $valueCollectionFactory->fromApiFormat(
                [
                    'name' => [
                        [
                            'locale' => null,
                            'scope' => null,
                            'data' => 'Big boot'
                        ]
                    ]
                ]
            ),
        );
    }
}
