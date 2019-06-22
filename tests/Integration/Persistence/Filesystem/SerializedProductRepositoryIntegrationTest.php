<?php

declare(strict_types=1);

namespace App\Tests\Integration\Persistence\Api\Product;

use App\Domain\Model\Product\Product;
use App\Domain\Model\Product\ProductCollection;
use App\Domain\Model\Product\Value\ScalarValue;
use App\Domain\Model\Product\ValueCollection;
use App\Infrastructure\Persistence\Api\Product\GetProductCollection;
use App\Infrastructure\Persistence\Filesystem\SerializedProductRepository;
use Concurrent\Http\HttpServer;
use Concurrent\Http\HttpServerConfig;
use Concurrent\Http\HttpServerListener;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Concurrent\Network\TcpServer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;

class SerializedProductRepositoryIntegrationTest extends KernelTestCase
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

    public function test_it_persists_serialized_products_in_a_file(): void
    {
        $filepath = static::$kernel->getProjectDir() . '/var/test-files/serialized_products.tmp';
        $serializedProductRepository = new SerializedProductRepository($filepath);

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
        $serializedProductRepository->persist($productsCollection1);
        $serializedProductRepository->persist($productsCollection2);

        $content = file_get_contents($filepath);
        $expectedContent = file_get_contents(__DIR__ . '/' . 'serialized_products.expected');

        Assert::assertSame($expectedContent, $content);
    }

    public function test_it_reads_serialized_products_in_a_file(): void
    {
        $filepath = __DIR__ . '/serialized_products.expected';
        $serializedProductRepository = new SerializedProductRepository($filepath);

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

        $page = $serializedProductRepository->fetch();
        Assert::assertTrue($page->hasNextPage());
        Assert::assertEqualsCanonicalizing($productsCollection1, $page->productList());

        $page = $page->nextPage();
        Assert::assertTrue($page->hasNextPage());
        Assert::assertEqualsCanonicalizing($productsCollection2, $page->productList());

        $page = $page->nextPage();
        Assert::assertFalse($page->hasNextPage());
        Assert::assertEqualsCanonicalizing(new ProductCollection(), $page->productList());
    }

    public function test_it_reads_from_an_empty_file(): void
    {
        $filepath = __DIR__ . '/serialized_products_empty_file.test';
        $serializedProductRepository = new SerializedProductRepository($filepath);

        $page = $serializedProductRepository->fetch();

        Assert::assertFalse($page->hasNextPage());
        Assert::assertEqualsCanonicalizing(new ProductCollection(), $page->productList());
    }
}
