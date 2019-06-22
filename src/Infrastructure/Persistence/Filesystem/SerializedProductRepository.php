<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Filesystem;


use App\Domain\Model\Product\ProductCollection;
use App\Domain\Writer\ProductRepository;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\Assert\Assert;

final class SerializedProductRepository implements ProductRepository
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

    public function fetch(): \App\Domain\Model\Page
    {
        $resource = fopen($this->filepath, 'r');
        Assert::true(is_resource($resource));

        return new Page($resource);
    }

}