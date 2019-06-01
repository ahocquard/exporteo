<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Model;

use App\Domain\Model\Product\Product;
use App\Domain\Model\Product\ProductList;
use App\Domain\Model\Product\ValueList;
use App\Infrastructure\API\Product\GetProductList;
use Concurrent\Http\HttpServer;
use Concurrent\Http\HttpServerConfig;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Concurrent\Network\TcpServer;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GetProductListIntegrationTest extends KernelTestCase
{
    protected function setUp(): void
    {
        $this->createServer();
        self::bootKernel();
    }

    public function test_it_get_connector_products(): void
    {
        /** @var \App\Domain\Query\GetProductList $getProducts */
        $getProducts = static::$container->get(GetProductList::class);
        $page = $getProducts->fetchByPage('client', 'secret', 'admin', 'admin', 'http://127.0.0.1:8081');

        //var_dump($page);
        Assert::assertEqualsCanonicalizing(new ProductList(
            new Product('big_boot', ['summer_collection', 'winter_boots'], new ValueList()),
            new Product('docks_red', ['winter_collection'], new ValueList()),
            new Product('small_boot', [], new ValueList()),

        ), $page->productList());
    }

    private function createServer(): void
    {
        $factory = new Psr17Factory();
        $server = new HttpServer(new HttpServerConfig($factory, $factory));

        $handler = new class($factory, $this) implements RequestHandlerInterface {
            private $factory;

            private $test;

            public function __construct(ResponseFactoryInterface $factory, TestCase $test) {
                $this->factory = $factory;
                $this->test = $test;
            }

            public function handle(ServerRequestInterface $request): ResponseInterface {
                $path = rtrim(urldecode(preg_replace("'\?.*$'", '', $request->getRequestTarget())), '/');
                if ($path === '/api/oauth/v1/token') {
                    $header = $request->getHeader('Authorization')[0] ?? '';
                    $body = json_decode($request->getBody()->getContents(), true);

                    $expectedBody = [
                        'grant_type' => 'password',
                        'username' => 'admin',
                        'password' => 'admin'
                    ];

                    if ($expectedBody === $body && 'Basic Y2xpZW50OnNlY3JldA==' === $header) {
                        $response = $this->factory->createResponse();
                        $response = $response->withHeader('Content-Type', 'application/json');
                        $response = $response->withBody($this->factory->createStream($this->test->getAuthenticatedJson()));

                        return $response;
                    }
                }

                if ($path == '/api/rest/v1/products') {
                    $response = $this->factory->createResponse();
                    $response = $response->withHeader('Content-Type', 'application/json');
                    $response = $response->withBody($this->factory->createStream($this->test->getFirstPage('http://127.0.0.1:8081')));

                    return $response;
                }

                return $this->factory->createResponse(404);
            }
        };

        $server->run(TcpServer::listen('127.0.0.1', 8081), $handler);
    }

    public function getAuthenticatedJson(): string
    {
        return <<<JSON
            {
                "refresh_token" : "this-is-a-refresh-token",
                "access_token" : "this-is-an-access-token"
            }
JSON;
    }


    public function getFirstPage(string $baseUri)
    {
        return <<<JSON
        {
        	"_links": {
        		"self": {
        			"href": "$baseUri\/api\/rest\/v1\/products?page=1&with_count=true&pagination_type=page&limit=10"
        		},
        		"first": {
        			"href": "$baseUri\/api\/rest\/v1\/products?page=1&with_count=true&pagination_type=page&limit=10"
        		}
        	},
        	"current_page": 1,
        	"items_count": 11,
        	"_embedded": {
        		"items": [{
        				"_links": {
        					"self": {
        						"href": "$baseUri\/api\/rest\/v1\/products\/big_boot"
        					}
        				},
        				"identifier": "big_boot",
        				"family": "boots",
        				"groups": [
        					"similar_boots"
        				],
        				"categories": [
        					"summer_collection",
        					"winter_boots"
        				],
        				"enabled": true,
        				"values": {
        					"color": [{
        						"locale": null,
        						"scope": null,
        						"data": "black"
        					}]
        				}
        			},
        			{
        				"_links": {
        					"self": {
        						"href": "$baseUri\/api\/rest\/v1\/products\/docks_red"
        					}
        				},
        				"identifier": "docks_red",
        				"family": "boots",
        				"groups": [
        					"caterpillar_boots"
        				],
        				"categories": [
        					"winter_collection"
        				],
        				"enabled": true,
        				"values": {}
        			},
        			{
        				"_links": {
        					"self": {
        						"href": "$baseUri\/api\/rest\/v1\/products\/small_boot"
        					}
        				},
        				"identifier": "small_boot",
        				"family": "boots",
        				"groups": [
        					"similar_boots"
        				],
        				"categories": [],
        				"enabled": true,
        				"values": {}
        			}
        		]
        	}
        }
JSON;
    }
}
