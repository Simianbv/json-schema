<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Fields\Elements;

/**
 * @class   Boolean
 * @package Simianbv\JsonSchema\Fields\Elements
 */
class Toggle extends Boolean
{
    /**
     * @return string
     */
    protected function getComponentType(): string
    {
        return 'toggle';
    }
}
