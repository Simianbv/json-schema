<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Facades;

use Illuminate\Support\Facades\Facade;
use Simianbv\JsonSchema\Fields\AutoComplete as AutoCompleteField;

/**
 * @see \Simianbv\JsonSchema\Fields\AutoComplete
 */
class AutoComplete extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return new AutoCompleteField();
    }
}
