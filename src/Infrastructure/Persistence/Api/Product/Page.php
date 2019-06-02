<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Api\Product;

use \Akeneo\Pim\ApiClient\Pagination\Page as AkeneoClientPage;
use App\Domain\Model\Product\Product;
use App\Domain\Model\Product\ProductCollection;

final class Page implements \App\Domain\Model\Page
{
    /** @var ProductCollection */
    private $productList;

    /** @var AkeneoClientPage */
    private $akeneoClientPage;

    /** @var ValueCollectionFactory */
    private $valueCollectionFactory;

    public function __construct(AkeneoClientPage $akeneoClientPage, ValueCollectionFactory $valueCollectionFactory)
    {
        $this->akeneoClientPage = $akeneoClientPage;
        $this->productList = new ProductCollection();
        $this->valueCollectionFactory = $valueCollectionFactory;

        foreach ($akeneoClientPage->getItems() as $item) {
            $this->productList = $this->productList->add(new Product($item['identifier'], $item['categories'], $this->valueCollectionFactory->fromApiFormat($item['values'])));
        }
    }

    public function productList(): ProductCollection
    {
        return $this->productList;
    }

    public function nextPage(): \App\Domain\Model\Page
    {
        return new self($this->akeneoClientPage->getNextPage(), $this->valueCollectionFactory);
    }

    public function hasNextPage(): bool
    {
        return $this->akeneoClientPage->hasNextPage();
    }
}
