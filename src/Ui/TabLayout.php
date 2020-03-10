<?php
/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Ui;

/**
 * @method make(array $fields = [], string $name = null)
 * @class   TabLayout
 * @package Simianbv\JsonSchema\Fields\Ui
 */
class TabLayout extends Layout
{
    function initialize(): void
    {
        $this->tab();
    }

    /**
     * @return array
     */
    public function provideAdditionalAttributes(): array
    {
        return ['icon'];
    }

    /**
     * @param string $icon
     *
     * @return $this
     */
    public function icon(string $icon)
    {
        $this->attributes(['icon' => $icon]);
        return $this;
    }
}
