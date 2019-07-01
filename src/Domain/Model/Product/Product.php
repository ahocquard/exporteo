<?php

declare(strict_types=1);

namespace App\Domain\Model\Product;

final class Product
{
    /** @var string */
    private $identifier;

    /** @var string[] */
    private $categories;

    /** @var ValueCollection[] */
    private $values;

    public function __construct(string $identifier, array $categories, ValueCollection $values)
    {
        $this->identifier = $identifier;
        $this->categories = (function(string ...$categories) {
            return $categories;
        })(...$categories);
        $this->values = $values;
    }

    public function toArray(): array
    {
        $properties = $this->values->toArray();
        $properties['identifier'] = $this->identifier;
        $properties['categories'] = implode(',', $this->categories);

        ksort($properties);

        return $properties;
    }

    public function headers(): array
    {
        $headers = ['identifier', 'categories'];

        return array_merge($headers, $this->values->headers());
    }
}
