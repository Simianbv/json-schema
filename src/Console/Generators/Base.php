<?php
/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Console\Generators;

use Illuminate\Console\Command;
use Simianbv\JsonSchema\Console\Commands\MakeResource;

/**
 * @class   Base
 * @package Simianbv\JsonSchema\Console\Generators
 */
class
Base
{
    /**
     * @var string
     */
    protected $location = '';

    /**
     * @var string
     */
    protected $stubLocation = '';

    /**
     * @var array
     */
    protected $fillables = [];

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $resource;

    /**
     * @var string[]
     */
    protected $output = [];

    /**
     * @var MakeResource
     */
    protected $parent;

    /**
     * ClassGenerator constructor.
     *
     * @param       $resource
     * @param array $fields
     */
    public function initialize($resource, array $fields = [])
    {
        if (!empty($fields)) {
            $this->fill($fields);
        }
        $this->resource = $resource;
    }

    /**
     * Fill the model with the key-value attributes given in the $fields array.
     *
     * @param array $fields
     */
    public function fill(array $fields)
    {
        foreach ($fields as $key => $value) {
            if (in_array($key, $this->fillables)) {
                $this->data[$key] = $value;
            }
        }
    }

    /**
     * Returns the base namespace / path to where the models are stored.
     * @return string
     */
    protected function getModelNamespace()
    {
        return config('json-schema.models.namespace');
    }

    /**
     * Fill the stub, do a search and replace and return the final stub string to write to file.
     *
     * @param array $fields
     * @param       $content
     * @param array $delimiters
     *
     * @return mixed
     */
    public function fillStub(array $fields, $content, $delimiters = ['{{', '}}'])
    {
        $keys = array_map(
            function ($key) use ($delimiters) {
                return $delimiters[0] . $key . $delimiters[1];
            }, array_keys($fields)
        );

        return str_replace($keys, array_values($fields), $content);
    }

    /**
     * Trim the value from all slashes
     *
     * @param string $value
     * @param string $trim defaults to \
     *
     * @return string
     */
    protected function trim(string $value, string $trim = '\\')
    {
        return rtrim(trim($value, $trim), $trim);
    }

    /**
     * @param Command $class
     */
    public function setCallee($class)
    {
        $this->parent = $class;
    }

    /**
     * Return the namespace if the namespace is set, otherwise, return an empty string
     *
     * @param string $ns
     *
     * @return string
     */
    protected function ns($ns): string
    {
        if (!$ns) {
            return '';
        }

        return strlen($ns) > 0 ? $ns . '\\' : '';
    }

    /**
     * @param string $ns
     *
     * @return string
     */
    protected function dir($ns): string
    {
        if (!$ns) {
            return '';
        }
        return strlen($ns) > 0 ? $ns . '/' : '';
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation(string $location): void
    {
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function getStubLocation(): string
    {
        return $this->stubLocation;
    }

    /**
     * @param string $stubLocation
     */
    public function setStubLocation(string $stubLocation): void
    {
        $this->stubLocation = $stubLocation;
    }

    /**
     * @return array
     */
    public function getResource(): array
    {
        return $this->resource;
    }

    /**
     * @param array $resource
     */
    public function setResource(array $resource): void
    {
        $this->resource = $resource;
    }

    /**
     * @return array
     */
    public function getFillables(): array
    {
        return $this->fillables;
    }

    /**
     * @param array $fillables
     */
    public function setFillables(array $fillables): void
    {
        $this->fillables = $fillables;
    }

    /**
     * @param $key
     * @param $value
     */
    public function addFillables($key, $value): void
    {
        $this->fillables[$key] = $value;
    }

}
