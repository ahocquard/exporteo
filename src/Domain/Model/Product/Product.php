<?php

declare(strict_types=1);

namespace App\Domain\Model\Product;

use App\Domain\Model\ExportHeaders;

final class Product
{
    /** @var string */
    private $identifier;

    /** @var string[] */
    private $categories;

    /** @var ValueCollection[] */
    private $values;

    /**
     * @param string   $identifier
     * @param string[] $categories
     */
    public function __construct(string $identifier, array $categories, ValueCollection $values)
    {
        $this->identifier = $identifier;
        $this->categories = (function(string ...$categories) {
            return $categories;
        })(...$categories);
        $this->values = $values;
    }

    public function toArray(ExportHeaders $headers): array
    {
        $properties = [
            'identifier' => $this->identifier,
            'categories' => implode(',', $this->categories)
        ];

        $properties = array_merge($headers->headersIndexedByKey(), $properties, $this->values->toArray());
        ksort($properties);

        return $properties;
    }

    public function headers(): array
    {
        $headers = ['identifier', 'categories'];
        return array_merge($headers, $this->values->headers());
    }
}
