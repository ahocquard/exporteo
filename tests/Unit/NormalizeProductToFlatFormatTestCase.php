<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Domain\Model\ApiFormatProduct;
use App\Domain\Normalizer\ProductNormalizer;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class NormalizeProductToFlatFormatTestCase extends TestCase
{
    public function test_it_transform_properties_to_flat_format(): void
    {
        $product = new ApiFormatProduct('my_product', ['shoes', 'clothes']);
        $normalizer = new ProductNormalizer();

        Assert::assertSame(
            [
                'identifier' => 'my_product',
                'categories' => 'shoes,clothes'
            ],
            $normalizer->toFlatFormat($product)
        );
    }
}
