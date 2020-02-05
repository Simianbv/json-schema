<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Contracts;

/**
 * @interface IsLockable
 * @package   Simianbv\JsonSchema\Contracts
 */
interface IsLockable
{

    /**
     * Check if the resource is locked yes or no.
     *
     * @return bool
     */
    public function isLocked(): bool;

}
