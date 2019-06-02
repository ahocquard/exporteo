<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Api\Product;

use Akeneo\Pim\ApiClient\Api\AttributeApiInterface;
use App\Domain\Model\Product\Value\ArrayValue;
use App\Domain\Model\Product\Value\ScalarValue;
use App\Domain\Model\Product\ValueList;

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
     * @return ValueList
     */
    public function fromApiFormat(array $apiFormatValues): ValueList
    {
        $values = [];
        foreach ($apiFormatValues as $attributeCode => $valuesForAttribute) {
            // TODO: use dedicated query (don't mock what you don't own)
            $attribute = $this->attributeApiClient->get($attributeCode);

            foreach ($valuesForAttribute as $value) {
                if (in_array($attribute['type'], self::SCALAR_TYPES)) {
                    $values[] = new ScalarValue($attributeCode, $value['locale'], $value['scope'], $value['data']);
                }
                else if (in_array($attribute['type'], self::COLLECTION_TYPES)) {
                    $values[] = new ArrayValue($attributeCode, $value['locale'], $value['scope'], $value['data']);
                }
            }
        }

        return new ValueList(...$values);
    }
}
