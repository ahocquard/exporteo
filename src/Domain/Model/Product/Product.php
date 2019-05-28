<?php

declare(strict_types=1);

namespace App\Domain\Model\Product;

use App\Domain\Model\ApiFormatProduct;

final class Product
{
    /** @var string */
    private $identifier;

    /** @var string[] */
    private $categories;

    /** @var ValueList[] */
    private $values;

    /**
     * @param string   $identifier
     * @param string[] $categories
     */
    public function __construct(string $identifier, array $categories, ValueList $values)
    {
        $this->identifier = $identifier;
        $this->categories = (function(string ...$categories) {
            return $categories;
        })(...$categories);
        $this->values = $values;
    }

    public function toArray(): array
    {
        $array = [
            'identifier' => $this->identifier,
            'categories' => implode(',', $this->categories)
        ];

        $array = array_merge($array, $this->values->toArray());
        ksort($array);

        return $array;
    }

    public function headers(): array
    {
        $headers = ['identifier', 'categories'];
        return array_merge($headers, $this->values->headers());
    }
}
