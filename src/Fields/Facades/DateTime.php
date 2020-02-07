<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Fields\Facades;

use Illuminate\Support\Facades\Facade;
use Simianbv\JsonSchema\Fields\Elements\DateTime as DateTimeField;

/**
 * @see \Simianbv\JsonSchema\Fields\Elements\DateTime
 */
class DateTime extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return new DateTimeField();
    }
}
