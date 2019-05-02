<?php

use App\Application\ExportProductsToCsvCommand;
use App\Application\ExportProductsToCsvCommandHandler;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Concurrent\Http\HttpClient;
use Concurrent\Http\HttpClientConfig;
use Concurrent\Http\HttpServer;
use Concurrent\Http\HttpServerConfig;
use Concurrent\Http\TcpConnectionManager;
use Concurrent\Network\TcpServer;
use Concurrent\Task;
use donatj\MockWebServer\MockWebServer;
use donatj\MockWebServer\Response;
use donatj\MockWebServer\ResponseStack;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\HttpKernel\KernelInterface;
use Webmozart\Assert\Assert;

class ExportProductsContext implements Context
{
    const TOKEN_URI = 'api/oauth/v1/token';
    const PRODUCTS_URI = 'api/rest/v1/products';

    ///** @var MockWebServer */
    //private $server;

    /** @var KernelInterface */
    private $kernel;

    protected $logger;
    protected $manager;
    protected $factory;
    protected $client;
    protected $server;
    protected $address;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @Given /^the product big_boot categorized in summer_collection and winter_boots$/
     */
    public function theProductBig_bootCategorizedInSummer_collectionAndWinter_boots()
    {
        //$this->server = new MockWebServer(8081, '127.0.0.1');
        //$this->server->start();
        //$output = [];
        //$this->server->setResponseOfPath(
        //    '/'. self::TOKEN_URI,
        //    new ResponseStack(
        //        new Response($this->getAuthenticatedJson())
        //    )
        //);
        //
        //$this->server->setResponseOfPath(
        //    '/'. self::PRODUCTS_URI,
        //    new ResponseStack(
        //        new Response($this->getFirstPage(), [], 200)
        //    )
        //);

        $factory = new Psr17Factory();
        $server = new HttpServer(new HttpServerConfig($factory, $factory));

        $handler = new class($factory, $this) implements RequestHandlerInterface {
            private $factory;

            private $exportProductContext;

            public function __construct(ResponseFactoryInterface $factory, ExportProductsContext $exportProductsContext) {
                $this->factory = $factory;
                $this->exportProductContext = $exportProductsContext;
            }

            public function handle(ServerRequestInterface $request): ResponseInterface {

                $path = rtrim(urldecode(preg_replace("'\?.*$'", '', $request->getRequestTarget())), '/');
                if ($path == '/api/oauth/v1/token') {
                    $response = $this->factory->createResponse();
                    $response = $response->withHeader('Content-Type', 'application/json');
                    $response = $response->withBody($this->factory->createStream($this->exportProductContext->getAuthenticatedJson()));
                    return $response;
                }

                if ($path == '/api/rest/v1/products') {
                    $response = $this->factory->createResponse();
                    $response = $response->withHeader('Content-Type', 'application/json');
                    $response = $response->withBody($this->factory->createStream($this->exportProductContext->getFirstPage('http://127.0.0.1:8081')));

                    return $response;
                }

                return $this->factory->createResponse(404);
            }
        };

        $server->run(TcpServer::listen('127.0.0.1', 8081), $handler);
    }


    /**
     * @Given /^another product small_boot without any category$/
     */
    public function anotherProductSmall_bootWithoutAnyCategory()
    {
    }

    /**
     * @When /^I export these products from the API$/
     */
    public function iExportTheseProductsFromTheAPI()
    {
        $command = new ExportProductsToCsvCommand(
            'client',
            'secret',
            'username',
            'password',
            'http://127.0.0.1:8081/',
            $this->kernel->getProjectDir() . '/var/test-files/export_categories.csv'
        );
        $commandHandler = new ExportProductsToCsvCommandHandler();
        $commandHandler->handle($command);
    }

    /**
     * @Then /^I have the following file:$/
     */
    public function iHaveTheFollowingFile(PyStringNode $string)
    {
        $expectedFile = file_get_contents($this->kernel->getProjectDir() . '/features/expected-files/export_categories.csv');

        $path = $this->kernel->getProjectDir() . '/var/test-files/export_categories.csv';
        Assert::true(file_exists($path), "No generated file '$path'");
        $file = file_get_contents($path);

        Assert::same($file, $expectedFile);
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
        				"values": {
        					"color": [{
        						"locale": null,
        						"scope": null,
        						"data": "red"
        					}]
        				}
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
        				"values": {
        					"color": [{
        						"locale": null,
        						"scope": null,
        						"data": "maroon"
        					}]
        				}
        			}
        		]
        	}
        }
JSON;
    }
}
