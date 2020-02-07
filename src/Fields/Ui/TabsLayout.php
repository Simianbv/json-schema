<?php
/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Fields\Ui;

/**
 * @method make(array $fields = [], string $name = null)
 * @class    TabsLayout
 * @package  Simianbv\JsonSchema\Fields\Ui
 */
class TabsLayout extends Layout
{
    function initialize(): void
    {
        $this->attribute('animated', false);
        $this->tabs();
    }

    /**
     * @return array
     */
    public function provideAdditionalAttributes(): array
    {
        return ['animated'];
    }

    /**
     * @param bool $animate
     *
     * @return $this
     */
    public function animate($animate = true)
    {
        $this->attribute('animated', $animate);
        return $this;
    }
}
