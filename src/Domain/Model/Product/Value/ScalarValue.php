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

    public function toArray(): array
    {
        return [$this->header => $this->data];
    }

    public function header(): string
    {
        return $this->header;
    }
}
