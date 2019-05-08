<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model;

use App\Domain\Model\ApiFormatProduct;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

/**
 * @copyright 2019 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ApiFormatProductTestCase extends TestCase
{
    public function test_it_transformAsArray(): void
    {
        $product = new ApiFormatProduct('my_product', ['shoes', 'clothes']);
        Assert::assertSame(['shoes', 'clothes'] , $product->categories());
    }

    public function test_it_has_an_identifier(): void
    {
        $product = new ApiFormatProduct('my_product', ['shoes', 'clothes']);
        Assert::assertSame(['shoes', 'clothes'] , $product->categories());
    }
}
