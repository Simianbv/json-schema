<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Fields\Elements;

use Simianbv\JsonSchema\Traits\DateAttributes;
use Simianbv\JsonSchema\Traits\TimeAttributes;

/**
 * @class   DateTime
 * @package Simianbv\JsonSchema\Fields\Elements
 */
class DateTime extends DateTimeBase
{

    use DateAttributes, TimeAttributes;

    /**
     * Set up the default attributes.
     * @return void
     */
    public function setDefaultAttributes(): void
    {
        $this->format('Y-m-d');
        $this->setDateTimeComponent();
    }

    /**
     * @return string
     */
    public function getFieldType(): string
    {
        return 'string';
    }

    /**
     * @return string
     */
    protected function getComponentType(): string
    {
        return 'DateTime';
    }
}
