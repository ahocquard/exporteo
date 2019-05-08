<?php

declare(strict_types=1);

namespace App\Infrastructure\API\Product;

use App\Domain\Model\ApiFormatProduct;
use App\Domain\Model\ApiFormatProductsList;
use \Akeneo\Pim\ApiClient\Pagination\Page as AkeneoClientPage;

final class Page implements \App\Domain\Model\Page
{
    /** @var ApiFormatProductsList */
    private $productList;

    /** @var AkeneoClientPage */
    private $akeneoCLientPage;

    public function __construct(AkeneoClientPage $akeneoCLientPage)
    {
        $this->akeneoCLientPage = $akeneoCLientPage;
        $this->productList = new ApiFormatProductsList();
        foreach ($akeneoCLientPage->getItems() as $item) {
            $this->productList = $this->productList->add(new ApiFormatProduct($item['identifier'], $item['categories']));
        }
    }

    public function productList(): ApiFormatProductsList
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
