<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\Application\ExportProductsToCsvCommand;
use App\Application\ExportProductsToCsvCommandHandler;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Concurrent\Http\HttpServer;
use Concurrent\Http\HttpServerConfig;
use Concurrent\Network\TcpServer;
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

    /** @var KernelInterface */
    private $kernel;

    /** @var ExportProductsToCsvCommandHandler */
    private $exportProductsToCsvCommandHandler;

    public function __construct(KernelInterface $kernel, ExportProductsToCsvCommandHandler $exportProductsToCsvCommandHandler)
    {
        $this->kernel = $kernel;
        $this->exportProductsToCsvCommandHandler = $exportProductsToCsvCommandHandler;
    }

    /**
     * @Given /^the product big_boot categorized in summer_collection and winter_boots$/
     */
    public function theProductBig_bootCategorizedInSummer_collectionAndWinter_boots()
    {
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
     * @Given /^another product docks_red categorized in winter_collection$/
     */
    public function anotherProductDocks_redCategorizedInWinter_collection()
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

        $this->exportProductsToCsvCommandHandler->handle($command);
    }

    /**
     * @Then /^I have the following file:$/
     */
    public function iHaveTheFollowingFile(PyStringNode $expectedContent)
    {
        $path = $this->kernel->getProjectDir() . '/var/test-files/export_categories.csv';
        Assert::true(file_exists($path), "No generated file '$path'");
        $file = file_get_contents($path);

        Assert::same($file, $expectedContent->getRaw());
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