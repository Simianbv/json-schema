<?php
/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Fields\Elements;

use Simianbv\JsonSchema\Traits\DateAttributes;

/**
 * @class   Date
 * @package Simianbv\JsonSchema\Fields\Elements
 */
class Date extends DateTimeBase
{

    use DateAttributes;

    /**
     * @return $this
     */
    public function range(): Date
    {
        $this->setFieldType('array');
        $this->attributes(['range' => true]);
        return $this;
    }

    /**
     * Set up the default values.
     *
     */
    public function setDefaultAttributes(): void
    {
        $this->format('Y-m-d');
        $this->setDateComponent();
    }

    /**
     * @return string
     */
    protected function getComponentType(): string
    {
        return 'Date';
    }
}
