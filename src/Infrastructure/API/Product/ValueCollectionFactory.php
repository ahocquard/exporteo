<?php

declare(strict_types=1);

namespace App\Infrastructure\API\Product;

use Akeneo\Pim\ApiClient\Api\AttributeApiInterface;
use App\Domain\Model\Product\Value\TextValue;
use App\Domain\Model\Product\ValueList;

final class ValueCollectionFactory
{
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
                if (in_array($attribute['type'], ['pim_catalog_text', 'pim_catalog_textarea'])) {
                    $values[] = new TextValue($attributeCode, $value['locale'], $value['scope'], $value['data']);
                }
            }
        }

        return new ValueList(...$values);
    }
}