<?php

declare(strict_types=1);

namespace App\Domain\Writer;

interface ProductRepositoryFactory
{
    public function create(): ProductRepository;
}