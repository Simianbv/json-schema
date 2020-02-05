<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Traits;

/**
 * @trait   Lockable
 * @package Simianbv\JsonSchema\Traits
 */
trait Lockable
{

    /**
     * Is Locked
     *
     * A helper to check if this Model is lockable and if so, is also locked
     *
     * @return bool
     */
    public function isLocked(): bool
    {
        return $this->hasAttribute('is_locked') && ($this->is_locked === true || $this->is_locked === 1);
    }

    /**
     * @param $attr
     *
     * @return bool
     */
    public function hasAttribute($attr)
    {
        return array_key_exists($attr, $this->attributes);
    }
}
