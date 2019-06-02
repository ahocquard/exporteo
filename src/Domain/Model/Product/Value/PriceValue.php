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

    private $currency;

    /** @var mixed */
    private $data;

    /** @var string */
    private $header;

    public function __construct(string $attributeCode, ?string $localeCode, ?string$channelCode, string $currency, $data)
    {
        $this->attributeCode = $attributeCode;
        $this->localeCode = $localeCode;
        $this->channelCode = $channelCode;
        $this->currency = $currency;

        $localeCodesForHeader = $localeCode !== null ? "-$localeCode" : "";
        $channelCodeForHeader = $channelCode !== null ? "-$channelCode" : "";

        $this->header = "{$this->attributeCode}{$localeCodesForHeader}{$channelCodeForHeader}{$currency}";

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

    public function currency(): string
    {
        return $this->currency;
    }

    public function data()
    {
        return $this->data;
    }

    public function header(): string
    {
        return $this->header;
    }

    public function toArray(): array
    {
        return [$this->header => $this->data];
    }
}
