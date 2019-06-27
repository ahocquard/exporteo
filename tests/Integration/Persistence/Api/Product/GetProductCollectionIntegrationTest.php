<?php

declare(strict_types=1);

namespace App\Tests\Integration\Persistence\Api\Product;

use App\Domain\Model\Product\Product;
use App\Domain\Model\Product\ProductCollection;
use App\Domain\Model\Product\Value\ScalarValue;
use App\Domain\Model\Product\ValueCollection;
use App\Infrastructure\Persistence\Api\Product\GetProductCollection;
use donatj\MockWebServer\MockWebServer;
use donatj\MockWebServer\Response;
use donatj\MockWebServer\ResponseStack;
use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GetProductCollectionIntegrationTest extends KernelTestCase
{
    /** @var MockWebServer */
    protected $server;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->server = new MockWebServer(8081, '127.0.0.1');

        $this->server->start();
        $this->server->setResponseOfPath(
            '/api/oauth/v1/token',
            new ResponseStack(
                new Response($this->getAuthenticatedJson())
            )
        );

        $this->server->setResponseOfPath(
            '/api/rest/v1/products',
            new ResponseStack(
                new Response($this->getFirstProductPage('http://127.0.0.1:8081'))
            )
        );

        $this->server->setResponseOfPath(
            '/api/rest/v1/attributes/color',
            new ResponseStack(
                new Response($this->getColorAttribute())
            )
        );

        $this->server->setResponseOfPath(
            '/api/rest/v1/attributes/name',
            new ResponseStack(
                new Response($this->getNameAttribute())
            )
        );
    }

    protected function tearDown(): void
    {
        $this->server->stop();
        parent::tearDown();
    }

    public function test_it_get_connector_products(): void
    {
        /** @var \App\Domain\Query\GetProductCollection $getProducts */
        $getProducts = static::$container->get(GetProductCollection::class);
        $page = $getProducts->fetchByPage('client', 'secret', 'admin', 'admin', 'http://127.0.0.1:8081');

        Assert::assertEqualsCanonicalizing(new ProductCollection(
            new Product(
                'big_boot',
                ['summer_collection', 'winter_boots'],
                new ValueCollection(
                    new ScalarValue('color', null, null, 'black'),
                    new ScalarValue('name', null, null, 'Big boot'),
                )
            ),
            new Product('docks_red', ['winter_collection'], new ValueCollection()),
            new Product('small_boot', [], new ValueCollection()),

        ), $page->productList());
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


    public function getFirstProductPage(string $baseUri)
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
        					}],
                            "name": [{
        						"locale": null,
        						"scope": null,
        						"data": "Big boot"
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

    public function getColorAttribute()
    {
        return <<<JSON
            {
                "code": "color",
                "type": "pim_catalog_simpleselect",
                "group": "product",
                "unique": false,
                "s": true,
                "allowed_extensions": [],
                "metric_family": null,
                "default_metric_unit": null,
                "reference_data_name": null,
                "available_locales": [],
                "max_characters": null,
                "validation_rule": null,
                "validation_regexp": null,
                "wysiwyg_enabled": null,
                "number_min": null,
                "number_max": null,
                "decimals_allowed": null,
                "negative_allowed": null,
                "date_min": null,
                "date_max": null,
                "max_file_size": null,
                "minimum_input_length": null,
                "sort_order": 1,
                "localizable": false,
                "scopable": false,
                "labels": {
                    "de_DE": "Color",
                    "en_US": "Color",
                    "fr_FR": "Couleur"
                },
                "auto_option_sorting": false
            }
JSON;
    }

    public function getNameAttribute()
    {
        return <<<JSON
          {
            "code": "name",
            "type": "pim_catalog_text",
            "group": "marketing",
            "unique": false,
            "useable_as_grid_filter": true,
            "allowed_extensions": [],
            "metric_family": null,
            "default_metric_unit": null,
            "reference_data_name": null,
            "available_locales": [],
            "max_characters": null,
            "validation_rule": null,
            "validation_regexp": null,
            "wysiwyg_enabled": null,
            "number_min": null,
            "number_max": null,
            "decimals_allowed": null,
            "negative_allowed": null,
            "date_min": null,
            "date_max": null,
            "max_file_size": null,
            "minimum_input_length": null,
            "sort_order": 2,
            "localizable": false,
            "scopable": false,
            "labels": {
                "de_DE": "Name",
                "en_US": "Name",
                "fr_FR": "Nom"
            },
            "auto_option_sorting": null
        }
JSON;

    }
}
