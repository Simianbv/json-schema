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
class Boolean extends Field
{

    /**
     * @return string
     */
    protected function getFieldType(): string
    {
        return 'boolean';
    }

    /**
     * @return string
     */
    protected function getComponentType(): string
    {
        return 'Boolean';
    }

    /**
     * @return Field
     */
    public function toggle(): Field
    {
        $this->attributes(
            [
                'allow_toggle' => true,
            ]
        );
        return $this;
    }

    /**
     * @param mixed  $value
     * @param string $label
     *
     * @return Field
     */
    public function trueValue($value, $label = null): Field
    {
        $this->attributes(['true-value' => $value]);
        if ($label) {
            $this->attributes(['true-label' => $label]);
        }
        return $this;
    }

    /**
     * @param string      $value
     * @param string|null $label nullable
     *
     * @return Field
     */
    public function falseValue($value, $label = null): Field
    {
        $this->attributes(['false-value' => $value]);
        if ($label) {
            $this->attributes(['false-label' => $label]);
        }
        return $this;
    }

    /**
     * Set up any default values you want to override by default.
     * @return void
     */
    protected function setDefaultAttributes()
    {
        $this
            ->trueValue(true, 'true')
            ->falseValue(false, 'false');
    }
}
