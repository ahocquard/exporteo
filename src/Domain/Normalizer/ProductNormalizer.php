<?php

declare(strict_types=1);

namespace App\Domain\Normalizer;

use App\Domain\Model\ApiFormatProduct;

final class ProductNormalizer
{
    public function toFlatFormat(ApiFormatProduct $product)
    {
        return [
            'identifier' => $product->identifier(),
            'categories' => implode(',', $product->categories())
        ];
    }
}
