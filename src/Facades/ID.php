<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Facades;

use Illuminate\Support\Facades\Facade;
use Simianbv\JsonSchema\Fields\ID as IDField;

/**
 * @see \Simianbv\JsonSchema\Fields\ID
 */
class ID extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return new IDField();
    }
}
