<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Filesystem;


use App\Domain\Model\ExportHeaders;
use App\Domain\Model\Product\ProductCollection;
use App\Domain\Writer\TemporaryProductStorage;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\Assert\Assert;

final class SerializedTemporaryProductStorage implements TemporaryProductStorage
{
    /** @var string */
    private $filepath;

    /** @var Filesystem */
    private $filesystem;

    public function __construct(string $filepath)
    {
        $this->filepath = $filepath;
        $this->filesystem = new Filesystem();
    }

    public function persist(ProductCollection $products): void
    {
           $this->filesystem->appendToFile($this->filepath, serialize($products) . PHP_EOL);
    }

    public function fetchWithAllHeaders(ExportHeaders $exportHeaders): iterable
    {
        $resource = fopen($this->filepath, 'r');
        Assert::true(is_resource($resource));

        $serializedProducts = stream_get_line($resource, 1000000, PHP_EOL);

        while ($serializedProducts !== false) {
            $unserializedProducts = $this->unserializeProducts($serializedProducts);

            foreach ($unserializedProducts->products() as $product) {
                yield $product;
            }

            $serializedProducts = stream_get_line($resource, 1000000, PHP_EOL);
        }
    }

    private function unserializeProducts(string $serializedProduct): ProductCollection
    {
        return unserialize($serializedProduct);
    }
}