<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Ui\Facades;

use Illuminate\Support\Facades\Facade;
use Simianbv\JsonSchema\Ui\TabsLayout as TabsLayoutLayout;

/**
 * @see \Simianbv\JsonSchema\Ui\TabsLayout
 */
class TabsLayout extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return new TabsLayoutLayout;
    }
}
