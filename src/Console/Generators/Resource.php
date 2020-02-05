<?php
/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Console\Generators;

use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * @class   Resource
 * @package Simianbv\JsonSchema\Console\Generators
 */
class Resource extends Base
{

    // protected $location =  null; // 'app/Lightning/Resources';

    /**
     * @var string
     */
    protected $resource_stub_location = "";

    /**
     * @var string
     */
    protected $facade_namespace = "";

    /**
     * @var string
     */
    protected $model_namespace = "";

    /**
     * @var string
     */
    protected $resource_path = "";

    /**
     * @var string
     */
    private $stub;

    /**
     * @var string
     */
    private $model;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var string[]
     */
    private $uses = [];

    /**
     * Set up the correct paths described in the config files.
     *
     * Resource constructor.
     */
    public function __construct()
    {
        // $this->location = config('json-schema.resources.location');
        $this->resource_stub_location = config('json-schema.stubs.resource');
        $this->facade_namespace = config('json-schema.resources.facade_namespace');
        $this->model_namespace = config('json-schema.models.namespace');
        $this->resource_path = config('json-schema.resources.path');
    }

    /**
     * All the filter properties and types a filter/field can be.
     *
     * @var array
     */
    protected $filterTypes = [
        'autocomplete' => ['tag', 'multiselect'],
        'select' => ['combobox', 'enum', 'set'],
        'text' => ['string', 'email',],
        'textarea' => ['mediumText', 'largeText', 'text',],
        'number' => [
            'int',
            'integer',
            'smallInt',
            'smallInteger',
            'mediumInt',
            'mediumInteger',
            'bigInt',
            'bigInteger',
            'increments',
            'bigIncrements',
            'double',
            'float',
        ],
        'datetime' => ['datetime', 'timestamp'],
        'time' => ['time'],
        'date' => ['date'],
        'bool' => ['boolean', 'checkbox', 'switch', 'toggle'],
    ];

    /**
     * All the filterable options.
     *
     * @var array
     */
    protected $filterOptions = [
        'tags' => ['=', 'IN', 'NOT IN', '!='],
        'select' => ['=', 'IN', 'NOT IN', '!='],
        'text' => ['LIKE', '=', '!=', 'IS EMPTY', 'IS NOT EMPTY'],
        'number' => ['>', '<', '>=', '<=', '=', '!=', 'IN', 'NOT IN', 'BETWEEN', 'LIKE'],
        'datetime' => ['=', '!=', 'IS AFTER', '<', '<=', '>', '>=', 'BETWEEN'],
        'date' => ['=', '!=', 'IS AFTER', '<', '<=', '>', '>=', 'BETWEEN'],
        'bool' => ['=', '!=', 'IS NULL'],
    ];

    /**
     * All the components and their aliases.
     *
     * @var array
     */
    protected $componentTypes = [
        'Checkbox' => ['types' => ['boolean', 'checkbox']],
        'Date' => ['types' => ['date']],
        'DateTime' => ['types' => ['dateTime', 'timestamp']],
        'Number' => [
            'types' => [
                'bigInteger',
                'mediumInteger',
                'integer',
                'bigIncrements',
                'increments',
                'decimal',
                'double',
                'float',
            ],
        ],
        'Radio' => ['types' => ['']],
        'Select' => ['types' => ['enum', 'set']],
        'Textarea' => ['types' => ['longText', 'mediumText', 'text']],
        'Text' => ['types' => ['string', 'char']],
        'Time' => ['types' => ['time']],

        // relation fields
        'Autocomplete' => ['types' => ['autocomplete', 'string', 'text']],
        'Tags' => ['types' => ['array',]],
    ];

    /**
     * @param $resource
     * @param $namespace
     * @param $model
     *
     * @return bool
     */
    public function create(array $resource, $namespace, $model): string
    {
        $this->stub = file_get_contents($this->resource_stub_location);
        // $this->namespace = $namespace;
        // $this->model = $model;
        $this->resource = $resource;

        $fields = [];
        $filters = [];
        $actions = [];

        $componentFields = $this->generateComponentFields();

        $this->uses[] = "use " . $this->getModelNamespace() . $this->ns($namespace) . $model . ";\n";

        $resourceClass = $model . 'Resource';


        $this->getResourcePath();

        $fields = [
            'FullNamespace' => config("json-schema.resources.namespace") . $namespace,
            'Model' => $model,
            'ResourceClass' => $resourceClass,
            'Resource' => $resourceClass,
            'Uses' => implode("", array_unique($this->uses)),
            'Fields' => implode(',', $fields),
            'ComponentFields' => implode(",\n\t\t\t", $componentFields),
            'Filters' => implode(", \n", $this->generateFilters($resource, $namespace, $model)),
            'Actions' => implode(',', $actions),
        ];

        $this->uses = [];

        return $this->fillStub($fields, $this->stub);
    }

    /**
     * Get the resource directory path, if the directory doesn't exist yet, create it.
     *
     * @return string
     */
    private function getResourcePath()
    {
        $path = $this->resource_path;
        $seg = explode('/', $path);

        if (count($seg) > 0) {
            // check if the directory actually exists
            // @todo remove to a better location
            if (!File::isDirectory($path)) {
                File::makeDirectory($path, 0755, true);
            }
            return implode("\\", $seg) . "\\";
        }

        return '';
    }

    /**
     * Generate all the component's fields.
     *
     *
     * @return array
     */
    public function generateComponentFields(): array
    {
        $components = [];
        $relations = $this->getRelations();
        $relationFields = array_map(
            function ($i) {
                return $i['local'];
            }, $relations
        );

        if (!isset($this->resource['columns'])) {
            return $components;
        }

        foreach ($this->resource['columns'] as $column => $attr) {
            $componentField = "";
            $componentType = null;
            $hasRelation = false;
            $relation = null;

            // in case this is a normal field
            if (!in_array($column, $relationFields) || $column == "id") {
                if (isset($attr['primary']) && $attr['primary'] == true) {
                    $componentType = 'ID';
                } else {
                    $componentType = $this->getComponentFieldType($attr['type']);
                }
            } // in case this is a relation field
            else {
                $relation = $this->getComponentRelation($column, $relations);
                if ($relation) {
                    $componentType = $relation['component'];
                    $hasRelation = true;
                }
            }

            if ($componentType) {
                $this->uses[] = "use " . $this->facade_namespace . $componentType . ";\n";
                $label = trim(str_replace('_id', ' ', ucfirst($column)));
                $componentField = $componentType . '::make("' . $column . '", __("' . $label . '"))';
            }

            if ($hasRelation && $relation) {
                $this->uses[] = 'use ' . $this->getModelNamespace() . $this->getNamespacePath($relation) . $relation['model'] . ";\n";
                $componentField .= '->' . $relation['type'] . '(' . $relation['model'] . "::class)";
            }

            if (isset($attr['length']) || isset($attr['maxLength'])) {
                $componentField .= '->max(' . ($attr['length'] ?? $attr['maxLength']) . ')';
            }
            if (isset($attr['minLength'])) {
                $componentField .= '->min(' . $attr['minLength'] . ')';
            }
            if (((isset($attr['required']) && $attr['required'] == true) ||
                    (isset($attr['nullable']) && $attr['nullable'] == false)) && $componentType !== 'ID') {
                $componentField .= '->required()';
            }
            if (isset($attr['nullable']) && $attr['nullable'] == true) {
                $componentField .= '->nullable()';
            }

            if ($componentField !== "") {
                $components[] = $componentField;
            }
        }

        // Log::debug($model);
        // Log::debug($components);

        return $components;
    }

    public function getComponentRelation($type, $relations): array
    {
        $relation = null;
        $relationModel = null;
        foreach ($relations as $key => $attr) {
            if ($attr['local'] == $type) {
                $relation = $relations[$key];
                $relationModel = $key;
                break;
            }
        }
        if (!$relation) {
            return null;
        }
        switch ($relation['type']) {
            case 'hasOne':
            case 'belongsTo':
                $componentType = 'Autocomplete';
                break;
            case 'hasMany':
            case 'belongsToMany':
                $componentType = 'Tags';
                break;
            default:
                $componentType = "Text";
                break;
        }

        return array_merge(
            [
                'model' => $relationModel,
                'component' => $componentType,
            ], $relation
        );
    }

    /**
     * Get the base path if the relation is namespaced on top of the models.
     *
     * @param array $relation
     *
     * @return string
     */
    public function getNamespacePath(array $relation): string
    {
        return (isset($relation['namespace'])) ? $relation['namespace'] . '\\' : '';
    }

    /**
     * Get the namespaced path to the models.
     *
     * @return string
     */
    public function getModelNamespace(): string
    {
        return $this->model_namespace;
    }

    /**
     * Returns the component FIELD TYPE, if there's no alternative or the FIELD TYPE provided was not found
     * returns a plain TEXT FIELD.
     *
     * @param string $type
     *
     * @return string
     */
    public function getComponentFieldType(string $type): string
    {
        foreach ($this->componentTypes as $component => $types) {
            if (in_array($type, $types['types'])) {
                return $component;
            }
        }
        return "Text";
    }

    /**
     * Returns all the relations for this Resource or Model.
     *
     * @return mixed|array
     */
    public function getRelations()
    {
        return (isset($this->resource['relations'])) ? $this->resource['relations'] : [];
    }

    /**
     * Build up the relations and corresponding models associated with the relation.
     *
     * @param array  $resource
     * @param string $namespace
     * @param string $model
     *
     * @return array $filters
     */
    private function generateFilters(array $resource, string $namespace, string $model): array
    {
        $filters = [];

        if (isset($resource['columns'])) {
            $filters = $this->generateColumnFilters($resource);
        }

        if (isset($resource['relations'])) {
            $this->generateRelationFilters($resource, $namespace, $model, $filters);
        }

        return $filters;
    }

    /**
     * Generate the related fields filter lists.
     *
     * @param array  $resource
     * @param string $namespace
     * @param string $model
     * @param array  $filters
     *
     * @return array
     */
    private function generateRelationFilters(array $resource, string $namespace, string $model, array $filters): array
    {
        foreach ($resource['relations'] as $relationModel => $attribute) {
            // As the key of the relations is the name of the model, let's store it to a variable
            $relationColumn = isset($attribute['column']) ? $attribute['column'] : Str::snake($relationModel);

            // Attempt to build the full namespaced class name for the relation model
            $fullRelationModel = $this->getModelNamespace() . $this->ns($attribute['namespace'] ?? null) . $relationModel;

            // we're simply assuming the related model has already been created and get the corresponding table from there.
            if (class_exists($fullRelationModel)) {
                $table = (new $fullRelationModel)->getTable();
            } else {
                $table = '';
            }

            [$type, $options] = $this->determineFieldType($relationModel, $attribute, $namespace, $model);
            $filterOptions = $this->filterOptions[$type];

            $current = "\t\t\t'" . $relationColumn . "_id' => [
                        'type' => '$type',
                        'filter_options' => ['" . implode("', '", $filterOptions) . "'],
                        'name' => '" . $relationColumn . "_id',
                        'label' => '" . $relationModel . "',
                        $options,
                        'relation' => [
                            'relation' => '{$attribute['type']}',
                            'model' => '$fullRelationModel',
                            'select' => '*',
                            'searchable' => ['id'],
                            'joins' => [
                                'table' => '$table',
                                'local' => '{$attribute['local']}', 
                                'foreign' => '{$attribute['foreign']}'
                            ],
                        ]
                    ]";

            $filters[] = $current;
        }

        return $filters;
    }

    /**
     * Build up all the filters for the given columns.
     *
     * @param array $resource
     *
     * @return array
     */
    private function generateColumnFilters(array $resource): array
    {
        $filters = [];
        foreach ($resource['columns'] as $field => $attribute) {
            // get the field type for this column / attribute
            $type = $this->getFieldType($attribute);

            // get the filter options for the given type
            $filterOptions = $this->filterOptions[$type];

            $options = '';
            if (($attribute['type'] === 'enum' || $attribute['type'] === 'set') && isset($attribute['options'])) {
                $options = "\n\t\t\t'options' => ['" . implode("', '", $attribute['options']) . "'],";
            }

            $current = "\n\t\t'$field' => [
            'type' => '$type',
            'filter_options' => ['" . implode("', '", $filterOptions) . "'],
            'name' => '" . $field . "',
            'label' => '" . $field . "', $options\n\t\t]";
            $filters[] = $current;
        }

        return $filters;
    }

    /**
     * @param $attr
     *
     * @return int|string
     */
    private function getFieldType(array $attr)
    {
        if (!isset($attr['type'])) {
            return 'text';
        }
        $type = $attr['type'];

        if (in_array($type, array_keys($this->filterTypes))) {
            return $type;
        }

        foreach ($this->filterTypes as $key => $options) {
            if (in_array($type, $options)) {
                return $key;
            }
        }

        return 'text';
    }

    /**
     * Determine what the FIELD TYPE should be for the relation field and if the field should have a default set
     * of options available or otherwise be queryable.
     *
     * @param string $relationModel
     * @param array  $relationAttributes
     * @param string $namespace
     * @param string $model
     *
     * @return array
     */
    private function determineFieldType(string $relationModel, array $relationAttributes, string $namespace, string $model): array
    {
        // we can assume this is about the relation field, which can be either select or multi select and can have either options or autocomplete

        $appendix = null;
        // $autocomplete = false;
        try {
            $fullRelationModel = $this->getModelNamespace() . $this->ns($relationAttributes['namespace'] ?? null) . $relationModel;

            $count = 0;
            if (class_exists($fullRelationModel)) {
                $count = $fullRelationModel::count();
            }

            if ($count > 30) {
                $apiVersion = env('API_VERSION') ?? 'v1';

                $apiUrl = env('APP_URL') . '/' . $apiVersion . '/' . Str::snake($this->dir($relationAttributes['namespace']) ?? '') . Str::slug(Str::snake(Str::plural($relationModel)));
                // $autocomplete = true;
                $appendix = "'autocomplete' => ['url' => '$apiUrl']";
            }
        } catch (Exception $e) {
            $this->warn("Unable to determine the field type for relation model " . $relationModel);
        }

        $type = isset($relationAttributes['type']) && $relationAttributes['type'] == 'hasMany' ? 'tags' : 'select';

        if (!$appendix) {
            $appendix = "'options' => []";
        }

        return [$type, $appendix];
    }

}
