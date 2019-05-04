<?php

declare(strict_types=1);

namespace App\Infrastructure\API\Product;

use Akeneo\Pim\ApiClient\AkeneoPimClientBuilder;
use Akeneo\Pim\ApiClient\Pagination\Page;
use Concurrent\Http\HttpClient;
use Concurrent\Http\HttpClientConfig;
use Nyholm\Psr7\Factory\Psr17Factory;

// TODO: to test
final class GetApiFormatProductList implements \App\Domain\Query\GetApiFormatProductList
{
    /** @var Psr17Factory TODO: split according to the 3 different interfaces */
    private $factory;

    // TODO: return an iterators
    public function __construct()
    {
        $this->factory = new Psr17Factory();
    }

    // TODO: use env variables
    // TODO return our own page
    public function fetchByPage(string $client, string $secret, string $username, string $password, string $uri): Page
    {
        $akeneoClientBuilder = new AkeneoPimClientBuilder($uri);

        $akeneoClientBuilder
            ->setHttpClient(new HttpClient(new HttpClientConfig($this->factory)))
            ->setRequestFactory($this->factory)
            ->setStreamFactory($this->factory);

        $client = $akeneoClientBuilder->buildAuthenticatedByPassword(
            $client,
            $secret,
            $username,
            $password
        );

        return $client->getProductApi()->listPerPage(100, true, ['pagination_type' => 'search_after']);
    }
}
