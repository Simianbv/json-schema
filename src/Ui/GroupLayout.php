<?php
/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Ui;

use Simianbv\JsonSchema\Ui\Layout;

/**
 * @method make(array $fields = [], string $name = null)
 * @class    GroupLayout
 * @package  Simianbv\JsonSchema\Fields\Ui
 */
class GroupLayout extends Layout
{
    function initialize(): void
    {
        $this->group();
    }
}
