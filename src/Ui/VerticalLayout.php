<?php
/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Ui;

/**
 * @method make(array $fields = [], string $name = null)
 * @class   VerticalLayout
 * @package Simianbv\JsonSchema\Ui
 */
class VerticalLayout extends Layout
{
    public function initialize(): void
    {
        $this->vertical();
    }
}
