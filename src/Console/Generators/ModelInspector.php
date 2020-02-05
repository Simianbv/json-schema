<?php
/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Console\Generators;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Str;

/**
 * Class ModelInspector
 * @package Simianbv\JsonSchema\Console\Generators
 */
class ModelInspector
{
    /**
     * @var Command
     */
    private $parent;

    /**
     * @param Command $parent
     */
    public function setCallee(Command $parent): void
    {
        $this->parent = $parent;
    }

    public function process(string $modelPath): array
    {
        $class = config('json-schema.models.namespace') . $modelPath;

        if (!class_exists($class)) {
            throw new Exception("Class could not be found. The class we've searched for is: " . $class);
        }

        if (!in_array(Model::class, class_parents($class))) {
            throw new Exception("Unable to process class, the class is not a Eloquent model.");
        }

        $model = new $class;

        $columns = $this->getColumns($model);

        foreach ($columns as $columnName => $column) {
            if (strrpos($columnName, '_id') !== false) {
                $this->parent->info($columnName);

                $fn = Str::camel(str_replace('_id', '', $columnName));

                $this->parent->info('fn name: ' . $fn);

                if (method_exists($model, $fn)) {
                    $related = $model->{$fn}();
                    if ($related instanceof Relation) {
                        $relationType = explode("\\", get_class($related));
                        $relationType = Str::camel(end($relationType));

                        $relatedModel = $related->getModel();
                        $relatedModel = str_replace(config('json-schema.models.namespace'), "", get_class($relatedModel));

                        dd($relatedModel, $relationType);
                    }
                }
            }
        }

        $relations = $model->getRelations();

        // dd($relations);

        return [];
    }

    /**
     * @param Model $model
     *
     * @return array
     */
    private function getColumns(Model $model): array
    {
        $columnData = [];
        $columns = $this->getTableColumns($model);

        foreach ($columns as $column) {
            $columnData[$column] = $this->getColumnDefinition($model, $column);
        }

        return $columnData;
    }
//
//    public function getRelations(Model $model)
//    {
//        $reflector = new ReflectionClass($model);
//        $relations = [];
//        foreach ($reflector->getMethods() as $reflectionMethod) {
//            $returnType = $reflectionMethod->getReturnType();
//            if ($returnType) {
//                if (in_array(
//                    class_basename($returnType->getName()), [
//                    'HasOne',
//                    'HasMany',
//                    'BelongsTo',
//                    'BelongsToMany',
//                    'MorphToMany',
//                    'MorphTo',
//                ]
//                )) {
//                    $relations[] = $reflectionMethod;
//                }
//            }
//        }
//
//        dd($relations);
//    }

    /**
     * @param Model $model
     *
     * @return mixed
     */
    public function getTableColumns(Model $model)
    {
        return $model->getConnection()->getSchemaBuilder()->getColumnListing($model->getTable());
    }

    /**
     * @param Model  $model
     * @param string $column
     *
     * @return mixed
     */
    private function getColumnDefinition(Model $model, string $column)
    {
        $data = $model->getConnection()->getDoctrineColumn($model->getTable(), $column)->toArray();

        $data['type'] = $this->getColumnType($model, $column);

        return $data;
    }

    /**
     * @param Model  $model
     * @param string $column
     *
     * @return mixed|array
     */
    public function getColumnType(Model $model, string $column)
    {
        return $model->getConnection()->getSchemaBuilder()->getColumnType($model->getTable(), $column);
    }


}
