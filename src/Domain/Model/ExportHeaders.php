<?php

declare(strict_types=1);

namespace App\Domain\Model;

final class ExportHeaders
{
    /** @var string[] */
    private $headers = [];

    private function __construct(array $headers)
    {
        $this->headers = $headers;
    }

    public static function empty(): self
    {
        return new self([]);
    }


    public function addHeaders(string ...$headers)
    {
        return new self(array_merge($this->headers, $headers));
    }

    public function headers(): array
    {
        ksort($this->headers);

        return $this->headers;
    }
}
