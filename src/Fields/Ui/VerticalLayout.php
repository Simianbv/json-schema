<?php
/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Fields\Ui;

use Simianbv\JsonSchema\Fields\Layout;

/**
 * @method make(array $fields = [], string $name = null)
 * @class   VerticalLayout
 * @package Simianbv\JsonSchema\Fields\Ui
 */
class VerticalLayout extends Layout
{
    public function initialize(): void
    {
        $this->vertical();
    }
}
