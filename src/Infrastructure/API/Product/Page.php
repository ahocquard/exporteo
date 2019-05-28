<?php

declare(strict_types=1);

namespace App\Infrastructure\API\Product;

use App\Domain\Model\ApiFormatProductsList;
use \Akeneo\Pim\ApiClient\Pagination\Page as AkeneoClientPage;
use App\Domain\Model\Product\Product;
use App\Domain\Model\Product\ProductList;

final class Page implements \App\Domain\Model\Page
{
    /** @var ApiFormatProductsList */
    private $productList;

    /** @var AkeneoClientPage */
    private $akeneoCLientPage;

    public function __construct(AkeneoClientPage $akeneoCLientPage)
    {
        $this->akeneoCLientPage = $akeneoCLientPage;
        $this->productList = new ProductList();
        foreach ($akeneoCLientPage->getItems() as $item) {
            $this->productList = $this->productList->add(new Product($item['identifier'], $item['categories']));
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
