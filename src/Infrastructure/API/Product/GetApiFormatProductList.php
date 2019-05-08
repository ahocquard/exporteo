<?php

declare(strict_types=1);

namespace App\Infrastructure\API\Product;

use Akeneo\Pim\ApiClient\AkeneoPimClientBuilder;
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
    public function fetchByPage(string $client, string $secret, string $username, string $password, string $uri): \App\Domain\Model\Page
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

        $page = $client->getProductApi()->listPerPage(100, true, ['pagination_type' => 'search_after']);

        return new Page($page);
    }
}
