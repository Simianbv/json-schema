<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Facades;

use Illuminate\Support\Facades\Facade;
use Simianbv\JsonSchema\Fields\Boolean as BooleanField;

/**
 * @see \Simianbv\JsonSchema\Fields\Boolean
 */
class Boolean extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return new BooleanField();
    }
}
