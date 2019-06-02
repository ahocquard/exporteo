<?php

declare(strict_types=1);

namespace App\Domain\Model\Product\Value;

interface Value
{
    public function attributeCode();

    public function localeCode(): ?string;

    public function channelCode(): ?string;

    public function data();

    public function headers(): array;

    public function toArray(): array;
}
