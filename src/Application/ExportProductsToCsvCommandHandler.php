<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Model\Product\ProductCollection;
use App\Domain\Model\ExportHeaders;
use App\Domain\Query\GetProductList;
use App\Domain\Writer\ProductRepository;
use App\Domain\Writer\ProductRepositoryFactory;
use Concurrent\Task;
use League\Csv\Writer;
use Symfony\Component\Filesystem\Filesystem;
use function Concurrent\all;

final class ExportProductsToCsvCommandHandler
{
    /** @var GetProductList */
    private $getApiFormatProductList;

    /** @var ProductRepositoryFactory */
    private $productRepositoryFactory;

    public function __construct(GetProductList $getApiFormatProductList, ProductRepositoryFactory $productRepositoryFactory)
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
        $productRepository = $this->productRepositoryFactory->create();

        $tasks = [];

        $transformAndWriteToCSV = $this->transformAndWriteToCSV();
        if (!$productPage->hasNextPage()) {
            $transformAndWriteToCSV($productPage->productList(), $productRepository, $headers);
            $this->createCsvFile($productRepository, $headers, $command->pathToExport());

            return;
        }

        while($productPage->hasNextPage()) {
            $currentPage = $productPage;
            $productPage = $productPage->nextPage();
            $tasks[] = Task::async($transformAndWriteToCSV, $currentPage->productList(), $productRepository, $headers);
        }

        if (!empty($tasks)) {
            Task::await(all($tasks));
        }

        $this->createCsvFile($productRepository, $headers, $command->pathToExport());
    }

    private function transformAndWriteToCSV(): callable {
        return function(ProductCollection $productCollection, ProductRepository $productRepository, ExportHeaders $headers) {
            $headers->addHeaders(...$productCollection->headers());
            $productRepository->persist($productCollection);
        };
    }

    private function createTemporaryFile(): string
    {
        $filesystem = new Filesystem();

        return $filesystem->tempnam('/tmp', 'exporteo_json_products_');
    }

    private function createCsvFile(ProductRepository $productRepository, ExportHeaders $exportHeaders, string $pathToExport): void
    {
        $temporaryFilePath = $this->createTemporaryFile();
        $writer = Writer::createFromPath($temporaryFilePath);
        $writer->insertOne($exportHeaders->headers());

        $page = $productRepository->fetch();
        do {
            $writer->insertAll($page->productList()->toArray($exportHeaders));

        } while ($page->hasNextPage() && $page = $page->nextPage());


        $filesystem = new Filesystem();
        $filesystem->copy($temporaryFilePath, $pathToExport);
        $filesystem->remove($temporaryFilePath);
    }
}
