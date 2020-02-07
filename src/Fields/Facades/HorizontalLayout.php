<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Fields\Facades;

use Illuminate\Support\Facades\Facade;
use Simianbv\JsonSchema\Fields\Ui\HorizontalLayout as HorizontalLayoutLayout;

/**
 * @see \Simianbv\JsonSchema\Fields\Ui\HorizontalLayout
 */
class HorizontalLayout extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return new HorizontalLayoutLayout();
    }
}
