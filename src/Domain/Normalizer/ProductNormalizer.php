<?php

declare(strict_types=1);

namespace App\Domain\Normalizer;

use App\Domain\Model\Product;

final class ProductNormalizer
{
    public function toFlatFormat(Product $product)
    {
        return [
            'identifier' => $product->identifier(),
            'categories' => implode(',', $product->categories())
        ];
    }
}
