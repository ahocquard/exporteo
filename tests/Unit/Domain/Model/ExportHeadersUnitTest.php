<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model;

use App\Domain\Model\ApiFormatProduct;
use App\Domain\Model\ApiFormatProductsList;
use App\Domain\Model\ExportHeaders;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class ExportHeadersUnitTest extends TestCase
{
    public function test_it_creates_an_empty_list_of_headers(): void
    {
        $headers = ExportHeaders::empty();

        Assert::assertSame($headers->headers(), []);
    }

    public function test_it_adds_an_header(): void
    {
        $headers = ExportHeaders::empty();
        $headers = $headers->addHeaders('new_header_1');

        Assert::assertSame($headers->headers(), ['new_header_1']);
    }

    public function test_it_adds_several_headers(): void
    {
        $headers = ExportHeaders::empty();
        $headers = $headers->addHeaders('new_header_1', 'new_header_2');

        Assert::assertSame($headers->headers(), ['new_header_1', 'new_header_2']);
    }
}
