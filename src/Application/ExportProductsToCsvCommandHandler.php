<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Model\Product\ProductCollection;
use App\Domain\Model\ExportHeaders;
use App\Domain\Query\GetProductList;
use App\Domain\Writer\TemporaryProductStorage;
use App\Domain\Writer\TemporaryProductStorageFactory;
use Concurrent\Task;
use League\Csv\Writer;
use Symfony\Component\Filesystem\Filesystem;
use function Concurrent\all;

final class ExportProductsToCsvCommandHandler
{
    /** @var GetProductList */
    private $getApiFormatProductList;

    /** @var TemporaryProductStorageFactory */
    private $productRepositoryFactory;

    public function __construct(GetProductList $getApiFormatProductList, TemporaryProductStorageFactory $productRepositoryFactory)
    {
        $this->getApiFormatProductList = $getApiFormatProductList;
        $this->productRepositoryFactory = $productRepositoryFactory;
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
        $temporaryProductStorage = $this->productRepositoryFactory->create();

        $tasks = [];

        $transformAndWriteToCSV = $this->transformAndWriteToCSV();
        if (!$productPage->hasNextPage()) {
            $transformAndWriteToCSV($productPage->productList(), $temporaryProductStorage, $headers);
            $this->createCsvFile($temporaryProductStorage, $headers, $command->pathToExport());

            return;
        }

        while($productPage->hasNextPage()) {
            $transformAndWriteToCSV($productPage->productList(), $temporaryProductStorage, $headers);

            $productPage = $productPage->nextPage();

        }

        $this->createCsvFile($temporaryProductStorage, $headers, $command->pathToExport());
    }

    private function transformAndWriteToCSV(): callable {
        return function(ProductCollection $productCollection, TemporaryProductStorage $productRepository, ExportHeaders $headers) {
            $headers->addHeaders(...$productCollection->headers());
            $productRepository->persist($productCollection);
        };
    }

    /**
     * TODO: put it in a service
     */
    private function createCsvFile(TemporaryProductStorage $productRepository, ExportHeaders $exportHeaders, string $pathToExport): void
    {
        $filesystem = new Filesystem();
        $temporaryFilePath = $filesystem->tempnam('/tmp', 'exporteo_json_products_');

        $writer = Writer::createFromPath($temporaryFilePath);
        $writer->insertOne($exportHeaders->headers());

        $page = $productRepository->fetch();
        do {
            $writer->insertAll($page->productList()->toArray($exportHeaders));

        } while ($page->hasNextPage() && $page = $page->nextPage());


        $filesystem->copy($temporaryFilePath, $pathToExport);
        $filesystem->remove($temporaryFilePath);
    }
}
