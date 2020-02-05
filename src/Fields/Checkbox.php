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
class Checkbox extends Boolean
{
    /**
     * @return string
     */
    protected function getComponentType(): string
    {
        return 'Checkbox';
    }
}
