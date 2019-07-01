<?php

declare(strict_types=1);

namespace App\Tests\Integration\Persistence\Api\Product;

use App\Domain\Model\ExportHeaders;
use App\Domain\Model\Product\Product;
use App\Domain\Model\Product\ProductCollection;
use App\Domain\Model\Product\Value\ScalarValue;
use App\Domain\Model\Product\ValueCollection;
use App\Infrastructure\Persistence\Filesystem\SerializedTemporaryProductStorage;
use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;

class SerializedTemporaryProductStorageIntegrationTest extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();

        $filepath = static::$kernel->getProjectDir() . '/var/test-files/serialized_products.tmp';
        $filesystem = new Filesystem();
        if ($filesystem->exists($filepath)) {
            $filesystem->remove($filepath);
        }
    }

    public function test_it_persists_products_as_flat_json_in_a_file(): void
    {
        $filepath = static::$kernel->getProjectDir() . '/var/test-files/serialized_products.tmp';
        $storage = new SerializedTemporaryProductStorage($filepath);

        $productsCollection1 = new ProductCollection(
            new Product(
                'big_boot',
                ['summer_collection', 'winter_boots'],
                new ValueCollection(
                    new ScalarValue('color', null, null, 'black'),
                    new ScalarValue('name', null, null, 'Big boot'),
                    )
            ),
            new Product('docks_red', ['winter_collection'], new ValueCollection()),
            new Product('small_boot', [], new ValueCollection()),
        );

        $productsCollection2 = new ProductCollection(new Product('medium_boot', [], new ValueCollection()));
        $storage->persist($productsCollection1);
        $storage->persist($productsCollection2);

        $content = file_get_contents($filepath);
        $expectedContent = file_get_contents(__DIR__ . '/' . 'serialized_products.expected');

        Assert::assertSame($expectedContent, $content);
    }

    public function test_it_reads_flat_products_in_a_file(): void
    {
        $filepath = __DIR__ . '/serialized_products.expected';
        $storage = new SerializedTemporaryProductStorage($filepath);

        $product1 = new Product(
            'big_boot',
            ['summer_collection', 'winter_boots'],
            new ValueCollection(
                new ScalarValue('color', null, null, 'black'),
                new ScalarValue('name', null, null, 'Big boot'),
                )
        );

        $product2 = new Product('docks_red', ['winter_collection'], new ValueCollection());
        $product3 = new Product('small_boot', [], new ValueCollection());
        $product4 = new Product('medium_boot', [], new ValueCollection());

        $exportHeaders = ExportHeaders::empty();
        $exportHeaders = $exportHeaders->addHeaders(
            'categories',
            'color',
            'name',
            'extra-property',
            'identifier'
        );

        $products = [];
        array_push ($products, ...$storage->fetchWithAllHeaders($exportHeaders)); // iterable_as_array

        Assert::assertSame([
            [
                'categories' => 'summer_collection,winter_boots',
	            'color' => 'black',
                'extra-property' => null,
                'identifier' => 'big_boot',
	            'name' => 'Big boot'
            ],
            [
                'categories' => 'winter_collection',
                'color' => null,
                'extra-property' => null,
                'identifier' => 'docks_red',
                'name' => null
            ],
            [
                'categories' => '',
                'color' => null,
                'extra-property' => null,
                'identifier' => 'small_boot',
                'name' => null
            ],
            [
                'categories' =>'',
                'color' => null,
                'extra-property' => null,
                'identifier' => 'medium_boot',
                'name' => null
            ]
        ], $products);
    }

    public function test_it_reads_from_an_empty_file(): void
    {
        $filepath = __DIR__ . '/serialized_products_empty_file.test';
        $storage = new SerializedTemporaryProductStorage($filepath);

        $products = [];
        array_push ($products, ...$storage->fetchWithAllHeaders(ExportHeaders::empty())); // iterable_as_array

        Assert::assertEqualsCanonicalizing([], $products);
    }
}
