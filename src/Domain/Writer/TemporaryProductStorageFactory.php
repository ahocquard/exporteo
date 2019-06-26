<?php

declare(strict_types=1);

namespace App\Domain\Writer;

interface TemporaryProductStorageFactory
{
    public function create(): TemporaryProductStorage;
}