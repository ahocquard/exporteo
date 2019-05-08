<?php

declare(strict_types=1);

namespace App\Domain\Query;

use App\Domain\Model\Page;

interface GetApiFormatProductList
{
    public function fetchByPage(string $client, string $secret, string $username, string $password, string $uri): Page;
}
