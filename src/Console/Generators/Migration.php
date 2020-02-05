<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Console\Generators;

use Illuminate\Support\Str;

/**
 * @class   MigrationGenerator
 * @package Simianbv\JsonSchema\Console\Generators
 */
class Migration extends Base
{
    /**
     * @var string
     */
    protected $location = 'database/migrations';
    protected $stubLocation = 'Console/Lightning/Stubs/Lightning-Migration.stub';
    protected $fillables = [
        'Model',
        'Table',
        'Columns',
        'Timestamps',
    ];

    private $stub;

    /**
     * @param $resource
     * @param $namespace
     * @param $model
     *
     * @return string
     */
    public function create(array $resource, string $namespace, string $model): string
    {
        $this->resource = $resource;
        $this->stub = file_get_contents(app_path($this->stubLocation));

        $columns = [];

        if (isset($this->resource['columns'])) {
            foreach ($this->resource['columns'] as $field => $attr) {
                $column = '';

                if (!isset($attr['type'])) {
                    continue;
                }
                if (isset($attr['primary']) && $attr['primary'] == true) {
                    $column = '$table->bigIncrements("' . $field . '")->unsigned()';
                } else {
                    if ($attr['type'] == 'enum' || $attr['type'] == 'set') {
                        $options = $attr['options'] ?? [];
                        $column = '$table->' . $attr['type'] . '("' . $field . '", ["' . implode('", "', $options) . '"])';
                    } else {
                        if (isset($attr['length'])) {
                            $field .= '", ' . $attr['length'];
                        } else {
                            $field .= '"';
                        }

                        $column = '$table->' . $attr['type'] . '("' . $field . ')';
                        if (isset($attr['unsigned']) && $attr['unsigned']) {
                            $column .= '->unsigned()';
                        }

                        if (!isset($attr['nullable']) || $attr['nullable'] === true) {
                            $column .= '->nullable()';
                        }
                        if (isset($attr['unique']) && $attr['unique'] === true) {
                            $column .= '->unique()';
                        }

                        if (isset($attr['default'])) {
                            $default = is_string($attr['default']) ? '"' . $attr['default'] . '"' : $attr['default'];

                            if (!$default) {
                                $default = 'false';
                            }

                            $column .= '->default(' . $default . ')';
                        }

                        if (isset($attr['comment'])) {
                            $column .= '->comment("' . $attr['comment'] . '")';
                        }
                    }
                }

                $column .= ';';

                $columns[] = $column;
            }

            if (isset($this->resource['attributes']) && is_array($this->resource['attributes'])) {
                foreach ($this->resource['attributes'] as $migrationAttr) {
                    $columns[] = '$table->' . $migrationAttr . ';';
                }
            }
        }

        if (isset($this->resource['relations']) && is_array($this->resource['relations'])) {
            foreach ($this->resource['relations'] as $relationModel => $attr) {
                // @todo: add a default $foreignKeyConstraint as well?
            }
        }

        if (isset($this->resource['lockable']) && $this->resource['lockable'] == true) {
            $columns[] = '$table->boolean("is_locked")->nullable()->default(false);';
        }

        $fields = [
            'Model' => $model,
            'Namespace' => $namespace,
            'Table' => strtolower($namespace) . '_' . strtolower(Str::snake(Str::plural($model))),
            'Columns' => implode("\n\t\t\t", $columns) . "\n",
            'SoftDeletes' => isset($resource['softDelete']) && $resource['softDelete'] == true ? '$table->softDeletes();' : '',
            'Timestamps' => isset($resource['timestamps']) && $resource['timestamps'] == true ? '$table->timestamps();' : '',
        ];

        $migrationContent = $this->fillStub($fields, $this->stub);

        return $migrationContent;
    }

    /**
     * @param string $model
     * @param string $relationModel
     * @param array  $attr
     *
     * @return mixed
     */
    public function fillRelationStub(string $model, string $relationModel, array $attr)
    {
        if (!isset($attr['type'])) {
            return '';
        }

        if ($attr['type'] === 'hasMany') {
            $fnName = Str::camel(Str::plural($relationModel));
        } else {
            $fnName = Str::camel(Str::singular($relationModel));
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
