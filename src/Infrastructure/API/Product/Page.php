<?php

declare(strict_types=1);

namespace App\Infrastructure\API\Product;

use \Akeneo\Pim\ApiClient\Pagination\Page as AkeneoClientPage;
use App\Domain\Model\Product\Product;
use App\Domain\Model\Product\ProductList;
use App\Domain\Model\Product\ValueList;

final class Page implements \App\Domain\Model\Page
{
    /** @var ProductList */
    private $productList;

    /** @var AkeneoClientPage */
    private $akeneoCLientPage;

    public function __construct(AkeneoClientPage $akeneoClientPage)
    {
        $this->akeneoCLientPage = $akeneoClientPage;
        $this->productList = new ProductList();
        foreach ($akeneoClientPage->getItems() as $item) {
            $this->productList = $this->productList->add(new Product($item['identifier'], $item['categories'], new ValueList()));
        }
    }

    public function productList(): ProductList
    {
        return $this->productList;
    }

    public function nextPage(): \App\Domain\Model\Page
    {
        return new self($this->akeneoCLientPage->getNextPage());
    }

    public function hasNextPage(): bool
    {
        return $this->akeneoCLientPage->hasNextPage();
    }
}
