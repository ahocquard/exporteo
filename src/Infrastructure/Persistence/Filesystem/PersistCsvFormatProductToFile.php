<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Filesystem;

use Symfony\Component\Filesystem\Filesystem;

/**
 * @copyright 2019 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class PersistCsvFormatProductToFile
{
    public function persist()
    {
        $filesystem = new Filesystem();
        $temporaryFilePath = $filesystem->tempnam('/tmp', 'exporteo_json_products_');

    }
}
