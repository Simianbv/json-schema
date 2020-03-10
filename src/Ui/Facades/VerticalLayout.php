<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Ui\Facades;

use Illuminate\Support\Facades\Facade;
use Simianbv\JsonSchema\Ui\VerticalLayout as VerticalLayoutLayout;

/**
 * @see \Simianbv\JsonSchema\Ui\VerticalLayout
 */
class VerticalLayout extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return new VerticalLayoutLayout;
    }
}
