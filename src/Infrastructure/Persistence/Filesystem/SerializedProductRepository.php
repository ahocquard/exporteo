<?php


namespace App\Infrastructure\Persistence\Filesystem;


use App\Domain\Model\Product\ProductCollection;
use App\Domain\Writer\ProductRepository;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\Assert\Assert;

final class SerializedProductRepository implements ProductRepository
{
    /** @var string */
    private $filepath;

    public function __construct(string $filepath)
    {
        $this->filepath = $filepath;
    }

    public function persist(ProductCollection $products): void
    {
        $filesystem = new Filesystem();
        $filesystem->appendToFile($this->filepath, serialize($products) . PHP_EOL);
    }

    public function fetch(): \App\Domain\Model\Page
    {
        $resource = fopen($this->filepath, 'r');
        Assert::true(is_resource($resource));

        return new Page($this->resource);
    }

}