<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Filesystem;

use App\Domain\Writer\ProductRepository;
use App\Domain\Writer\ProductRepositoryFactory;
use Symfony\Component\Filesystem\Filesystem;

final class SerializedProductRepositoryFactory implements ProductRepositoryFactory
{
    public function create(): ProductRepository
    {
        $filesystem = new Filesystem();
        $filepath = $filesystem->tempnam(sys_get_temp_dir(), 'exporteo_json_products_');

        return new SerializedProductRepository($filepath);
    }
}