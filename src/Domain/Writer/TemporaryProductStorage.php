<?php

declare(strict_types=1);

namespace App\Domain\Writer;

use App\Domain\Model\Product\ProductCollection;

/**
 * Persist products into a storage.
 * As you can't fetch all the headers before fetching all the products, it is necessary to store them
 * in a temporary storage before creating a CSV.
 *
 * For example, the last product can have a value of an attribute X that does not exist in any other product.
 * It means that we must generate the CSV file with this column corresponding to the attribute.
 *
 */
interface TemporaryProductStorage
{
    public function persist(ProductCollection $products): void;

    public function fetch(): iterable;
}
