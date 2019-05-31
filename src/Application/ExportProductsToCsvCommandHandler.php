<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Model\Product\ProductList;
use App\Domain\Model\ExportHeaders;
use App\Domain\Query\GetProductList;
use Concurrent\Task;
use League\Csv\Writer;
use Symfony\Component\Filesystem\Filesystem;
use function Concurrent\all;

final class ExportProductsToCsvCommandHandler
{
    /** @var GetProductList */
    private $getApiFormatProductList;

    public function __construct(GetProductList $getApiFormatProductList)
    {
        $this->getApiFormatProductList = $getApiFormatProductList;
    }

    public function handle(ExportProductsToCsvCommand $command)
    {
        $productPage = $this->getApiFormatProductList->fetchByPage(
            $command->client(),
            $command->secret(),
            $command->username(),
            $command->password(),
            $command->uri()
        );

        $headers = new ExportHeaders();
        $flatFormatTemporaryFilepath = $this->createTemporaryFile();

        $tasks = [];

        $transformAndWriteToCSV = $this->transformAndWriteToCSV();
        if (!$productPage->hasNextPage()) {
            $transformAndWriteToCSV($productPage->productList(), $flatFormatTemporaryFilepath, $headers);
            $this->createCsvFile($flatFormatTemporaryFilepath, $headers, $command->pathToExport());

            return;
        }

        while($productPage->hasNextPage()) {
            $currentPage = $productPage;
            $productPage = $productPage->nextPage();
            $tasks[] = Task::async($transformAndWriteToCSV, $currentPage->productList(), $flatFormatTemporaryFilepath, $headers);
        }

        if (!empty($tasks)) {
            Task::await(all($tasks));
        }

        $this->createCsvFile($flatFormatTemporaryFilepath, $headers, $command->pathToExport());
    }

    private function transformAndWriteToCSV(): callable {
        return function(ProductList $productList, string $flatFormatWriter, ExportHeaders $headers) {
            $filesystem = new Filesystem();

            $headers->addHeaders(...$productList->headers());
            $filesystem->appendToFile($flatFormatWriter, serialize($productList) . PHP_EOL);
        };
    }

    private function createTemporaryFile(): string
    {
        $filesystem = new Filesystem();

        return $filesystem->tempnam('/tmp', 'exporteo_json_products_');
    }

    private function createCsvFile(string $flatFormatTemporaryFilepath, ExportHeaders $exportHeaders, string $pathToExport): void
    {
        $temporaryFilePath = $this->createTemporaryFile();
        $writer = Writer::createFromPath($temporaryFilePath);
        $writer->insertOne($exportHeaders->headers());

        $resource = fopen($flatFormatTemporaryFilepath, 'r');

        $serializedProducts = stream_get_line($resource, 1000000, PHP_EOL);
        while (false !== $serializedProducts) {
            $products = $this->unserializeProducts($serializedProducts);
            $writer->insertAll($products->toArray($exportHeaders));

            $serializedProducts = stream_get_line($resource, 1000000, PHP_EOL);
        }

        $filesystem = new Filesystem();
        $filesystem->copy($temporaryFilePath, $pathToExport);
        $filesystem->remove($temporaryFilePath);
        $filesystem->remove($flatFormatTemporaryFilepath);

    }

    private function unserializeProducts(string $serializedProduct): ProductList
    {
        return unserialize($serializedProduct);
    }
}
