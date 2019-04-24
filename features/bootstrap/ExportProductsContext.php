<?php
/**
 * @copyright 2019 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

use Akeneo\Pim\ApiClient\Api\ProductApi;
use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use donatj\MockWebServer\MockWebServer;
use donatj\MockWebServer\Response;
use donatj\MockWebServer\ResponseStack;
use Symfony\Component\HttpKernel\KernelInterface;
use Webmozart\Assert\Assert;

class ExportProductsContext implements Context
{
    const TOKEN_URI = 'api/oauth/v1/token';
    const PRODUCTS_URI = 'api/rest/v1/products';

    /** @var MockWebServer */
    private $server;

    /**
     * @var KernelInterface
     */
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @Given /^the product big_boot categorized in summer_collection and winter_boots$/
     */
    public function theProductBig_bootCategorizedInSummer_collectionAndWinter_boots()
    {
        $this->server = new MockWebServer(8081, '127.0.0.1');
        $this->server->start();

        $this->server->setResponseOfPath(
            '/'. self::TOKEN_URI,
            new ResponseStack(
                new Response($this->getAuthenticatedJson())
            )
        );

        $this->server->setResponseOfPath(
            '/'. self::PRODUCTS_URI,
            new ResponseStack(
                new Response($this->getFirstPage(), [], 200)
            )
        );
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

        Assert::same($expectedFile, $file);
    }


    private function getAuthenticatedJson()
    {
        return <<<JSON
            {
                "refresh_token" : "this-is-a-refresh-token",
                "access_token" : "this-is-an-access-token"
            }
JSON;
    }


    private function getFirstPage()
    {
        $baseUri = $this->server->getServerRoot();

        return <<<JSON
        {
            "_links":{
                "self":{
                  "href": "$baseUri\/api\/rest\/v1\/products?page=1&with_count=true&pagination_type=page&limit=10"
                },
                "first":{
                  "href": "$baseUri\/api\/rest\/v1\/products?page=1&with_count=true&pagination_type=page&limit=10"
                },
                "next":{
                  "href": "$baseUri\/api\/rest\/v1\/products?page=2&with_count=true&pagination_type=page&limit=10"
                }
            },
            "current_page": 1,
            "items_count": 11,
            "_embedded": {
                "items": [
                  {
                    "_links":{
                      "self":{
                        "href": "$baseUri\/api\/rest\/v1\/products\/big_boot"
                      }
                    },
                    "identifier":"big_boot",
                    "family":"boots",
                    "groups":[
                      "similar_boots"
                    ],
                    "categories":[
                      "summer_collection",
                      "winter_boots"
                    ],
                    "enabled":true,
                    "values":{
                      "color":[
                        {
                          "locale":null,
                          "scope":null,
                          "data":"black"
                        }
                      ]
                    }
                  },
                  {
                    "_links":{
                      "self":{
                        "href": "$baseUri\/api\/rest\/v1\/products\/docks_red"
                      }
                    },
                    "identifier":"docks_red",
                    "family":"boots",
                    "groups":[
                      "caterpillar_boots"
                    ],
                    "categories":[
                      "winter_collection"
                    ],
                    "enabled":true,
                    "values":{
                      "color":[
                        {
                          "locale":null,
                          "scope":null,
                          "data":"red"
                        }
                      ]
                    }
                  },
                  {
                    "_links":{
                      "self":{
                        "href":"$baseUri\/api\/rest\/v1\/products\/small_boot"
                      }
                    },
                    "identifier":"small_boot",
                    "family":"boots",
                    "groups":[
                      "similar_boots"
                    ],
                    "categories":[
                      "summer_collection",
                      "winter_boots",
                      "winter_collection"
                    ],
                    "enabled":true,
                    "values":{
                      "color":[
                        {
                          "locale":null,
                          "scope":null,
                          "data":"maroon"
                        }
                      ]
                    }
                  },

        }
JSON;
    }
}
