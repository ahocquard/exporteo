<?php

declare(strict_types=1);

namespace App\Application;

use Akeneo\Pim\ApiClient\AkeneoPimClientBuilder;
use Akeneo\Pim\ApiClient\Pagination\PageInterface;
use Concurrent\Http\HttpClient;
use Concurrent\Http\HttpClientConfig;
use Concurrent\Task;
use League\Csv\Writer;
use Nyholm\Psr7\Factory\Psr17Factory;
use function Concurrent\all;
use Symfony\Component\Filesystem\Filesystem;

final class ExportProductsToCsvCommandHandler
{
    // TODO: no coupling to the client as it's infra
    public function handle(ExportProductsToCsvCommand $command)
    {
        $factory = new Psr17Factory();
        $clientBuilder = new AkeneoPimClientBuilder($command->uri());

        $clientBuilder
            ->setHttpClient(new HttpClient(new HttpClientConfig($factory)))
            ->setRequestFactory($factory)
            ->setStreamFactory($factory);

        $client = $clientBuilder->buildAuthenticatedByPassword(
            $command->client(),
            $command->secret(),
            $command->username(),
            $command->password()
        );

        $filesystem = new Filesystem();
        $temporaryFilePath = $filesystem->tempnam(__DIR__. '/../../', 'exporteo_json_products_');
        $writer = Writer::createFromPath($temporaryFilePath);
        //$writer->setEnclosure(' ');
        $writer->insertOne(['identifier', 'categories']);

        $productPage = $client->getProductApi()->listPerPage(100);
        $tasks = [];

        while($productPage->hasNextPage()) {
            var_dump('request');

            $transformAndWriteToCSV = $this->transformAndWriteToCSV();
            $currentPage = $productPage;
            $productPage = $productPage->getNextPage();
            $tasks[] = Task::async($transformAndWriteToCSV, $currentPage, $writer);
        }

        if (!empty($tasks)) {
            Task::await(all($tasks));
        }

        $filesystem->copy($temporaryFilePath, $command->pathToExport());
    }

    private function transformAndWriteToCSV(): callable {
        return function(PageInterface $page, Writer $writer) {
            var_dump('response');
            $products = [];
            foreach ($page->getItems() as $item) {
                $products[] = [
                    $item['identifier'],
                    implode(',', $item['categories'])
                ];
            };
        };
    }
}
