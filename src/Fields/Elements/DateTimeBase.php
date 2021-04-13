<?php
/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Fields\Elements;

use Simianbv\JsonSchema\Fields\Field;

/**
 * @class   DateTime
 * @package Simianbv\JsonSchema\Fields\Elements
 */
abstract class DateTimeBase extends Field
{

    /**
     * @var bool
     */
    protected $date = true;

    /**
     * @var bool
     */
    protected $time = true;

    /**
     * Returns true if the implementing Field is both a DATE and a TIME component.
     *
     * @return bool
     */
    public function hasDateTimeComponent(): bool
    {
        return $this->hasDateComponent() && $this->hasTimeComponent();
    }

    /**
     * Returns true if the implementing Field has a DATE component.
     *
     * @return bool
     */
    public function hasDateComponent(): bool
    {
        return $this->date === true;
    }

    /**
     * Only allow monday through friday to be selectable
     * @return $this
     */
    public function workdaysOnly ()
    {
        $this->unselectableDaysOfWeek([0, 6]);
        return $this;
    }


    /**
     * Returns true if the implementing Field has a TIME component.
     *
     * @return bool
     */
    public function hasTimeComponent(): bool
    {
        return $this->time === true;
    }

    /**
     * A helper to define this component to be a DATE and TIME component.
     */
    public function setDateTimeComponent(): self
    {
        $this->date = true;
        $this->time = true;
        return $this;
    }

    /**
     * A helper to define this component to be a DATE component. Setting the TIME component
     * to false.
     *
     * @return $this
     */
    public function setDateComponent(): self
    {
        $this->date = true;
        $this->time = false;
        return $this;
    }

    /**
     * A helper to define this component to be a TIME component. Setting the DATE component
     * to false.
     *
     * @return $this
     */
    public function setTimeComponent()
    {
        $this->date = false;
        $this->time = true;
        return $this;
    }

    /**
     * Add an attribute where you define that the component should  be defined as an inline component.
     *
     * @return $this
     */
    public function inline()
    {
        $this->attributes(['inline' => true]);
        return $this;
    }

    /**
     * Returns the FIELD TYPE property, which, in this case is a NUMBER field.
     *
     * @return string
     */
    protected function getFieldType(): string
    {
        return 'string';
    }

    /**
     * Returns the COMPONENT TYPE property, which, in this case is a DATETIME field.
     *
     * @return string
     */
    protected function getComponentType(): string
    {
        return 'DateTime';
    }
}
