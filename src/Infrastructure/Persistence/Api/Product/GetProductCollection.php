<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Api\Product;

use Akeneo\Pim\ApiClient\AkeneoPimClientBuilder;
use Concurrent\Http\HttpClient;
use Concurrent\Http\HttpClientConfig;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

final class GetProductCollection implements \App\Domain\Query\GetProductList
{
    /** @var RequestFactoryInterface */
    private $requestFactory;

    /** @var StreamFactoryInterface */
    private $streamFactory;

    /** @var ResponseFactoryInterface */
    private $responseFactory;

    public function __construct(
        RequestFactoryInterface $requestFactory,
        StreamFactoryInterface $streamFactory,
        ResponseFactoryInterface $responseFactory
    ) {
        $this->requestFactory = $requestFactory;
        $this->streamFactory = $streamFactory;
        $this->responseFactory = $responseFactory;
    }

    // TODO: use env variables
    public function fetchByPage(string $client, string $secret, string $username, string $password, string $uri): \App\Domain\Model\Page
    {
        $akeneoClientBuilder = new AkeneoPimClientBuilder($uri);

        $client = $akeneoClientBuilder->buildAuthenticatedByPassword(
            $client,
            $secret,
            $username,
            $password
        );

        $page = $client->getProductApi()->listPerPage(100, true, ['pagination_type' => 'search_after']);
        $valueCollectionFactory = new ValueCollectionFactory($client->getAttributeApi());

        return new Page($page, $valueCollectionFactory);
    }
}
