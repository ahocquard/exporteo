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

        $flatFormatTemporaryFilepath = $this->createTemporaryFile();

        $writer->insertOne(['identifier', 'categories']);

        $tasks = [];

        $transformAndWriteToCSV = $this->transformAndWriteToCSV();
        if (!$productPage->hasNextPage()) {
            $transformAndWriteToCSV($productPage->productList(), $writer, $flatFormatTemporaryFilepath);
        }

        while($productPage->hasNextPage()) {
            $currentPage = $productPage;
            $productPage = $productPage->nextPage();
            $tasks[] = Task::async($transformAndWriteToCSV, $currentPage->productList(), $writer, $flatFormatTemporaryFilepath);
        }

        if (!empty($tasks)) {
            Task::await(all($tasks));
        }

        $filesystem = new Filesystem();
        $filesystem->copy($temporaryFilePath, $command->pathToExport());
        $filesystem->remove($temporaryFilePath);
    }

    private function transformAndWriteToCSV(): callable {
        return function(ApiFormatProductsList $productList, Writer $writer, string $flatFormatWriter) {
            $products = CsvFormatProductsList::fromApiFormatProductList($productList);
            $writer->insertAll($products->toArray());

            $csvFormatProductList = new CsvFormatProductsList();
            $this->write($csvFormatProductList, '');
        };
    }

    private function createTemporaryFile(): string
    {
        $filesystem = new Filesystem();

        return $filesystem->tempnam('/tmp', 'exporteo_json_products_');
    }

    private function write(CsvFormatProductsList $csvFormatProductList, string $filepath): void
    {
    }
}
