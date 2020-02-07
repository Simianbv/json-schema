<?php
/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Fields\Ui;

/**
 * @method make(array $fields = [], string $name = null)
 * @class   InlineLayout
 * @package Simianbv\JsonSchema\Fields\Ui
 */
class InlineLayout extends Layout
{
    public function initialize(): void
    {
        $this->horizontal();
        $this->inline();
    }
}
