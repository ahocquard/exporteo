<?php

declare(strict_types=1);

namespace App\Infrastructure\API\Product;

use App\Domain\Model\Product\Value\ScalarValue;
use App\Domain\Model\Product\ValueList;

final class ValueCollectionFactory
{
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
            foreach ($valuesForAttribute as $value) {
                if (is_scalar($value['data']) || $value['data'] === null) {
                    $values[] = new ScalarValue($attributeCode, $value['locale'], $value['scope'], $value['data']);
                }
            }

        }

        return new ValueList(...$values);
    }
}
