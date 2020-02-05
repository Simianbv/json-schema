<?php
/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Ui;

/**
 * @method make(array $fields = [], string $name = null)
 * @class   InlineLayout
 * @package Simianbv\JsonSchema\Ui
 */
class InlineLayout extends Layout
{
    public function initialize(): void
    {
        $this->horizontal();
        $this->inline();
    }
}
