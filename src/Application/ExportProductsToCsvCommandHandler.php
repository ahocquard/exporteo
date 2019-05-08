<?php

declare(strict_types=1);

namespace App\Application;

use Akeneo\Pim\ApiClient\Pagination\PageInterface;
use App\Domain\Model\ApiFormatProductsList;
use App\Domain\Model\CsvFormatProduct;
use App\Domain\Model\CsvFormatProductsList;
use App\Domain\Query\GetApiFormatProductList;
use Concurrent\Task;
use League\Csv\Writer;
use Symfony\Component\Filesystem\Filesystem;
use function Concurrent\all;

final class ExportProductsToCsvCommandHandler
{
    /** @var GetApiFormatProductList */
    private $getApiFormatProductList;

    public function __construct(GetApiFormatProductList $getApiFormatProductList)
    {
        $this->getApiFormatProductList = $getApiFormatProductList;
    }

    // TODO: no coupling to the client as it's infra
    public function handle(ExportProductsToCsvCommand $command)
    {
        $productPage = $this->getApiFormatProductList->fetchByPage(
            $command->client(),
            $command->secret(),
            $command->username(),
            $command->password(),
            $command->uri()
        );

        $temporaryFilePath = $this->createTemporaryFile();

        $writer = Writer::createFromPath($temporaryFilePath);
        $writer->insertOne(['identifier', 'categories']);

        $tasks = [];

        $transformAndWriteToCSV = $this->transformAndWriteToCSV();
        if (!$productPage->hasNextPage()) {
            $transformAndWriteToCSV($productPage->productList(), $writer);
        }

        while($productPage->hasNextPage()) {
            $currentPage = $productPage;
            $productPage = $productPage->nextPage();
            $tasks[] = Task::async($transformAndWriteToCSV, $currentPage->productList(), $writer);
        }

        if (!empty($tasks)) {
            Task::await(all($tasks));
        }

        $filesystem = new Filesystem();
        $filesystem->copy($temporaryFilePath, $command->pathToExport());
        $filesystem->remove($temporaryFilePath);
    }

    private function transformAndWriteToCSV(): callable {
        return function(ApiFormatProductsList $productList, Writer $writer) {
            $products = $this->transformAsArray($productList);

            $writer->insertAll($products);

            $csvFormatProductList = new CsvFormatProductsList();
            $this->write($csvFormatProductList, '');
        };
    }

    private function transformAsArray(ApiFormatProductsList $productList): array
    {
        $products = new CsvFormatProductsList();
        foreach ($productList-> products() as $product) {
            $products = $products->add(new CsvFormatProduct($product->identifier(), $product->categories()));
        };

        return $products->toArray();
    }

    private function createTemporaryFile(): string
    {
        $filesystem = new Filesystem();
        return $filesystem->tempnam('/tmp', 'exporteo_json_products_');    }

    private function write(CsvFormatProductsList $csvFormatProductList, string $filepath): void
    {
    }
}
