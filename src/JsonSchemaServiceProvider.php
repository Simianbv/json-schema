<?php
/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema;

use Illuminate\Support\ServiceProvider;
use Simianbv\JsonSchema\Console\Commands\MakeResource;

/**
 * @class   JsonSchemaServiceProvider
 * @package Simianbv\JsonSchema
 */
class JsonSchemaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/api.php');

        $this->publishes(
            [
                __DIR__ . '/json-schema.php' => config_path('json-schema.php'),
            ], "json-schema"
        );
        $this->publishes(
            [
                __DIR__ . '/resources/stubs/model.stub' => resource_path('stubs/model.stub'),
                __DIR__ . '/resources/stubs/model-relation.stub' => resource_path('stubs/model-relation.stub'),
                __DIR__ . '/resources/stubs/model-soft-delete.stub' => resource_path('stubs/model-soft-delete.stub'),
                __DIR__ . '/resources/stubs/controller.stub' => resource_path('stubs/controller.stub'),
                __DIR__ . '/resources/stubs/controller-has-many.stub' => resource_path('stubs/controller-has-many.stub'),
                __DIR__ . '/resources/stubs/controller-has-one.stub' => resource_path('stubs/controller-has-one.stub'),
                __DIR__ . '/resources/stubs/migration.stub' => resource_path('stubs/migration.stub'),
                __DIR__ . '/resources/stubs/resource.stub' => resource_path('stubs/resource.stub'),

            ], "json-schema-stubs"
        );


        if ($this->app->runningInConsole()) {
            $this->commands(
                [
                    MakeResource::class,
                    //MakeSchemaFromModel::class
                ]
            );
        }
    }
}
