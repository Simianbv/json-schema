<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Console\Generators;

use Illuminate\Support\Str;
use Simianbv\JsonSchema\Contracts\IsLockable;
use Simianbv\JsonSchema\Traits\Lockable;

/**
 * @class   ModelGenerator
 * @package Simianbv\JsonSchema\Console\Generators
 */
class Model extends Base
{
    /**
     * @var string
     */
    protected $location = "";

    /**
     * @var string
     */
    protected $stubLocation = "";

    /**
     * @var array
     */
    protected $fillables = [
        'Date',
        'SoftDelete',
        'Namespace',
        'Uses',
        'Fillables',
        'Relations',
    ];

    /**
     * @var string
     */
    private $relation_stub;

    /**
     * @var string
     */
    private $model_stub;

    /**
     * @var string
     */
    private $soft_delete_stub;


    public function __construct()
    {
        $this->location = config('json-schema.models.path');
        $this->stubLocation = config('json-schema.stubs.model');
    }

    /**
     * @param $resource
     * @param $namespace
     * @param $model
     *
     * @return bool
     */
    public function create($resource, $namespace, $model)
    {
        $this->model_stub = file_get_contents($this->stubLocation);
        $this->resource = $resource;

        $fillables = [];
        $relations = [];
        $uses = [];
        $traits = [];
        $implements = [];

        if (isset($this->resource['lockable']) && $this->resource['lockable'] == true) {
            $uses[] = "use " . IsLockable::class . ";\n";
            $uses[] = "use " . Lockable::class . ";\n";
            $implements[] = 'IsLockable';
            $traits[] = 'Lockable';
            $fillables[] = 'is_locked';
        }

        if (isset($this->resource['columns'])) {
            foreach ($this->resource['columns'] as $field => $attr) {
                if (
                    (
                        !isset($attr['fillable']) ||
                        (isset($attr['fillable']) && $attr['fillable'] == true)
                    )
                    && !isset($attr['primary'])
                ) {
                    $fillables[] = $field;
                }
            }
        }

        $softDelete = '';
        if (isset($this->resource['attributes']) && $this->resource['softDelete'] == true) {
            $this->soft_delete_stub = file_get_contents(config('json-schema.stubs.model-soft-delete'));

            $softDelete = $this->fillStub([], $this->soft_delete_stub);
            $uses[] = 'use Illuminate\Database\Eloquent\SoftDeletes;';
            $traits[] = 'SoftDeletes';
        }

        if (isset($this->resource['relations']) && is_array($this->resource['relations'])) {
            $this->relation_stub = file_get_contents(config('json-schema.stubs.model-relation'));

            foreach ($this->resource['relations'] as $relationModel => $attr) {
                if (!isset($attr['type']) || !isset($attr['foreign']) || !isset($attr['local'])) {
                    $this->parent->error("Niet alle verplichte velden ( type, foreign, local, namespace) zijn ingevuld bij $model -> $relationModel");
                    continue;
                }

                if (isset($attr['model'])) {
                    $relationModel = $attr['model'];
                }
                $relationNamespace = isset($attr['namespace']) ? $this->trim($attr['namespace']) : null;
                $fnName = $attr['function'] ?? '';
                $uses[] = 'use ' . $this->getModelNamespace() . $this->ns($relationNamespace) . $relationModel . ";\n";
                $uses[] = 'use Illuminate\Database\Eloquent\Relations\\' . Str::studly($attr['type']) . ";\n";
                $relations[] = $this->fillRelationStub($model, $relationModel, $attr, $fnName);
            }
        }

        $fields = [
            'SoftDelete' => $softDelete,
            'Date' => date('Y'),
            'ModelTable' => strtolower($namespace) . '_' . strtolower(Str::snake(Str::plural($model))),
            'Namespace' => 'App\\Models' . (strlen($namespace) > 0 ? '\\' . $namespace : ''),
            'Model' => $model,
            'Uses' => implode("", array_unique($uses)),
            'Fillables' => "'" . implode("', \n\t\t'", $fillables) . "'\n",
            'Relations' => implode('', $relations),
            'Traits' => (!empty($traits) ? 'use ' . implode(', ', $traits) . ';' : ''),
            'Implements' => (!empty($implements) ? ' implements ' . implode(', ', $implements) : ''),
        ];

        $modelContent = $this->fillStub($fields, $this->model_stub);
        return $modelContent;
    }

    /**
     * @param string $model
     * @param string $relationModel
     * @param array  $attr
     * @param string $fnName
     *
     * @return mixed
     */
    public function fillRelationStub(string $model, string $relationModel, array $attr, $fnName = '')
    {
        if (!isset($attr['type'])) {
            return '';
        }

        if ($fnName === '') {
            if ($attr['type'] == 'hasMany' || $attr['type'] == 'belongsToMany') {
                $fnName = Str::camel(Str::plural($relationModel));
            } else {
                $fnName = Str::camel(Str::singular($relationModel));
            }
        }

        $fields = [
            'ModelClass' => $model,
            'RelatedModelClass' => $relationModel,
            'RelationType' => Str::camel($attr['type']),
            'RelatedModelFunction' => $fnName,
        ];

        return $this->fillStub($fields, $this->relation_stub);
    }
}
