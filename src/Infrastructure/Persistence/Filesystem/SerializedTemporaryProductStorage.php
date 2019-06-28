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
           $this->filesystem->appendToFile($this->filepath, json_encode($products->toArray(ExportHeaders::empty())) . PHP_EOL);
    }

    public function fetchWithAllHeaders(ExportHeaders $exportHeaders): iterable
    {
        $resource = fopen($this->filepath, 'r');
        Assert::true(is_resource($resource));

        $serializedProducts = stream_get_line($resource, 1000000, PHP_EOL);

        while ($serializedProducts !== false) {
            $unserializedProducts = $this->decodeProducts($serializedProducts);

            foreach ($unserializedProducts as $product) {
                $data = array_merge(array_fill_keys($exportHeaders->headers(), null), $product);
                ksort($data);

                yield $data;
            }

            $serializedProducts = stream_get_line($resource, 1000000, PHP_EOL);
        }
    }

    private function decodeProducts(string $serializedProduct): array
    {
        return json_decode($serializedProduct, true);
    }
}