<?php

declare(strict_types=1);

namespace App\Domain\Model;

// TODO: fix stateful object
// it is stateful because async tasks add headers in it
// we should maybe use another pattern to keep immutability
final class ExportHeaders
{
    /** @var string[] */
    private $headers = [];

    public function addHeaders(string ...$headers)
    {
        $this->headers = array_merge($this->headers, $headers);
    }

    public function headers(): array
    {
        sort($this->headers);

        return $this->headers;
    }
}
