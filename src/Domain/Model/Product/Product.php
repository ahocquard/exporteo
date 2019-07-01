<?php

declare(strict_types=1);

namespace App\Domain\Model\Product;

final class Product
{
    /** @var string */
    private $identifier;

    /** @var null|string */
    private $familyCode;

    /** @var null|string */
    private $parentProductModelCode;

    /** @var string[] */
    private $categoryCodes;

    /** @var string[] */
    private $groupCodes;

    /** @var bool */
    private $enabled;

    /** @var ValueCollection[] */
    private $values;

    public function __construct(
        string $identifier,
        ?string $familyCode,
        ?string $parentProductModelCode,
        array $groupCodes,
        array $categoryCodes,
        bool $enabled,
        ValueCollection $values
    ){
        $this->identifier = $identifier;
        $this->familyCode = $familyCode;
        $this->parentProductModelCode = $parentProductModelCode;
        $this->groupCodes = (function(string ...$groupCode) {
            return $groupCode;
        })(...$groupCodes);
        $this->categoryCodes = (function(string ...$categories) {
            return $categories;
        })(...$categoryCodes);
        $this->enabled = $enabled;
        $this->values = $values;
    }

    public function toArray(): array
    {
        $properties = $this->values->toArray();
        $properties['identifier'] = $this->identifier;
        $properties['categories'] = implode(',', $this->categoryCodes);
        $properties['enabled'] = $this->enabled;
        $properties['groups'] = implode(',', $this->groupCodes);
        $properties['parent'] = $this->parentProductModelCode;
        $properties['family'] = $this->familyCode;

        ksort($properties);

        return $properties;
    }

    public function headers(): array
    {
        $headers = ['identifier', 'categories', 'enabled', 'groups', 'parent', 'family'];

        return array_merge($headers, $this->values->headers());
    }
}
