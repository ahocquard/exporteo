<?php


namespace App\Domain\Model\File;

/**
 * It represents an array key/value of te data to perist in the file.
 */
final class Row
{
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function data(): array
    {
        return $this->data;
    }
}