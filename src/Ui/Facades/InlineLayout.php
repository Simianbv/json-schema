<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Ui\Facades;

use Illuminate\Support\Facades\Facade;
use Simianbv\JsonSchema\Ui\InlineLayout as InlineLayoutLayout;

/**
 * @method static make(array $fields = [], string $name = null)
 *
 * @see \Simianbv\JsonSchema\Ui\\InlineLayout
 */
class InlineLayout extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return new InlineLayoutLayout;
    }
}
