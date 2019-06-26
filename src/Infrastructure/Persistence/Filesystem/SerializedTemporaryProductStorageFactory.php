<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Filesystem;

use App\Domain\Writer\TemporaryProductStorage;
use App\Domain\Writer\TemporaryProductStorageFactory;
use Symfony\Component\Filesystem\Filesystem;

final class SerializedTemporaryProductStorageFactory implements TemporaryProductStorageFactory
{
    public function create(): TemporaryProductStorage
    {
        $filesystem = new Filesystem();
        $filepath = $filesystem->tempnam(sys_get_temp_dir(), 'exporteo_json_products_');

        return new SerializedTemporaryProductStorage($filepath);
    }
}