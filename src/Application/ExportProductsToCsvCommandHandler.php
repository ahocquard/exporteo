<?php

declare(strict_types=1);

namespace App\Application;

use Akeneo\Pim\ApiClient\AkeneoPimClientBuilder;
use function Concurrent\all;
use Concurrent\Http\HttpClient;
use Concurrent\Http\HttpClientConfig;
use Concurrent\Task;
use Concurrent\Timer;
use Nyholm\Psr7\Factory\Psr17Factory;

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

        $attributePage = $client->getAttributeApi()->listPerPage(3);
        $tasks = [];
        do {
            var_dump('Request');

            $tasks[] = Task::async(function() use ($attributePage){
                $timer = new Timer(300);
                $timer->awaitTimeout();

                var_dump('Consumer');
                $timer->awaitTimeout();
            });

        } while ($attributePage->hasNextPage() && $attributePage = $attributePage->getNextPage());

        Task::await(all($tasks));
    }
}
