<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Filters;

use Simianbv\JsonSchema\Contracts\FilterInterface;
use Simianbv\JsonSchema\Contracts\LayoutInterface;

/**
 * @class   FilterCollection
 * @package Simianbv\JsonSchema\Filters
 */
class FilterCollection implements LayoutInterface
{
    /**
     * @var array
     */
    private $collection_filters = [];
    /**
     * @var string
     */
    private $collection_name = '';
    /**
     * @var string
     */
    private $collection_description = '';
    /**
     * @var int
     */
    private $column_width = 25;

    /**
     * Set the size for all components instantly.
     *
     * @var string|int
     */
    private $collection_size;

    /**
     * Initialize the Filter collection
     *
     * @param array       $filters
     * @param string|null $title
     */
    public function initialize(array $filters, string $title = null)
    {
        $this->filters($filters);
    }

    /**
     * @param array       $filters
     * @param string|null $name
     *
     * @return FilterCollection
     */
    public function make(array $filters = [], string $name = null): FilterCollection
    {
        $this->initialize($filters, $name);

        return $this;
    }

    /**
     * Add multiple filters by providing an array of filters.
     *
     * @param array $filters
     *
     * @return $this
     */
    public function filters(array $filters): FilterCollection
    {
        foreach ($filters as $filter) {
            if ($filter instanceof FilterInterface) {
                $this->addFilter($filter);
            }
        }
        return $this;
    }

    /**
     * @param FilterInterface $filter
     * @param int|null        $index default is null
     *
     * @return $this
     */
    public function addFilter(FilterInterface $filter, $index = null)
    {
        if (!$index) {
            $this->collection_filters[] = $filter;
        } else {
            $this->collection_filters[$index] = $filter;
        }

        return $this;
    }

    /**
     * Build up the FORM SCHEMA as defined by the JSON SCHEMA draft.
     *
     * @return array
     */
    public function toSchema(): array
    {
        return [
            '$schema' => "http://json-schema.org/draft-07/schema#",
            "type" => "object",
            "title" => $this->getName(),
            "description" => $this->getDescription(),
            "properties" => $this->getProperties(),
            "required" => [],
        ];
    }

    /**
     * Returns an array containing all the properties.
     *
     * @return array
     */
    public function getProperties(): array
    {
        $data = [];
        foreach ($this->getFilters() as $filter) {
            /** @var $filter Filter */
            //            if ($this->size) {
            //                $element->size($this->size);
            //            }
            $data[$filter->getColumn()] = $filter->toArray();
        }
        return $data;
    }

    /**
     * Set up the number of columns per row
     *
     * @param int $percentage
     *
     * @return FilterCollection
     */
    public function width(int $percentage = 25): FilterCollection
    {
        $this->column_width = $percentage;
        return $this;
    }

    /**
     * Set up the name for the filter collection.
     *
     * @param string $name
     *
     * @return FilterCollection
     */
    public function name(string $name): FilterCollection
    {
        $this->collection_name = $name;
        return $this;
    }

    /**
     * Set the description, which is required for the form schema.
     *
     * @param string $description
     *
     * @return FilterCollection
     */
    public function description(string $description): FilterCollection
    {
        $this->collection_description = $description;
        return $this;
    }

    /**
     * Returns all the filters available in this filter collection.
     *
     * @return array
     */
    public function getFilters(): array
    {
        return $this->collection_filters;
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->column_width;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->collection_name;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->collection_description;
    }

    /**
     * @param $size
     *
     * @return $this
     */
    public function size($size)
    {
        $this->collection_size = $size;
        return $this;
    }
}
