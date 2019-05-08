<?php

declare(strict_types=1);

namespace App\Domain\Model;

final class CsvFormatProduct
{
    /** @var string */
    private $identifier;

    /** @var string[] */
    private $categories;

    /**
     * @param string   $identifier
     * @param string[] $categories
     */
    public function __construct(string $identifier, array $categories)
    {
        $this->identifier = $identifier;
        $this->categories = (function(string ...$categories) {
            return $categories;
        })(...$categories);
    }

    public static function fromApiFormatProduct(ApiFormatProduct $product)
    {
        return new self($product->identifier(), $product->categories());
    }

    public function toArray(): array
    {
        return [
            'identifier' => $this->identifier,
            'categories' => implode(',', $this->categories)
        ];
    }

    public function headers(): array
    {
        return ['identifier', 'categories'];
    }
}
