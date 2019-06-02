<?php

declare(strict_types=1);

namespace App\Domain\Model\Product\Value;

final class PriceValue implements Value
{
    /** @var string */
    private $attributeCode;

    /** @var ?string */
    private $localeCode;

    /** @var ?string */
    private $channelCode;

    /** @var mixed */
    private $data;

    /** @var string */
    private $headers;

    private $dataAsArray;

    public function __construct(string $attributeCode, ?string $localeCode, ?string$channelCode, $data)
    {
        $this->attributeCode = $attributeCode;
        $this->localeCode = $localeCode;
        $this->channelCode = $channelCode;

        $localeCodesForHeader = $localeCode !== null ? "-$localeCode" : '';
        $channelCodeForHeader = $channelCode !== null ? "-$channelCode" : '';

        foreach ($data as ['amount' => $amount, 'currency' => $currency]) {
            $header = "{$this->attributeCode}{$localeCodesForHeader}{$channelCodeForHeader}-{$currency}";
            $this->headers[] = $header;
            $this->dataAsArray[$header] = $amount;
        }

        $this->data = $data;
    }

    public function attributeCode(): string
    {
        return $this->attributeCode;
    }

    public function localeCode(): ?string
    {
        return $this->localeCode;
    }

    public function channelCode(): ?string
    {
        return $this->channelCode;
    }

    public function data()
    {
        return $this->data;
    }

    public function headers(): array
    {
        return [$this->headers];
    }

    public function toArray(): array
    {
        return $this->dataAsArray;
    }
}
