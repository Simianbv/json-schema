<?php

namespace Simianbv\JsonSchema;


use Simianbv\JsonSchema\Contracts\LayoutInterface;
use Simianbv\JsonSchema\Contracts\ResourceInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * @class   Resource
 * @package Simianbv\JsonSchema
 */
abstract class Resource implements ResourceInterface
{

    /**
     * @var array
     */
    protected $column_names = [];

    /**
     * @var array
     */
    public $column_definitions = [];

    /**
     * Returns the fields required to populate the Resource Model's fields.
     *
     * @param Request $request
     *
     * @return mixed
     */
    abstract public function fields(Request $request): LayoutInterface;

    /**
     * Returns an array containing all the filterable fields for a given Resource's Model.
     *
     * @param Request $request
     *
     * @return mixed
     */
    abstract public function filters(Request $request): array;

    /**
     * Returns an array containing all the Action classes that can run on the Resource's Model.
     *
     * @param Request $request
     *
     * @return mixed
     */
    abstract public function actions(Request $request): array;

    /**
     * Returns the Model class
     *
     * @return string
     */
    abstract public function entity(): string;

    /**
     * Get the columns for this Model and its associated Resource.
     *
     * return array
     */
    public function columns(): array
    {
        $this->column_names = Schema::getColumnListing($this->model()->getTable());
        return $this->column_names;
    }

    /**
     * Build up the columns and set up the correct data types for each field.
     */
    public function compile()
    {
        $table = $this->model()->getTable();
        foreach ($this->columns() as $column) {
            $this->column_definitions[$column] = DB::getSchemaBuilder()->getColumnType($table, $column);
        }
    }

    /**
     * Resolve the Model component based on the scope and name provided.
     *
     * @param $scope
     * @param $name
     *
     * @return mixed
     */
    public function resolve(string $scope, string $name): string
    {
        return Str::studly("App\\Models\\" . Str::studly($scope) . "\\" . Str::studly($name));
    }

    /**
     * Returns the Resource model class as JSON.
     *
     * @return false|string
     */
    public function __toString()
    {
        return json_encode($this);
    }

    /**
     * Returns a new instance of the Resource Model.
     *
     * @return Model|mixed
     */
    public function model()
    {
        $class = $this->entity();
        return app($class);
    }

}
