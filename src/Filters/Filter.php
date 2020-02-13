<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Filters;

use Illuminate\Support\Str;
use Simianbv\JsonSchema\Contracts\FilterInterface;
use Simianbv\JsonSchema\Contracts\HasRelationInterface;

/**
 * @class   Filter
 * @package Simianbv\JsonSchema\Filters\Elements
 */
abstract class Filter implements FilterInterface
{

    /**
     * @var string
     */
    private $filter_column = '';
    /**
     * @var string
     */
    private $filter_id = '';
    /**
     * @var string
     */
    private $filter_name = '';
    /**
     * @var string
     */
    private $attribute_label = '';
    /**
     * @var string
     */
    private $attribute_placeholder = '';
    /**
     * @var string
     */
    private $column_type = '';
    /**
     * @var string
     */
    private $component_type = '';
    /**
     * @var array
     */
    private $filter_options = [];
    /**
     * @var array
     */
    private $meta = [];
    /**
     * Returns an array containing all the available options per component type.
     *
     * @var array
     */
    private $available_options = [
        'text' => self::FILTER_OPTIONS_TEXT,
        'number' => self::FILTER_OPTIONS_NUMBER,
        'datetime' => self::FILTER_OPTIONS_DATETIME,
        'date' => self::FILTER_OPTIONS_DATE,
        'bool' => self::FILTER_OPTIONS_BOOL,
        'select' => self::FILTER_OPTIONS_SELECT,
        'tags' => self::FILTER_OPTIONS_TAGS,
    ];
    /**
     * @var bool
     */
    private $is_relation_column = false;
    /**
     * @var array
     */
    private $options = [];
    /**
     * @var string|null
     */
    private $relation_table = null;


    /**
     * Initialize the filter object.
     *
     * @param string $label
     * @param string $column
     *
     * @return Filter
     */
    public function initialize(string $label, string $column): Filter
    {
        $this->filter_id = $column;
        $this->filter_name = $label;
        $this->label($label);
        $this->column($column);
        $this->column_type = $this->getColumnType();
        $this->component_type = $this->getComponentType();

        return $this;
    }

    /**
     * Synonym for the initialize, except we want a unified access method.
     *
     * @param string $label
     * @param string $column
     *
     * @return Filter
     */
    public function make(string $label, string $column): Filter
    {
        return $this->initialize($label, $column);
    }

    /**
     * Return the type this column is associated with.
     *
     * @return string
     */
    abstract public function getColumnType(): string;

    /**
     * Return the type of component to use.
     *
     * @return string
     */
    abstract public function getComponentType(): string;

    /**
     *
     */
    public function getValue()
    {
        if ($this->hasValue()) {
            return request()->get($this->getColumn());
        }
        return null;
    }

    /**
     * Check if the value is given somewhere in the get request.
     *
     * @return bool
     */
    public function hasValue()
    {
        return request()->get($this->getColumn()) && request()->get($this->getColumn()) !== null;
    }

    /**
     * Build up the filter schema to an array containing all the right ingredients.
     *
     * @return array
     */
    public function toArray(): array
    {
        $data = [
            '$id' => '#/properties/' . $this->getColumn(),
            'type' => $this->field_type ?? $this->getColumnType(),
            'title' => $this->getLabel(),
            'name' => $this->getName(),
            'column' => $this->getColumn(),
            'component_type' => Str::slug($this->getComponentType()),
            'attrs' => [
                'label' => $this->getLabel(),
                'placeholder' => $this->getPlaceholder(),
            ],
            'value' => $this->getValue(),
            'meta' => [],
        ];

        //        if ($this->getFieldFormat()) {
        //            $data['format'] = $this->getFieldFormat();
        //        }

        if (!empty($this->meta)) {
            $data['meta'] = $this->meta;
        }

        if (!empty($this->base_classes)) {
            $data['base_classes'] = $this->base_classes;
        }

        if ($this instanceof HasRelationInterface && $this->hasRelation()) {
            $data['meta']['relation'] = $this->relationToArray();
        }

        //        if ($params = $this->getAdditionalProperties()) {
        //            foreach ($params as $key => $param) {
        //                if (is_array($param)) {
        //                    $obj = $param;
        //                    $param = $key;
        //                    if (isset($obj['type']) && $obj['type'] == 'object') {
        //                        $data[$param] = (object)$this->{$param};
        //                    }
        //                } else {
        //                    if (isset($this->{$param})) {
        //                        $data[$param] = $this->{$param};
        //                    }
        //                }
        //            }
        //        }
        return $data;
    }

    /**
     * Set up the name for this filter.
     *
     * @param string $name
     *
     * @return Filter
     */
    public function name(string $name): Filter
    {
        $this->filter_name = $name;
        return $this;
    }

    /**
     * Set up the label for this filter.
     *
     * @param string $label
     *
     * @return Filter
     */
    public function label(string $label): Filter
    {
        $this->attribute_label = $label;
        return $this;
    }

    /**
     * Set up the name for the placeholder for this filter.
     *
     * @param string $placeholder
     *
     * @return Filter
     */
    public function placeholder(string $placeholder): Filter
    {
        $this->attribute_placeholder = $placeholder;
        return $this;
    }

    /**
     * Add an affix to the filter property.
     *
     * @param string $affix
     *
     * @return Filter
     */
    public function affix(string $affix): Filter
    {
        return $this->attribute(['affix' => $affix]);
    }

    /**
     * Add a suffix to the filter property.
     *
     * @param string $suffix
     *
     * @return Filter
     */
    public function suffix(string $suffix): Filter
    {
        return $this->attribute(['suffix' => $suffix]);
    }

    /**
     * Add an icon to the filter property.
     *
     * @param string $icon
     *
     * @return Filter
     */
    public function icon(string $icon): Filter
    {
        return $this->attribute(['icon' => $icon]);
    }

    /**
     * Set up the size of the component field.
     *
     * @param mixed $size
     *
     * @return Filter
     */
    public function size($size): Filter
    {
        $this->attribute(['size' => $size]);
        return $this;
    }

    /**
     * Set the field size to small. A utility helper to quickly resize to smaller fields.
     *
     * @return Filter
     * @see size
     */
    public function small(): Filter
    {
        return $this->size('small');
    }

    /**
     * Set the field to medium. A utility helper to quickly resize to medium field.
     * @return Filter
     * @see size
     */
    public function medium(): Filter
    {
        return $this->size('medium');
    }

    /**
     * Set the field to medium. A utility helper to quickly resize to large field.
     * @return Filter
     * @see size
     */
    public function large(): Filter
    {
        return $this->size('large');
    }

    /**
     * Add a meta attribute to the output.
     *
     * @param array $attribute
     *
     * @return Filter
     */
    public function attribute(array $attribute): Filter
    {
        foreach ($attribute as $key => $value) {
            $this->meta[$key] = $value;
        }
        return $this;
    }

    /**
     * Set the name of the column used by this filter.
     *
     * @param string $column
     *
     * @return Filter
     */
    public function column(string $column): Filter
    {
        $this->filter_column = $column;
        return $this;
    }

    /**
     * Returns the unique identifier for this filter.
     *
     * @return string
     */
    public function getId(): string
    {
        return $this->filter_id;
    }

    /**
     * Returns the name for this filter.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->filter_name;
    }

    /**
     * Return the name of the column.
     *
     * @return string
     */
    public function getColumn(): string
    {
        return $this->filter_column;
    }

    /**
     * Returns the label for this filter.
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->attribute_label;
    }

    /**
     * Returns the placeholder for this filter.
     *
     * @return string
     */
    public function getPlaceholder(): string
    {
        return $this->attribute_placeholder;
    }

    /**
     * @param int|null $flags
     *
     * @return false|string
     */
    public function toJson(int $flags = null)
    {
        return json_encode($this->toArray(), $flags);
    }

    /**
     * @return false|string
     */
    public function __toString()
    {
        return $this->toJson();
    }
}
