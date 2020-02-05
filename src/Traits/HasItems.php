<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Traits;

/**
 * @trait   HasItems
 * @package Simianbv\JsonSchema\Traits
 */
trait HasItems
{

    /**
     * @var array |mixed[]
     */
    protected $items = [];

    /**
     * Add items to the array, if the value is an array, recursively trigger this function to allow for inner
     * values to be stored as well.
     *
     * @param array $items
     *
     * @return $this
     */
    public function itemsByArray(array $items): self
    {
        foreach ($items as $key => $value) {
            if (is_array($value)) {
                $this->items[$key] = [];
                foreach ($value as $inner_key => $inner_value) {
                    if (is_array($inner_value)) {
                        $this->items[$key][] = $inner_value;
                    } else {
                        $this->items[$key][] = ['value' => $inner_key, 'label' => $inner_value];
                    }
                }
            } else {
                $this->items[] = ['value' => $value, 'label' => $key];
            }
        }
        return $this;
    }

}
