<?php

declare(strict_types=1);

namespace App\Domain\Model\Product\Value;

final class ScalarValue implements Value
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
    private $header;

    public function __construct(string $attributeCode, ?string $localeCode, ?string$channelCode, $data)
    {
        $this->attributeCode = $attributeCode;
        $this->localeCode = $localeCode;
        $this->channelCode = $channelCode;

        $localeCodesForHeader = $localeCode !== null ? "-$localeCode" : "";
        $channelCodeForHeader = $channelCode !== null ? "-$channelCode" : "";

        $this->header = "{$this->attributeCode}{$localeCodesForHeader}{$channelCodeForHeader}";

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
        return [$this->header];
    }

    public function toArray(): array
    {
        return [$this->header => $this->data];
    }
}
