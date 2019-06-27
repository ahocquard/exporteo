<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Model\Product\ProductCollection;
use App\Domain\Model\ExportHeaders;
use App\Domain\Query\GetProductCollection;
use App\Domain\Writer\TemporaryProductStorage;
use App\Domain\Writer\TemporaryProductStorageFactory;
use Concurrent\Task;
use League\Csv\Writer;
use Symfony\Component\Filesystem\Filesystem;
use function Concurrent\all;

final class ExportProductsToCsvCommandHandler
{
    /** @var GetProductCollection */
    private $getApiFormatProductList;

    /** @var TemporaryProductStorageFactory */
    private $productRepositoryFactory;

    public function __construct(GetProductCollection $getApiFormatProductList, TemporaryProductStorageFactory $productRepositoryFactory)
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

        $headers = ExportHeaders::empty();
        $temporaryProductStorage = $this->productRepositoryFactory->create();

        do {
            $headers = $headers->addHeaders(...$productPage->productList()->headers());
            $temporaryProductStorage->persist($productPage->productList());
        } while ($productPage->hasNextPage() && $productPage = $productPage->nextPage());

        $this->createCsvFile($temporaryProductStorage, $headers, $command->pathToExport());
    }

    /**
     * TODO: put it in a service
     */
    private function createCsvFile(TemporaryProductStorage $temporaryProductStorage, ExportHeaders $exportHeaders, string $pathToExport): void
    {
        $filesystem = new Filesystem();
        $temporaryFilePath = $filesystem->tempnam('/tmp', 'exporteo_json_products_');

        $writer = Writer::createFromPath($temporaryFilePath);
        $writer->insertOne($exportHeaders->headers());

        foreach ($temporaryProductStorage->fetch() as $product) {
            $writer->insertOne($product->toArray($exportHeaders));
        };

        $filesystem->copy($temporaryFilePath, $pathToExport);
        $filesystem->remove($temporaryFilePath);
    }
}
