<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model;

use App\Domain\Model\ApiFormatProduct;
use App\Domain\Model\ApiFormatProductsList;
use App\Domain\Model\Product;
use App\Domain\Model\ProductList;
use App\Domain\Model\ExportHeaders;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ExportHeadersTestCase extends TestCase
{
    public function test_it_adds_an_header(): void
    {
        $headers = new ExportHeaders();
        $headers->addHeaders('new_header_1');

        Assert::assertSame($headers->headers(), ['new_header_1']);
    }

    public function test_it_adds_several_headers(): void
    {
        $headers = new ExportHeaders();
        $headers->addHeaders('new_header_1', 'new_header_2');

        Assert::assertSame($headers->headers(), ['new_header_1', 'new_header_2']);
    }

    public function test_it_sorts_headers(): void
    {
        $headers = new ExportHeaders();
        $headers->addHeaders('new_header_2', 'new_header_1');

        Assert::assertSame($headers->headers(), ['new_header_1', 'new_header_2']);
    }
}
