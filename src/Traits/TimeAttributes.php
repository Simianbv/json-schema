<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Traits;

/**
 * @trait   TimeAttributes
 * @package Simianbv\JsonSchema\Traits
 */
trait TimeAttributes
{
    /**
     * Set up the min time.
     *
     * @param $minTime
     *
     * @return $this
     */
    public function minTime($minTime): self
    {
        $this->attributes(['minTime' => $minTime]);
        return $this;
    }

    /**
     * Set up the max time.
     *
     * @param $maxTime
     *
     * @return $this
     */
    public function maxTime($maxTime): self
    {
        $this->attributes(['maxTime' => $maxTime]);
        return $this;
    }

    /**
     * A helper to allow seconds to be set as well.
     *
     * @return $this
     */
    public function enableSeconds(): self
    {
        $this->attributes(['enableSeconds' => true]);
        return $this;
    }

    /**
     * A helper to disable seconds to be set.
     *
     * @return $this
     */
    public function disableSeconds(): self
    {
        $this->attributes(['enableSeconds' => false]);
        return $this;
    }

    /**
     * A helper to display the component as an inline element.
     *
     * @return $this
     */
    public function inline(): self
    {
        $this->attributes(['inline' => true]);
        return $this;
    }

}
