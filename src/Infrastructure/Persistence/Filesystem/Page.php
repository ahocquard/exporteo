<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Filesystem;

use App\Domain\Model\Product\ProductCollection;
use Webmozart\Assert\Assert;

class Page implements \App\Domain\Model\Page
{
    /** @var resource */
    private $resource;

    /** @var ProductCollection */
    private $products;

    public function __construct($resource)
    {
        Assert::true(is_resource($resource));
        $this->resource = $resource;
        $serializedProducts = stream_get_line($this->resource, 1000000, PHP_EOL);
        $this->products = $serializedProducts === false ? new ProductCollection() : $this->unserializeProducts($serializedProducts);
    }

    public function productList(): ProductCollection
    {
        return $this->products;
    }

    public function nextPage(): \App\Domain\Model\Page
    {
        return new Page($this->resource);
    }

    public function hasNextPage(): bool
    {
        return count($this->products->products()) > 0;
    }

    private function unserializeProducts(string $serializedProduct): ProductCollection
    {
        return unserialize($serializedProduct);
    }
}