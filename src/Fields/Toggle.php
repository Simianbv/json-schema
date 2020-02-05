<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Fields;

/**
 * @class   Boolean
 * @package Simianbv\JsonSchema\Fields
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
