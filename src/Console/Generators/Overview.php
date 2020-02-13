<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Console\Generators;

use Illuminate\Support\Str;

/**
 * @class   Overview
 * @package Simianbv\JsonSchema\Console\Generators
 */
class Overview extends Base
{

    protected $stubLocation = '';
    protected $stubDetailLocation = '';
    protected $stubColumnLocation = '';

    /**
     * @var string
     */
    private $stub;
    /**
     * @var string
     */
    private $columnStub;
    /**
     * @var string
     */
    private $detailStub;

    /**
     * Overview constructor.
     */
    public function __construct()
    {
        $this->stubLocation = config('json-schema.stubs.overview');
        $this->stubDetailLocation = config('json-schema.stubs.overview-detail');
        $this->stubColumnLocation = config('json-schema.stubs.overview-column');
    }

    /**
     * @param $resource
     * @param $namespace
     * @param $model
     *
     * @return array
     */
    public function create($resource, $namespace, $model)
    {
        $this->resource = $resource;
        $this->stub = file_get_contents($this->stubLocation);
        $this->detailStub = file_get_contents($this->stubDetailLocation);
        $this->columnStub = file_get_contents($this->stubColumnLocation);

        $columns = $this->generateColumns($resource, $namespace, $model);

        $url = ($namespace != '' ? Str::slug(strtolower($namespace)) . '/' : '') . Str::slug(Str::snake(Str::plural($model))) . '/';
        $fields = [
            'ModelSingle' => $model,
            'ModelUrl' => $url,
            'ModelPlural' => Str::plural($model),
            'ModelLink' => Str::slug(($namespace != '' ? $namespace . '-' : '') . Str::snake($model)),
            'Acl' => strtolower($namespace) . '.' . Str::slug(Str::snake($model)),
            'ModelNamespace' => $this->ns($namespace) . $model,
            'Columns' => "\n" . implode("", $columns),
        ];

        $overviewFile = ucfirst(Str::slug(Str::snake(Str::plural($model))) . "-overview");
        $detailFile = ucfirst(Str::slug(Str::snake($model)) . "-detail");

        $overviewComponent = ucfirst(strtolower($namespace) . Str::plural($model)) . "Overview";
        $detailComponent = ucfirst(strtolower($namespace) . $model) . "Detail";

        $prefix = ($namespace != '' ? strtolower($namespace) . '/' : '');
        $namePrefix = ($namespace != '' ? strtolower($namespace) . '-' : '');

        $this->parent->addFrontendImport('const ' . $overviewComponent . ' = () => import(/* webpackChunkName: "' . strtolower($namespace) . '" */ "./views/' . strtolower($namespace) . '/' . $overviewFile . '.vue")');
        $this->parent->addFrontendImport('const ' . $detailComponent . ' = () => import(/* webpackChunkName: "' . strtolower($namespace) . '" */ "./views/' . strtolower($namespace) . '/' . $detailFile . '.vue")');
        $this->parent->addFrontendRoute("{path: '/" . $prefix . Str::slug(Str::snake(Str::plural($model))) . "', name: '" . $namePrefix . strtolower($overviewFile) . "', component: " . $overviewComponent . ", props: { default: false}}, ");
        $this->parent->addFrontendRoute("{path: '/" . $prefix . Str::slug(Str::snake(Str::plural($model))) . "/:id', name: '" . $namePrefix . strtolower($detailFile) . "', component: " . $detailComponent . ", props: true}, ");

        $overviewContent = $this->fillStub($fields, $this->stub, ['{%', '%}']);
        $detailContent = $this->filLStub(
            [
                'Model' => $model,
                'Url' => $url,
            ], $this->detailStub, ['{%', '%}']
        );

        return ['overview' => $overviewContent, 'detail' => $detailContent];
    }

    /**
     * @param $resource
     * @param $namespace
     * @param $model
     *
     * @return array
     */
    private function generateColumns($resource, $namespace, $model)
    {
        $columns = [];
        $sortableTypes = ['string', 'integer', 'bigInteger', 'float', 'increments', 'bigIncrements', 'boolean', 'bool'];

        if (isset($resource['columns'])) {
            foreach ($resource['columns'] as $column => $attr) {
                if (!Str::endsWith($column, ['_id'])) {
                    if ($attr['type'] == 'bool' || $attr['type'] == 'boolean' || $attr['type'] == 'smallInt') {
                        $content = '<i class="far fa-check-circle" v-if="props.row.' . $column . '"></i>';
                    } else {
                        $content = '{{ props.row.' . $column . ' }}';
                    }

                    $fields = [
                        'Field' => $column,
                        'Label' => __(ucfirst(implode(' ', explode('_', $column)))),
                        'Sortable' => $column == 'id' || in_array($attr['type'], $sortableTypes) ? 'sortable' : '',
                        'Content' => $content,
                    ];
                    $columns[] = $this->fillStub($fields, $this->columnStub, ['{%', '%}']);
                }
            }
        }
        return $columns;
    }
}

