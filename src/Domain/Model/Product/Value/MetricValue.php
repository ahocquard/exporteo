<?php

declare(strict_types=1);

namespace App\Domain\Model\Product\Value;

final class MetricValue implements Value
{
    /** @var string */
    private $attributeCode;

    /** @var ?string */
    private $localeCode;

    /** @var ?string */
    private $channelCode;

    /** @var mixed */
    private $data;

    /** @var array */
    private $headers;

    /** @var array */
    private $dataAsArray;

    public function __construct(string $attributeCode, ?string $localeCode, ?string$channelCode, $data)
    {
        $this->attributeCode = $attributeCode;
        $this->localeCode = $localeCode;
        $this->channelCode = $channelCode;

        $localeCodesForHeader = $localeCode !== null ? "-$localeCode" : '';
        $channelCodeForHeader = $channelCode !== null ? "-$channelCode" : '';

        ['amount' => $amount, 'unit' => $unit] = $data;

        $amountHeader = "{$this->attributeCode}{$localeCodesForHeader}{$channelCodeForHeader}";
        $unitHeader = "{$this->attributeCode}{$localeCodesForHeader}{$channelCodeForHeader}-unit";

        $this->headers = [$amountHeader, $unitHeader];

        $this->dataAsArray = [
            $amountHeader => $amount,
            $unitHeader => $unit
        ];

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
        return $this->headers;
    }

    public function toArray(): array
    {
        return $this->dataAsArray;
    }
}
