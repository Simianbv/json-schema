<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Simianbv\JsonSchema\Fields\Textarea
 */
class Textarea extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return new \Simianbv\JsonSchema\Fields\Textarea;
    }
}
