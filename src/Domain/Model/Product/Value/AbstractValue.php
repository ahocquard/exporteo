<?php

declare(strict_types=1);

namespace App\Domain\Model\Product\Value;

abstract class AbstractValue implements Value
{
    /** @var string */
    protected $attributeCode;

    /** @var ?string */
    protected $localeCode;

    /** @var ?string */
    protected $channelCode;

    /** @var mixed */
    protected $data;

    /** @var string */
    protected $header;

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

    public function header(): string
    {
        return $this->header;
    }
}
