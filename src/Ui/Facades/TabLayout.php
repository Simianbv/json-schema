<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Ui\Facades;

use Illuminate\Support\Facades\Facade;
use Simianbv\JsonSchema\Ui\TabLayout as TabLayoutLayout;

/**
 * @see \Simianbv\JsonSchema\Ui\\TabLayout
 */
class TabLayout extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return new TabLayoutLayout;
    }
}
