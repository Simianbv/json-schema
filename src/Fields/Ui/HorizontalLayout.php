<?php
/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Fields\Ui;

use Simianbv\JsonSchema\Fields\Layout;

/**
 * @method make(array $fields = [], string $name = null)
 * @class   HorizontalLayout
 * @package Simianbv\JsonSchema\Fields\Ui
 */
class HorizontalLayout extends Layout
{
    function initialize(): void
    {
        $this->horizontal();
    }
}
