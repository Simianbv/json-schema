<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Fields;

use Simianbv\JsonSchema\Traits\TimeAttributes;

/**
 * @class   Time
 * @package Simianbv\JsonSchema\Fields
 */
class Time extends DateTimeBase
{

    use TimeAttributes;

    /**
     * @return string
     */
    protected function getFieldType(): string
    {
        return 'string';
    }

    /**
     * @return string
     */
    protected function getComponentType(): string
    {
        return 'Time';
    }
}
