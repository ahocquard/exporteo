<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Api\Product;

use Akeneo\Pim\ApiClient\Api\AttributeApiInterface;
use App\Domain\Model\Product\Value\MetricValue;
use App\Domain\Model\Product\Value\ScalarCollectionValue;
use App\Domain\Model\Product\Value\PriceValue;
use App\Domain\Model\Product\Value\ScalarValue;
use App\Domain\Model\Product\ValueCollection;

final class ValueCollectionFactory
{
    private const SCALAR_TYPES = [
        'pim_catalog_text',
        'pim_catalog_textarea',
        'pim_catalog_file',
        'pim_catalog_image',
        'pim_catalog_date',
        'pim_catalog_simpleselect',
        'pim_catalog_reference_data_simpleselect',
        'pim_catalog_number',
        'pim_catalog_boolean',
        'reference_entity_simple_select',
    ];

    private const COLLECTION_TYPES = [
        'pim_catalog_multiselect',
        'pim_catalog_reference_data_multiselect',
        'pim_assets_collection',
        'reference_entity_multi_select',
    ];

    private const PRICE_TYPE = 'pim_catalog_price';

    private const METRIC_TYPE = 'pim_catalog_metric';

    private $cache = [];

    /** @var AttributeApiInterface */
    private $attributeApiClient;

    public function __construct(AttributeApiInterface $attributeApiClient)
    {
        $this->attributeApiClient = $attributeApiClient;
    }

    /**
     * @param array $apiFormatValues
     * [
     *     'attribute_code' => [
     *          [
     *              'locale' => 'fr_FR',
     *              'scope' => 'tablet',
     *              'data' => 'foo'
     *          ]
     *      ]
     * ]
     *
     *
     * @return ValueCollection
     */
    public function fromApiFormat(array $apiFormatValues): ValueCollection
    {
        $values = [];
        foreach ($apiFormatValues as $attributeCode => $valuesForAttribute) {
            // TODO: use dedicated query (don't mock what you don't own)
            $attribute = $this->cache[$attributeCode] ?? $this->attributeApiClient->get($attributeCode);

            $this->cache[$attribute['code']] = $attribute;

            foreach ($valuesForAttribute as $value) {
                if (in_array($attribute['type'], self::SCALAR_TYPES)) {
                    $values[] = new ScalarValue($attributeCode, $value['locale'], $value['scope'], $value['data']);
                }
                else if (in_array($attribute['type'], self::COLLECTION_TYPES)) {
                    $values[] = new ScalarCollectionValue($attributeCode, $value['locale'], $value['scope'], $value['data']);
                }
                else if ($attribute['type'] === self::PRICE_TYPE) {
                    $values[] = new PriceValue($attributeCode, $value['locale'], $value['scope'], $value['data']);
                }
                else if ($attribute['type'] === self::METRIC_TYPE) {
                    $values[] = new MetricValue($attributeCode, $value['locale'], $value['scope'], $value['data']);
                }
            }
        }

        return new ValueCollection(...$values);
    }
}
