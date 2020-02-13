<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Filters\Facades;

use Illuminate\Support\Facades\Facade;
use Simianbv\JsonSchema\Filters\Elements\Number as NumberFilter;

/**
 * @method static make(string $label, string $column = null)
 *
 * @see \Simianbv\JsonSchema\Filters\Elements\Number
 */
class Number extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return new NumberFilter;
    }
}
