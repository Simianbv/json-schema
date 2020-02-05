<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Traits;

use DateTime as DateTimeObject;

/**
 * @trait   DateAttributes
 * @package Simianbv\JsonSchema\Traits
 */
trait DateAttributes
{
    /**
     * @var string|null
     */
    protected static $default_format;

    /**
     * @var string|null
     */
    protected $format = null;

    /**
     * Provide an array of days of the week you dont want.
     * I.e. [0, 6] (Sunday is 0, Monday is 1, and so on)
     *
     * @param array $dates
     *
     * @return $this
     */
    public function unselectableDaysOfWeek(array $dates): self
    {
        $this->attributes(['unselectableDaysOfWeek' => $dates]);
        return $this;
    }

    /**
     * Enable the view to show the current week number.
     *
     * @return $this
     */
    public function showWeekNumber(): self
    {
        $this->attributes(['showWeekNumber' => true]);
        return $this;
    }

    /**
     * Set up the min date to be selected.
     *
     * @param string|int|DateTimeObject $minDate
     *
     * @return $this
     */
    public function minDate($minDate): self
    {
        if ($minDate instanceof DateTimeObject) {
            $minDate = $minDate->format($this->getFormat());
        }
        $this->attributes(['minDate' => $minDate]);
        return $this;
    }

    /**
     * Set up the max selectable date.
     *
     * @param string|int|DateTimeObject $maxDate
     *
     * @return $this
     */
    public function maxDate($maxDate): self
    {
        if ($maxDate instanceof DateTimeObject) {
            $maxDate = $maxDate->format($this->getFormat());
        }
        $this->attributes(['maxDate' => $maxDate]);
        return $this;
    }

    /**
     * Set up the default format to be used to display and process date time.
     *
     * @param string|mixed $format
     */
    public static function setDefaultFormat($format)
    {
        self::$default_format = $format;
    }

    /**
     * Set up the format to display dates and times.
     *
     * @param string $format
     *
     * @return $this
     */
    public function format(string $format): self
    {
        self::$default_format = $format;
        $this->format = $format;
        return $this;
    }

    /**
     * Returns the default format for the date time presentation.
     *
     * @return string
     */
    public function getFormat(): string
    {
        if ($this->format === null) {
            $this->format = self::$default_format;
        }
        return $this->format;
    }
}
