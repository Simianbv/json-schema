<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Filters\Facades;

use Illuminate\Support\Facades\Facade;
use Simianbv\JsonSchema\Filters\FilterCollection as FilterCollectionLayout;

/**
 * @method static make(array $fields = [], string $name = null)
 *
 * @see \Simianbv\JsonSchema\Filters\FilterCollection
 */
class FilterCollection extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return new FilterCollectionLayout;
    }
}
